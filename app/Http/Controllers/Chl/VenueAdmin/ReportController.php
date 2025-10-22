<?php

namespace App\Http\Controllers\Chl\VenueAdmin;

use App\Http\Controllers\Controller;
use App\Models\Chl\LeadCategory;
use App\Models\Chl\LeadReporting;
use App\Models\Chl\LeadTask;
use App\Models\Setting\Event;
use App\Models\Setting\Venue;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Log;

class ReportController extends Controller
{
    public function index()
    {
        Log::info('ReportController@index called');

        $reports = LeadReporting::select('event_id', 'venue_id', 'status', 'created_at', 'updated_at')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('chl.venue-admin.report.list', compact('reports'));
    }

    public function list()
    {
        $search = request('search');
        $sort = request('sort', 'created_at');
        $order = strtolower(request('order', 'desc')) === 'asc' ? 'asc' : 'desc';

        Log::info('venue id from session: ' . session('VENUE_ID'));
        Log::info('event id from session: ' . session('EVENT_ID'));
        $query = LeadReporting::with(['event', 'venue'])->where('venue_id', session('VENUE_ID'))->where('event_id', session('EVENT_ID'));

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->whereHas('event', function ($qe) use ($search) {
                    $qe->where('title', 'like', "%$search%");
                })
                    ->orWhereHas('venue', function ($qe) use ($search) {
                        $qe->where('title', 'like', "%$search%");
                    })
                    ->orWhere('status', 'like', "%$search%");
            });
            Log::info('Search applied: ' . $search);
        }

        $query->orderBy($sort, $order);
        // Log::info('Sorting applied: ' . $sort . ' ' . $order);

        $total = $query->count();
        $reports = $query->paginate(request('limit', 10))->through(function ($report) {

            $div_action = '<div class="font-sans-serif btn-reveal-trigger position-static">';

            // Show PDF Report
            $actions_pdf = '<a href="' . route('chl.venue.admin.tasks.show.pdf', Crypt::encrypt($report->id)) . '" target="_blank" class="btn p-1"'
                . ' title="Show PDF Report">'
                . '<i class="fas fa-passport text-success"></i></a>';
            $actions = $div_action . $actions_pdf . '</div>';

            if ($report->status != 'submitted') {
                $show_exp = '<a href="' . route('chl.venue.admin.tasks.show.exp', Crypt::encrypt($report->id)) . '" target="_blank" class="btn p-1"'
                    . ' title="Show EXP Report">'
                    . format_date($report->reporting_date) . '</a>';
            } else {
                $show_exp = format_date($report->reporting_date);
            }
            return [
                'id'         => $report->id,
                'reporting_date'   => '<div class="align-middle white-space-wrap fs-9 ps-2">' . $show_exp . '</div>',
                'event_id'   => '<div class="align-middle white-space-wrap fs-9 ps-2">' . $report->event->name . '</div>',
                'venue_id'   => '<div class="align-middle white-space-wrap fs-9 ps-2">' . $report->venue->title . '</div>',
                'status'     => '<div class="text-uppercase fs-9 fw-bold" style="color:' .
                    ($report->status === 'submitted' ? 'green' : 'orange') . ';">' .
                    $report->status . '</div>',
                'actions' => $actions,
                'created_at' => $report->created_at->format('Y-m-d H:i:s'),
                'updated_at' => $report->updated_at->format('Y-m-d H:i:s'),
            ];
        });
        Log::info('Prepared JSON response with ' . count($reports->items()) . ' rows');

        return response()->json([
            'rows'  => $reports->items(),
            'total' => $total,
        ]);
    }

    public function showExp($id)
    {
        $venueId = session('VENUE_ID');
        $eventId = session('EVENT_ID');

        $decryptedId = Crypt::decrypt($id);

        $user = auth()->user();
        appLog('User accessing tasks', ['user_id' => $user->id, 'event_id' => $eventId, 'venue_id' => $venueId]);

        $lead_report = LeadReporting::where('id', $decryptedId)->first();

        // ✅ Check if user is assigned to event & venue
        $isAssignedToEvent = $user->events()->where('event_id', $eventId)->exists();
        $isAssignedToVenue = $user->venues()->where('venue_id', $venueId)->exists();

        // ✅ If user not assigned to either, show message page
        if (! $isAssignedToEvent || ! $isAssignedToVenue) {
            return view('chl.venue-admin.tasks.partials.error_page', [
                'message' => 'You are not assigned to this event or venue. Please contact the administrator.'
            ]);
        }

        // ✅ If assigned → continue as normal
        $categories = LeadCategory::where('reporting_id', $decryptedId)->get();

        $users = User::whereHas('events', function ($q) use ($eventId) {
            $q->where('event_id', $eventId);
        })->get();

        $loggedInEmail = $user->email;
        $currentUser = $users->firstWhere('email', $loggedInEmail) ?? $user;

        $tasks = LeadTask::where('reporting_id', $decryptedId)
            ->orderBy('id', 'asc')
            ->get()
            ->groupBy('category');



        $currentVenue = Venue::find($venueId);
        $currentEvent = Event::find($eventId);

        return view('chl.venue-admin.tasks.index', compact(
            'categories',
            'tasks',
            'currentVenue',
            'currentEvent',
            'currentUser',
            'lead_report'
        ));
    }

    public function showPdf($id)
    {
        // Get current venue and event from session
        $decryptedId = Crypt::decrypt($id);
        $report = LeadReporting::findOrFail($decryptedId);
        $eventId = $report->event_id;
        $venueId = $report->venue_id;

        // All users for this event & venue
        $users = User::whereHas('events', function ($q) use ($eventId) {
            $q->where('event_id', $eventId);
        })->get();

        $loggedInEmail = auth()->user()->email;
        $currentUser = $users->firstWhere('email', $loggedInEmail);
        $currentUser = $currentUser ?? auth()->user();
        $categories = LeadCategory::where('event_id', $eventId)->where('venue_id', $venueId)
            ->with(['leadTasks' => function ($q) use ($venueId, $eventId) {
                $q->where('venue_id', $venueId)
                    ->where('event_id', $eventId)
                    ->orderBy('created_at', 'desc');
            }])
            ->get();

        // Fetch current venue & event for PDF display
        $currentVenue = Venue::find($venueId);
        $currentEvent = Event::find($eventId);

        // return redirect()->route('chl.venue.admin.tasks.report');
        // Load PDF view
        $pdf = Pdf::loadView('chl.venue-admin.tasks.partials.pdf', [
            'categories'   => $categories,
            'currentVenue' => $currentVenue,
            'currentEvent' => $currentEvent,
            'currentUser'  => $currentUser,
        ]);

        return $pdf->stream();
    }
}
