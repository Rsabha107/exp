<?php

namespace App\Http\Controllers\Sps\Admin;

use App\Http\Controllers\Controller;
use App\Mail\QrCodeMail;
use App\Models\Setting\Location;
use App\Models\Setting\Event;
use App\Models\Sps\ItemStatus;
use App\Models\Sps\Profile;
use App\Models\Sps\ProhibitedItem;
use App\Models\Sps\StoredItem;
use App\Models\Setting\Venue;
use App\Models\Sps\ProfileStatus;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Support\Str;
use Intervention\Image\Laravel\Facades\Image;

class StorageControllerff extends Controller
{
    public function index()
    {

        $projibitedItems = ProhibitedItem::all();
        $item_statuses = ItemStatus::all();
        $venues = Venue::all();
        $events = Event::all();
        $locations = Location::all();

        return view('sps.admin.list', [
            'prohibitedItems' => $projibitedItems,
            'item_statuses' => $item_statuses,
            'venues' => $venues,
            'events' => $events,
            'locations' => $locations,
        ]);
    }

    public function list()
    {
        Log::info('inside Admin ProfileController::list');
        Log::info(request()->all());
        // $venues = auth()->user()->venues?->pluck('id')->toArray();

        $search = request('search');
        $sort = (request('sort')) ? request('sort') : "id";
        $order = (request('order')) ? request('order') : "DESC";
        $mds_schedule_event_filter = (request()->mds_schedule_event_filter) ? request()->mds_schedule_event_filter : "";
        $mds_schedule_venue_filter = (request()->mds_schedule_venue_filter) ? request()->mds_schedule_venue_filter : "";
        $mds_schedule_rsp_filter = (request()->mds_schedule_rsp_filter) ? request()->mds_schedule_rsp_filter : "";
        $mds_date_range_filter = (request()->mds_date_range_filter) ? request()->mds_date_range_filter : "";

        // if ($mds_date_range_filter == "") {
        //     $mds_date_range_filter = date('Y-m-d') . ' to ' . date('Y-m-d');
        // }

        // Carbon::createFromFormat('d/m/Y', $request->slot_visibility)->toDateString()

        $ops = Profile::orderBy($sort, $order);
        // $ops = $ops->where('event_id', session('EVENT_ID'));
        // $ops = $ops->where('venue_id', session('VENUE_ID'));
        // $ops = $ops->where('storage_type_id', auth()->user()->storageType?->id);


        if ($search) {
            $ops = $ops->where(function ($query) use ($search) {
                $query
                    ->where('ref_number', 'like', '%' . $search . '%')
                    ->orWhere('first_name', 'like', '%' . $search . '%')
                    ->orWhere('last_name', 'like', '%' . $search . '%')
                    ->orWhere('email_address', 'like', '%' . $search . '%')
                    ->orWhere('phone', 'like', '%' . $search . '%');
            });
        }


        if ($mds_schedule_event_filter) {
            $ops = $ops->where('event_id', $mds_schedule_event_filter);
        }

        if ($mds_schedule_venue_filter) {
            $ops = $ops->where('venue_id', $mds_schedule_venue_filter);
        }

        if ($mds_schedule_rsp_filter) {
            $ops = $ops->where('rsp_id', $mds_schedule_rsp_filter);
        }

        if ($mds_date_range_filter) {
            $dates = explode('to', $mds_date_range_filter);
            $startDate = trim($dates[0]);
            if (count($dates) > 1) {
                $endDate = trim($dates[1]);
            } else {
                $endDate = null;
            }
            if ($startDate) {
                $startDate = Carbon::createFromFormat('d/m/y', $startDate)->toDateString();
            }
            if ($endDate) {
                $endDate = Carbon::createFromFormat('d/m/y', $endDate)->toDateString();
            }

            if ($startDate && $endDate) {
                $ops = $ops->whereBetween('booking_date', [$startDate, $endDate]);
            } else if ($startDate) {
                $ops = $ops->where('booking_date', '>=', $startDate);
            } else if ($endDate) {
                $ops = $ops->where('booking_date', '<=', $endDate);
            }
        }

        $total = $ops->count();
        $ops = $ops->paginate(request("limit"))->through(function ($op) {

            // $location = Location::find($booking->location_id);

            if ($op->items()->count() > 0) {
                $items = '<div class="align-middle white-space-wrap fs-9 ps-2">
                            <a href="javascript:void(0)" id="ItemDetails" data-id="' .
                    $op->id .
                    '" data-table="storage_table" data-bs-toggle="tooltip" data-bs-placement="right">
                            <span class="fa-number-circle">' . $op->items()->count() . '</span></a>
                          </div>';
            } else {
                $items = '<div class="align-middle white-space-wrap fs-9 ps-2"><span class="fa-number-circle-zero">0</span></div>';
            }

            $actions_head = '<div class="font-sans-serif btn-reveal-trigger position-static">';
            $add_action = '<a href="#" class="btn btn-sm" id="addItem" data-id="' . $op->id . '" data-table="storage_table" data-bs-toggle="tooltip" data-bs-placement="right" title="Add Items">
                            <i class="fa-solid fa-plus text-primary"></i></a>';
            $delete_action = '<a href="javascript:void(0)" class="btn btn-sm" data-table="storage_table" data-id="' . $op->id . '" id="deleteVisitorInformatione" data-bs-toggle="tooltip" data-bs-placement="right" title="Delete">
                            <i class="bx bx-trash text-danger"></i></a>';
            $actions_end = '</div>';

            $actions = $actions_head .
                // $add_action .
                $delete_action .
                $actions_end;

            // $actions =
            //     '<div class="font-sans-serif btn-reveal-trigger position-static">' .
            //     '<a href="#" class="btn btn-sm" id="addItem" data-id="' .
            //     $op->id .
            //     '" data-table="storage_table" data-bs-toggle="tooltip" data-bs-placement="right" title="Add Items">' .
            //     '<i class="fa-solid fa-plus text-primary"></i></a>' .
            //     '<a href="javascript:void(0)" class="btn btn-sm" data-table="storage_table" data-id="' .
            //     $op->id .
            //     '" id="deleteVisitorInformatione" data-bs-toggle="tooltip" data-bs-placement="right" title="Delete">' .
            //     '<i class="bx bx-trash text-danger"></i></a></div></div>';

            return  [
                'id' => $op->id,
                // 'id' => '<div class="align-middle white-space-wrap fw-bold fs-8 ps-2">' .$op->id. '</div>',
                'first_name' => '<div class="align-middle white-space-wrap fs-9 ps-2">' . $op->first_name . '</div>',
                'last_name' => '<div class="align-middle white-space-wrap fs-9 ps-2">' . $op->last_name . '</div>',
                'phone' => '<div class="align-middle white-space-wrap fs-9 ps-2">' . $op->phone . '</div>',
                'email_address' => '<div class="align-middle white-space-wrap fs-9 ps-2">' . $op->email_address . '</div>',
                'ref_number' => '<div class="align-middle white-space-wrap fs-9 ps-2">' . $op->ref_number . '</div>',
                'status' => '<span class="badge badge-phoenix fs--2 align-middle white-space-wrap ms-1 badge-phoenix-' . $op->status?->color . ' " style="cursor: default;"><span class="badge-label">' . $op->status?->title . '</span></span>',
                // 'status' => '<span class="badge badge-phoenix fs--2 align-middle white-space-wrap ms-1 badge-phoenix-' . $op->status?->color . ' " style="cursor: pointer;" id="editStatus" data-id="' . $op->id . '" data-table="storage_table"><span class="badge-label">' . $op->status?->title . '</span></span>',
                'items' => $items,
                'action' => $actions,
                'event' => $op->event?->name,
                'venue' => $op->venue?->title,
                'location' => $op->location?->title,
                'created_at' => format_date($op->created_at,  'H:i:s'),
                'updated_at' => format_date($op->updated_at, 'H:i:s'),
            ];
        });

        return response()->json([
            "rows" => $ops->items(),
            "total" => $total,
        ]);
    }

    public function create()
    {
        // return view('sps.admin.create');
        $prohibitedItems = ProhibitedItem::all();
        $item_statuses = ItemStatus::all();
        return view('sps.admin.create', ['prohibitedItems' => $prohibitedItems, 'item_statuses' => $item_statuses]);
    }

    public function store(Request $request)
    {
        // 1. Validate incoming request
        $validator = Validator::make($request->all(), [
            'venue_id' => 'required|exists:venues,id',
            'location_id' => 'required|exists:locations,id',
            'storage_type_id' => 'required|exists:storage_types,id',
            'event_id' => 'required|exists:events,id',
            'first_name' => 'required|string|max:50',
            'last_name'  => 'required|string|max:50',
            'phone'      => 'required|string|max:20',
            'email_address' => 'required|email|max:100',
            'venue_id' => 'required',
            'event_id' => 'required',
            'location_id' => 'required',
            // 'file_name' => 'nullable|file|mimes:jpg,jpeg,png|max:2048', // 2MB max
        ]);

        if ($validator->fails()) {
            $error = true;
            $message = implode($validator->errors()->all('<div>:message</div>'));  // use this for json/jquery
            return response()->json([
                'error' => $error,
                'message' => $message,
            ]);
        }

        // 3. Create profile

        $submitted = getStatusIdByLabel('submitted');
        $checked_in = getStatusIdByLabel('checked-in');
        $returned = getStatusIdByLabel('returned');

        $op = new Profile();

        $op->first_name = $request->first_name;
        $op->last_name = $request->last_name;
        $op->phone = $request->phone;
        $op->item_status_id = $submitted; // Set the status to 'Submitted'
        $op->storage_type_id = $request->storage_type_id;
        $op->email_address = $request->email_address;
        $op->event_id = $request->event_id;
        $op->venue_id = $request->venue_id;
        $op->location_id = $request->location_id; // Store as JSON if multiple locations

        $op->save();

        // 4. Handle items
        // $items = new StoredItem();

        Log::info('Prohibited Item IDs: ' . json_encode($request->prohibited_item_id));
        Log::info('Count Prohibited Item IDs: ' . count($request->prohibited_item_id));

        foreach ($request->prohibited_item_id as $key => $item) {
            Log::info('Processing Prohibited Item ID: ' . $item);

            if ($request->hasFile('file_name')) {

                $file = $request->file_name[$key];
                $fileNameWithExt = $file->getClientOriginalName();
                // get file name
                $filename = pathinfo($fileNameWithExt, PATHINFO_FILENAME);
                // get extension
                $extension = $file->getClientOriginalExtension();

                $fileNameToStore = $filename . '_' . time() . '.' . $extension;
                $fileNameToStore = rand() . date('ymdHis') . $file->getClientOriginalName();  // use this

                // Log::info($fileNameWithExt);
                // Log::info($filename);
                // Log::info($extension);
                // Log::info($fileNameToStore);

                // Storage::disk('private')->putFileAs('mds/event/logo', $file, $fileNameToStore); // upload to a private disk
                $destinationPathThumbnail = public_path('storage/items/img/');
                $image = Image::read($file);
                $image->resize(150, 150);
                $image->save($destinationPathThumbnail . $fileNameToStore);
                // Storage::disk('public')->putFileAs('contractor/logo', $file, $fileNameToStore);
            } else {
                $fileNameToStore = 'noimage.jpg';
            }

            $items = new StoredItem();
            $items->item_image = $fileNameToStore;
            $items->item_image_path = 'restricted/img/' . $fileNameToStore; // store the path to the file
            $items->profile_id = $op->id;
            $items->item_id = $request->prohibited_item_id[$key];
            $items->item_description = $request->item_description[$key] ?? null;
            $items->event_id = $op->event_id;
            $items->venue_id = $op->venue_id;
            $items->location_id = $op->location_id;

            $items->save();
            Log::info('Stored Item: ' . json_encode($items));
        }

        // $items->item_quantity = $request->item_quantity;
        $notification = array(
            'message'       => 'Visitor Information created!',
            'alert-type'    => 'success'
        );



        // return redirect()->route('sps.admin', ['profile' => $op])->with($notification);
        // return redirect()->route('sps.customer.profile')->with($notification);

        return response()->json(['message' => 'Profile created successfully', 'profile' => $op], 201);
    }

    public function itemStore(Request $request)
    {
        // 1. Validate incoming request
        $validator = Validator::make($request->all(), [
            'item_description' => 'required',
        ]);

        if ($validator->fails()) {
            // Log::info($validator->errors());
            $error = true;
            // $message = 'Employee not create.' . $op->id;
            $message = implode($validator->errors()->all('<div>:message</div>'));
        } else {

            $error = false;
            $message = 'Item created successfully.';

            $profile = StoredItem::create([
                'profile_id' => $request->profile_id,
                'item_description' => $request->item_description,
            ]);

            $notification = array(
                'message'       => 'Item added successfully!',
                'alert-type'    => 'success'
            );
        }

        return response()->json([
            'error' => $error,
            'message' => $message,
        ]);
    }

    public function find()
    {
        // $projibitedItems = ProhibitedItem::all();
        // $item_statuses = ItemStatus::all();
        // $venues = Venue::all();
        // $events = Event::all();
        // $locations = Location::all();
        return view('sps.admin.find');
        // return view('sps.admin.find', [
        //     'prohibitedItems' => $projibitedItems,
        //     'item_statuses' => $item_statuses,
        //     'venues' => $venues,
        //     'events' => $events,
        //     'locations' => $locations,
        // ]);
    }

    // find the visitor by ref_number
    public function get(Request $request)
    {
        Log::info('inside Admin StorageController::get');
        $validator = Validator::make($request->all(), [
            'ref_number' => 'required',
        ]);

        if ($validator->fails()) {
            $message = implode($validator->errors()->all(':message'));
            $notification = array(
                'message'       => $message,
                'alert-type'    => 'error'
            );
            return back()->with($notification)->withInput();
        }

        $profile = Profile::where('ref_number', $request->ref_number)->first();
        // if ($profile) {
        //     return response()->json(['profile' => $profile], 200);
        // } else {
        //     return response()->json(['message' => 'Profile not found'], 404);
        // }
        if ($profile) {
            $items = StoredItem::where('profile_id', $profile->id)->get();
        }
    }

    public function getItemDescriptionView($id)
    {
        $items = StoredItem::where('profile_id', $id)->get();

        // dd($items);
        $view = view('/sps/admin/mv/items', [
            'items' => $items,
        ])->render();

        return response()->json(['view' => $view]);
    }

    public function getVisitorResultView($id)
    {
        $op = Profile::with('items')->where('ref_number', $id)->first();
        $item_statuses = ItemStatus::all();
        $profile_statuses = ProfileStatus::all();
        // dd($op);
        if (!$op) {
            $error = true;
            $message = 'No items found for this profile.';
            return response()->json([
                'error' => $error,
                'message' => $message,
            ]);
        }

        $view = view('/sps/admin/mv/visitor_item', [
            'spectator' => $op,
            'item_statuses' => $item_statuses,
            'profile_statuses' => $profile_statuses,
        ])->render();

        return response()->json([
            'view' => $view,
            'error' => false,
            'message' => 'Items found for this profile.',
        ]);
    }

    public function deleteVisitor($id)
    {
        $ws = Profile::findOrFail($id);
        $ws->delete();

        $error = false;
        $message = 'Visitor Information deleted succesfully.';

        $notification = array(
            'message'       => 'Delivery Type deleted successfully',
            'alert-type'    => 'success'
        );

        return response()->json(['error' => $error, 'message' => $message]);
        // return redirect()->route('tracki.setup.workspace')->with($notification);
    } // delete

    // find and update the status of the visitor

    public function editStatus($id)
    {
        //  dd('editTaskProgress');
        $data = Profile::find($id);
        //dd($data);
        // $data_arr = [];

        // $data_arr[] = [
        //     "id"        => $data->id,
        //     "status_id"  => $data->order_status_id,
        // ];

        // $response = ["retData"  => $data_arr];
        return response()->json($data);
    } // editStatus

    public function updateStatus(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|exists:profiles,id',
            'status_id' => 'required|exists:item_statuses,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $op = Profile::findOrFail($request->id);
        $op->item_status_id = $request->status_id;
        // $op->storage_location = $request->storage_location ?? null; // Optional field
        // $op->storage_tag_number = $request->storage_tag_number ?? null; // Optional field
        $op->updated_by = auth()->user()->id; // Assuming you have user authentication
        $op->save();

        return response()->json(['message' => 'Status updated successfully', 'profile' => $op], 200);
        // return response()->json(['error' => false, 'message' => 'Order Status updated successfully.', 'id' => $head->id]);

    }

    public function updateCheckInStatus(Request $request)
    {
        Log::info('inside Venue Admin StorageController::updateCheckInStatus');
        Log::info('StorageController::updateCheckInStatus request: ' . json_encode($request->all()));
        $request->validate([
            'profile_id' => 'required|exists:profiles,id',
            'status' => 'required',
        ]);

        $profile = Profile::findOrFail($request->profile_id);
        $profile->item_status_id = $request->status;
        $profile->save();

        return response()->json(['message' => 'Profile status updated successfully.']);
    }

    public function updateCheckInItemStatus(Request $request)
    {
        Log::info('inside Venue Admin StorageController::updateCheckInItemStatus');
        Log::info('StorageController::updateCheckInItemStatus request: ' . json_encode($request->all()));
        $rules = [
            'profile_id' => 'required|exists:stored_items,id',
            'status' => 'required|exists:item_statuses,id',
        ];
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            $error = true;
            // $message = 'Employee not create.' . $op->id;
            $message = implode($validator->errors()->all('<div>:message</div>'));
            return response()->json([
                'error' => $error,
                'message' => $message,
            ]);
        }
        // Validate the request
        Log::info('Validation passed for updateCheckInItemStatus');
        Log::info('Request data: ' . json_encode($request->all()));


        $item = StoredItem::findOrFail($request->profile_id);
        $profile = Profile::findOrFail($item->profile_id);

        $item->item_status_id = $request->status;
        $item->save();

        $items = $profile->items;
        $total = $items->count();

        Log::info('Total items: ' . $total);

        $submitted = getStatusIdByLabel('submitted');
        $checked_in = getStatusIdByLabel('checked-in');
        $returned = getStatusIdByLabel('returned');

        $submitted = $items->where('item_status_id', $submitted)->count();
        $checkedIn = $items->where('item_status_id', $checked_in)->count();
        $returned = $items->where('item_status_id', $returned)->count();

        Log::info('Submitted: ' . $submitted);
        Log::info('Checked In: ' . $checkedIn);
        Log::info('Returned: ' . $returned);

        $profile_submitted = getProfileStatusIdByLabel('submitted');
        $profile_all_checked_in = getProfileStatusIdByLabel('all-checked-in');
        $profile_all_returned = getProfileStatusIdByLabel('all-returned');
        $profile_partially_checked_in = getProfileStatusIdByLabel('partially-checked-in');
        $profile_partially_returned = getProfileStatusIdByLabel('partially-returned');

        Log::info('profile_submitted: ' . $profile_submitted);
        Log::info('profile_all_checked_in: ' . $profile_all_checked_in);
        Log::info('profile_all_returned: ' . $profile_all_returned);
        Log::info('profile_partially_checked_in: ' . $profile_partially_checked_in);
        Log::info('profile_partially_returned: ' . $profile_partially_returned);

        // Decision Tree:
        Log::info('Decision Tree for item status update: ***************************************************************');
        Log::info('Profile Id: ' . $profile->id);

        if ($returned === $total) {
            Log::info('All items returned: ' . $returned);
            Log::info('Total items: ' . $total);
            Log::info('Profile all returned status: ' . $profile_all_returned);
            $profile->item_status_id = $profile_all_returned;
        } elseif ($returned > 0 && $returned < $total) {
            Log::info('Partially returned items: ' . $returned);
            Log::info('Total items: ' . $total);
            Log::info('Profile partially returned status: ' . $profile_partially_returned);
            $profile->item_status_id = $profile_partially_returned;
        } elseif ($checkedIn === $total) {
            Log::info('All items checked in: ' . $checkedIn);
            Log::info('Total items: ' . $total);
            Log::info('Profile all checked in status: ' . $profile_all_checked_in);
            $profile->item_status_id = $profile_all_checked_in;
        } elseif ($checkedIn > 0 && $checkedIn < $total) {
            Log::info('Partially checked in items: ' . $checkedIn);
            Log::info('Total items: ' . $total);
            Log::info('Profile partially checked in status: ' . $profile_partially_checked_in);
            $profile->item_status_id = $profile_partially_checked_in;
        } elseif ($submitted === $total) {
            Log::info('All items submitted: ' . $submitted);
            Log::info('Total items: ' . $total);
            Log::info('Profile submitted status: ' . $profile_submitted);
            $profile->item_status_id = $profile_submitted;
        } else {
            $profile->item_status_id = '0'; // Optional fallback
        }

        $profile->save();
        return response()->json(['message' => 'Item status updated successfully.', 'profile_status_title' => $profile->profileStatus?->title, 'profile_status_color' => $profile->profileStatus?->color, 'profile' => $profile], 200);
    }

    public function updateField(Request $request, $id)
    {
        $request->validate([
            'field' => 'in:storage_location,storage_tag_number,operator_comments',
            'value' => 'nullable|string|max:255'
        ]);

        Log::info('Updating field: ' . $request->field . ' with value: ' . $request->value);

        $op = StoredItem::findOrFail($id);
        $op->{$request->field} = $request->value;
        $ok = $op->save();

        if ($ok) {
            $message = 'Field updated successfully: ' . $request->field . ' = ' . $request->value;
            $error_code = 200;
        } else {
            $message = 'Failed to update field: ' . $request->field;
            $error_code = 500;
            // return response()->json(['message' => 'Failed to update field'], 500);
        }
        return response()->json(['message' => $message, 'profile' => $op], $error_code);
    }

    public function switch($id)
    {
        if ($id) {
            if (Event::findOrFail($id)) {
                Log::info('Event ID: ' . $id);

                session()->put('EVENT_ID', $id);
                Log::info('Event ID: ' . session()->get('EVENT_ID'));
                return redirect()->route('sps.admin.tasks.index')->with('message', 'Event Switched.');
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
                return redirect()->route('sps.admin')->with('message', 'Venue Switched.');
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
            return redirect()->route('sps.admin')->with('message', 'Event/Venue Switched.');
        }
        //  else {
        // return back()->with('error', 'Workspace not found.');
        Log::info('event_id is null');
        return redirect()->route('sps.admin')->with('error', 'Event not found.');
        // }
    }
}
