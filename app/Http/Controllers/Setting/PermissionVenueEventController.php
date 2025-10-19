<?php

namespace App\Http\Controllers\Setting;

use App\Http\Controllers\Controller;
use App\Models\Setting\PermVenueEvent;
use App\Models\Setting\Venue;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Intervention\Image\Laravel\Facades\Image;


class PermissionVenueEventController extends Controller
{
    // Display all PermissionVenueEvents
    public function index()
    {
        $perm_venue_events = PermVenueEvent::all();
        $venues = Venue::all();
        $users = User::all(); // Fetch all users

        return view('setting.permission.venue_event.list', compact('perm_venue_events', 'venues', 'users'));
    }

    // Get a single PermVenueEvent with its venues
    public function get($id)
    {
        $op = PermVenueEvent::with('venues')->findOrFail($id);
        return response()->json(['op' => $op, 'venues' => $op->venues]);
    }

    // List PermVenueEvents for DataTables or API
    public function list()
    {
        $search = request('search');
        $sort = request('sort', 'id');
        $order = request('order', 'DESC');

        $ops = PermVenueEvent::with('venues')->orderBy($sort, $order);

        if ($search) {
            $ops->where(function ($query) use ($search) {
                $query->where('title', 'like', "%$search%")
                    ->orWhere('id', 'like', "%$search%");
            });
        }

        $total = $ops->count();

        $ops = $ops->paginate(request('limit', 10))->through(function ($op) {
            $venues_display = '';
            foreach ($op->venues as $venue) {
                $venues_display .= '<span class="badge badge-pill bg-body-tertiary">' . $venue->short_name . '</span> ';
            }

            $actions =
                '<div class="font-sans-serif btn-reveal-trigger position-static">' .
                '<a href="javascript:void(0)" class="btn btn-sm" id="editPermVenueEvents" data-id="' . $op->id . '" data-table="perm_venue_event_table" title="Update">' .
                '<i class="fa-solid fa-pen-to-square text-primary"></i></a>' .
                '<a href="javascript:void(0)" class="btn btn-sm" id="deleteEvent" data-id="' . $op->id . '" data-table="perm_venue_event_table" title="Delete">' .
                '<i class="fa-solid fa-trash text-danger"></i></a></div>';

            return [
                'id' => $op->id,
                'title' => '<div class="align-middle fs-9 ps-3">' . $op->title . '</div>',
                'venues' => $venues_display,
                'actions' => $actions,
                'created_at' => $op->created_at->format('Y-m-d H:i:s'),
                'updated_at' => $op->updated_at->format('Y-m-d H:i:s'),
            ];
        });

        return response()->json([
            'rows' => $ops->items(),
            'total' => $total,
        ]);
    }

    // Store new PermVenueEvent
    public function store(Request $request)
    {
        $rules = [
            'title' => 'required',
            'venue_id' => 'required|array',
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json([
                'error' => true,
                'message' => implode($validator->errors()->all('<div>:message</div>')),
            ]);
        }

        $op = new PermVenueEvent();
        $op->title = $request->title;
        $op->user_id = $request->user_id; // Save user

        // Handle image upload
        if ($request->hasFile('file_name')) {
            $file = $request->file('file_name');
            $fileNameToStore = rand() . date('ymdHis') . $file->getClientOriginalName();
            $destinationPath = public_path('storage/event/logo/');
            $image = Image::read($request->file('file_name'));
            $image->save($destinationPath . $fileNameToStore);
            $op->event_logo = $fileNameToStore;
        } else {
            $op->event_logo = 'noimage.jpg';
        }

        $op->save();

        // Attach venues
        foreach ($request->venue_id as $key => $data) {
            $op->venues()->attach($request->venue_id[$key]);
        }



        return response()->json([
            'error' => false,
            'message' => 'PermVenueEvent created successfully',
        ]);
    }

    // Update existing PermVenueEvent
    public function update(Request $request)
    {
        $rules = [
            'id' => 'required|exists:perm_venue_events,id',
            'title' => 'required',
            'venue_id' => 'required|array',
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json([
                'error' => true,
                'message' => implode($validator->errors()->all('<div>:message</div>')),
            ]);
        }

        $op = PermVenueEvent::findOrFail($request->id);
        $op->title = $request->title;

        if ($request->hasFile('file_name')) {
            // Delete old image if exists
            if ($op->event_logo && Storage::disk('public')->exists('event/logo/' . $op->event_logo)) {
                Storage::disk('public')->delete('event/logo/' . $op->event_logo);
            }

            $file = $request->file('file_name');
            $fileNameToStore = rand() . date('ymdHis') . $file->getClientOriginalName();
            $destinationPath = public_path('storage/event/logo/');
            $image = Image::make($file)->resize(150, 150);
            $image->save($destinationPath . $fileNameToStore);
            $op->event_logo = $fileNameToStore;
        }

        $op->save();

        // Sync venues
        $op->venues()->sync($request->venue_id);

        return response()->json([
            'error' => false,
            'message' => 'PermVenueEvent updated successfully',
        ]);
    }

    // Delete a PermVenueEvent
    public function delete($id)
    {
        $op = PermVenueEvent::findOrFail($id);

        if ($op->event_logo && Storage::disk('public')->exists('event/logo/' . $op->event_logo)) {
            Storage::disk('public')->delete('event/logo/' . $op->event_logo);
        }

        $op->venues()->detach();
        $op->delete();

        return response()->json([
            'error' => false,
            'message' => 'PermVenueEvent deleted successfully',
        ]);
    }
}
