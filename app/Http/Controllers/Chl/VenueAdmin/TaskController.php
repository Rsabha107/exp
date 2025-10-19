<?php

namespace App\Http\Controllers\Chl\VenueAdmin;

use App\Http\Controllers\Controller;
use App\Mail\DailyChecklistPdfMail;
use App\Models\Category;
use App\Models\LeadCategory;
use App\Models\LeadComment;
use App\Models\LeadTask;
use App\Models\Setting\Event;
use App\Models\Setting\Venue;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;

class TaskController extends Controller
{
    /**
     * Display all tasks.
     */
    // public function index()
    // {
    //     Log::info('Fetching all tasks');

    //     $venueId = auth()->user()->venue_id;
    //     $categories = LeadTask::select('category')->where('venue_id', 5)->distinct()->pluck('category');

    //     $tasks = LeadTask::where('venue_id', 5)->orderBy('created_at', 'desc')->get();
    //     $venues = Venue::orderBy('title')->get(); // fetch all venues
    //     return view('chl.venue-admin.tasks.partials.index', compact('tasks', 'venues', 'categories'));
    // }

    // public function index()
    // {
    //     $venueId = session('VENUE_ID');
    //     $eventId = session('EVENT_ID');

    //     $categories = LeadCategory::where('event_id', $eventId)->get();
    //     // dd($categories);
    //     $users = User::whereHas('events', function ($q) use ($eventId) {
    //         $q->where('event_id', $eventId);
    //     })->get();

    //     $loggedInEmail = auth()->user()->email;
    //     $currentUser = $users->firstWhere('email', $loggedInEmail);
    //     $currentUser = $currentUser ?? auth()->user();
    //     $tasks = LeadTask::where('venue_id', $venueId)
    //         ->where('event_id', $eventId)
    //         ->orderBy('created_at', 'desc')
    //         ->get()
    //         ->groupBy('category');

    //     // Fetch current venue & event names
    //     $currentVenue = Venue::find($venueId);
    //     $currentEvent = Event::find($eventId);

    //     return view('chl.venue-admin.tasks.partials.index', compact(
    //         'categories',
    //         'tasks',
    //         'currentVenue',
    //         'currentEvent',
    //         'currentUser'
    //     ));
    // }

    public function index()
    {
        $venueId = session('VENUE_ID');
        $eventId = session('EVENT_ID');

        $user = auth()->user();
        appLog('User accessing tasks', ['user_id' => $user->id, 'event_id' => $eventId, 'venue_id' => $venueId]);

        $isAssignedToEvent = $user->events()->where('event_id', $eventId)->exists();
        $isAssignedToVenue = $user->venues()->where('venue_id', $venueId)->exists();

        // ✅ If user not assigned to either, show message page
        if (! $isAssignedToEvent || ! $isAssignedToVenue) {
            return view('chl.venue-admin.tasks.partials.error_page', [
                'message' => 'You are not assigned to this event or venue. Please contact the administrator.'
            ]);
        }

        // ✅ If assigned → continue as normal
        $categories = LeadCategory::where('event_id', $eventId)->where('venue_id', $venueId)->get();

        $users = User::whereHas('events', function ($q) use ($eventId) {
            $q->where('event_id', $eventId);
        })->get();

        $loggedInEmail = $user->email;
        $currentUser = $users->firstWhere('email', $loggedInEmail) ?? $user;

        $tasks = LeadTask::where('venue_id', $venueId)
            ->where('event_id', $eventId)
            ->orderBy('created_at', 'desc')
            ->get()
            ->groupBy('category');

        $currentVenue = Venue::find($venueId);
        $currentEvent = Event::find($eventId);

        return view('chl.venue-admin.tasks.index', compact(
            'categories',
            'tasks',
            'currentVenue',
            'currentEvent',
            'currentUser'
        ));
    }

    public function toggle($id)
    {
        $task = LeadTask::findOrFail($id);
        $task->completed_flag = !$task->completed_flag;
        $task->save();

        $venueId = session('VENUE_ID');
        $eventId = session('EVENT_ID');

        $categories = LeadCategory::where('event_id', $eventId)->where('venue_id', $venueId)
            ->with(['leadTasks' => function ($q) use ($venueId, $eventId) {
                $q->where('venue_id', $venueId)
                    ->where('event_id', $eventId)
                    ->orderBy('created_at', 'desc');
            }])
            ->orderBy('created_at', 'desc')
            ->get();
        $currentVenue = Venue::find($venueId);
        $currentEvent = Event::find($eventId);

        // Fetch current user (same logic as in index)
        $users = User::whereHas('events', function ($q) use ($eventId) {
            $q->where('event_id', $eventId);
        })->get();

        $loggedInEmail = auth()->user()->email;
        $currentUser = $users->firstWhere('email', $loggedInEmail) ?? auth()->user();
        return view('chl.venue-admin.tasks.list', compact('categories',  'currentUser', 'currentVenue', 'currentEvent'));
    }

    public function exportPdf()
    {
        // Get current venue and event from session
        $venueId = session('VENUE_ID');
        $eventId = session('EVENT_ID');
        Log::info('VENUE_ID: ' . $venueId);
        Log::info('EVENT_ID: ' . $eventId);

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

        // Load PDF view
        $pdf = Pdf::loadView('chl.venue-admin.tasks.partials.pdf', [
            'categories'   => $categories,
            'currentVenue' => $currentVenue,
            'currentEvent' => $currentEvent,
            'currentUser'  => $currentUser,
        ]);

        // Save PDF to storage
        $timestamp = now()->format('Ymd_His');
        $fileName = "exp_{$currentEvent->name}-{$currentVenue->short_name}-{$timestamp}.pdf";
        $filePath = 'pdf-exports/' . $fileName;
        Storage::disk('private')->makeDirectory('pdf-exports');
        // Storage::disk('private')->put('pdf-exports/' . $fileName, $pdf->output());
        Storage::disk('private')->put($filePath, $pdf->output());

        Mail::to(config('settings.admin_email')) 
            ->send(new DailyChecklistPdfMail($filePath));

        Mail::to($currentUser->email)
        ->send(new DailyChecklistPdfMail($filePath));


        session()->flash('pdf_generated', $fileName);
        return $pdf->stream('tasks.pdf');
    }


    public function switch($id)
    {
        if ($id) {
            if (Event::findOrFail($id)) {
                Log::info('Event ID: ' . $id);

                session()->put('EVENT_ID', $id);
                Log::info('Event ID: ' . session()->get('EVENT_ID'));
                return redirect()->route('chl.venue.admin.tasks.index')->with('message', 'Event Switched.');
            } else {
                // return back()->with('error', 'Workspace not found.');
                return back()->with('error', 'Event not found.');
            }
        } else {
            session()->forget('EVENT_ID');
            return back()->withInput();
        }
    }

    public function venueSwitch($id)
    {
        if ($id) {
            if (Venue::findOrFail($id)) {
                Log::info('Venue ID: ' . $id);

                session()->put('VENUE_ID', $id);
                Log::info('Venue ID: ' . session()->get('VENUE_ID'));
                return redirect()->route('chl.venue.admin.tasks.index')->with('message', 'Venue Switched.');
            } else {
                // return back()->with('error', 'Workspace not found.');
                return back()->with('error', 'Venue not found.');
            }
        } else {
            session()->forget('VENUE_ID');
            return back()->withInput();
        }
    }

    public function pickEvent(Request $request)
    {
        // $events = MdsEvent::all();
        // $this->switch($request->event_id);
        // return view('mds.admin.booking.pick', compact('events'));
        if ($request->event_id && $request->venue_id) {
            if (Event::findOrFail($request->event_id) && !session()->has('EVENT_ID')) {
                session()->put('EVENT_ID', $request->event_id);
            }

            if (Venue::findOrFail($request->venue_id) && !session()->has('VENUE_ID')) {
                session()->put('VENUE_ID', $request->venue_id);
            }
            return redirect()->route('chl.venue.admin.tasks.index')->with('message', 'Event/Venue Switched.');
        }
        //  else {
        // return back()->with('error', 'Workspace not found.');
        Log::info('event_id is null');
        return redirect()->route('chl.venue.admin')->with('error', 'Event not found.');
        // }
    }

    public function saveComment(Request $request)
    {
        $venueId = session('VENUE_ID');
        $eventId = session('EVENT_ID');

        if (!$venueId || !$eventId) {
            return response()->json([
                'error' => true,
                'message' => 'Venue or Event session is missing.'
            ]);
        }

        $request->validate([
            'category_id' => 'required|exists:lead_categories,id',
            'comment' => 'required|string|max:1000',
        ]);

        $comment = LeadComment::create([
            'venue_id'    => $venueId,
            'event_id'    => $eventId,
            'category_id' => $request->category_id,
            'comment'     => $request->comment,
        ]);

        Log::info('Comment created:', $comment->toArray());
        return response()->json([
            'error' => false,
            'message' => 'Comment added successfully!',
            'comment' => $comment
        ]);
    }


    public function deleteComment(Request $request)
    {
        $comment = LeadComment::find($request->id);
        if ($comment) {
            $comment->delete();
            return response()->json(['success' => true]);
        }
        return response()->json(['success' => false]);
    }
}
