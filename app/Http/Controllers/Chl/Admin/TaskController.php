<?php

namespace App\Http\Controllers\Chl\Admin;

use App\Models\Chl\Category;
use App\Models\Chl\LeadCategory;
use App\Models\Chl\LeadTask;
use App\Models\Chl\Task;
use App\Http\Controllers\Controller;
use App\Models\Chl\LeadReporting;
use App\Models\Setting\Event;
use App\Models\Setting\Venue;
use App\Models\StatusColor;
use App\Models\TodoStatus;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class TaskController extends Controller
{
    /**
     * Display all tasks grouped by categories.
     */
    public function index()
    {
        Log::info('Fetching all tasks with categories, statuses, and colors');

        // Eager load tasks with status and statusColor, grouped by categories
        $categories = Category::with(['tasks' => function ($query) {
            $query->with(['status', 'statusColor'])
                ->orderBy('created_at', 'asc');
        }])
            ->where('event_id', session('EVENT_ID'))
            ->get();

        $events = Event::all();

        // Load additional data for dropdowns or UI elements
        $venues = Venue::orderBy('title')->get();
        // $todo_statuses = TodoStatus::all();
        // $statusColors = StatusColor::all();
        Log::info('Fetched categories: ' . $categories->count());
        // Log::info('Fetched todo statuses: ' . $todo_statuses->count());
        // Log::info('Fetched status colors: ' . $statusColors->count());

        // Return the main view with all necessary data
        return view('chl.admin.tasks.index', compact('categories', 'venues', 'events'));
    }


    /**
     * Show form for creating a new task.
     */
    public function create()
    {
        // $todo_statuses = TodoStatus::all();
        // $statusColors = StatusColor::all();
        $categories = Category::all();

        return view('chl.admin.tasks.create', compact('categories'));
    }

    /**
     * Store a newly created task.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'category_id' => 'nullable|exists:categories,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => true,
                'message' => implode($validator->errors()->all('<div>:message</div>'))
            ]);
        }

        $event = $venue = null;
        if ($request->category_id) {
            $category = Category::find($request->category_id);
            if ($category) {
                $event_id = $category->event_id;
                $venue_id = $category->venue_id;
            }
        }

        Task::create([
            'title' => $request->title,
            'category_id' => $request->category_id,
            'event_id' => $event_id,
            'venue_id' => $venue_id,
            'completed_flag' => false,
        ]);

        if ($request->ajax()) {
            // Fetch updated list
            $categories = Category::with(['tasks' => function ($query) {
                $query->with(['status', 'statusColor'])
                    ->orderBy('created_at', 'asc');
            }])->where('event_id', session('EVENT_ID'))->get();

            $venues = Venue::all();

            // Render the list partial
            $html = view('chl.admin.tasks.list', compact('categories', 'venues'))->render();

            return response()->json([
                'error' => false,
                'message' => 'Task created successfully!',
                'html' => $html,
            ]);
        }

        return redirect()->route('chl.admin.tasks.index')->with('success', 'Task created successfully.');
    }



    /**
     * Show form for editing a task.
     */
    public function edit($id)
    {
        $categories = Category::all();
        $task = Task::findOrFail($id);
        // $todo_statuses = TodoStatus::all();
        // $statusColors = StatusColor::all();

        return view('chl.admin.tasks.edit', compact('task', 'categories'));
    }

    /**
     * Update a task.
     */
    public function update(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'category_id' => 'nullable|exists:categories,id',
            // 'due_date' => 'nullable|string',
            // 'status_id' => 'nullable|exists:todo_statuses,id',
            // 'status_color_id' => 'nullable|exists:status_colors,id',
        ]);

        // $due_date = $request->due_date
        //     ? \Carbon\Carbon::createFromFormat('d/m/Y H:i', $request->due_date)
        //     : null;
        $task = Task::findOrFail($request->task_id);

        $task->update([
            'title' => $request->title,
            // 'due_date' => $due_date
        ]);

        return redirect()->route('chl.admin.tasks.index')->with('success', 'Task updated successfully.');
    }

    /**
     * Delete a task.
     */

    public function delete($id)
    {
        $task = Task::find($id);

        if (!$task) {
            return response()->json([
                'success' => false,
                'message' => 'Task not found.'
            ]);
        }

        $task->delete();

        return response()->json([
            'success' => true,
            'message' => 'Task deleted successfully!',
            'task_id' => $id
        ]);
    }

    /**
     * Toggle completed status.
     */
    public function toggle($id)
    {
        appLog('Toggling task completion for ID: ' . $id);
        $task = Task::findOrFail($id);
        $task->completed = !$task->completed;
        $task->save();

        $venueId = session('VENUE_ID');
        $eventId = session('EVENT_ID');

        $categories = Category::where('event_id', $eventId) // note: 'event' column in your table
            ->with([
                'leadTasks' => function ($q) use ($venueId, $eventId) {
                    $q->where('venue_id', $venueId)
                        ->where('event_id', $eventId)
                        ->orderBy('created_at', 'asc');
                },
                'leadComment' // eager load comments for display
            ])
            ->orderBy('created_at', 'asc')
            ->get();



        return view('chl.admin.tasks.list', compact('categories'));
    }

    public function copyToLead(Request $request)
    {
        Log::info('Initiating copy to LeadTask process');
        $rules = [
            'event_id' => 'required|exists:events,id',
            'venue_id' => 'required|exists:venues,id',
        ];
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            $message = implode($validator->errors()->all('<div>:message</div>'));
            return response()->json([
                'success' => false,
                'message' => $message,
            ], 422);
        }

        DB::beginTransaction();
        try {
            appLog('Starting copy to LeadTask process');
            appLog('Request Data: ' . json_encode($request->all()));
            appLog('Session EVENT_ID: ' . session('EVENT_ID'));

            $session_event_id = session('EVENT_ID');

            $request_venueId = $request->venue_id;
            $request_eventId = $request->event_id;

            // 🔹 Step 0: Check if a reporting already exists
            $reporting = LeadReporting::where('event_id', $request_eventId)
                ->where('venue_id', $request_venueId)
                ->first();

            if (!$reporting) {
                // Create new reporting if not exists
                // Log::info('Creating new LeadReportinging for Event ID: ' . $request_eventId . ' and Venue ID: ' . $request_venueId);
                $reporting = LeadReporting::create([
                    'reporting_date' => Carbon::createFromFormat('d/m/Y', $request->reporting_date)->toDateString(),
                    'event_id' => $request_eventId,
                    'venue_id' => $request_venueId,
                    'status'   => 'pending',
                ]);
            } else {
                // Optional: reset status if needed
                Log::info('Existing LeadReportinging found with ID: ' . $reporting->id . '. Resetting status to pending.');
                // $reporting->update(['status' => 'pending']);
            }

            $reportingId = $reporting->id;
            // Log::info('Using LeadReportinging ID: ' . $reportingId);

            // Step 1: Delete old Lead data for this venue + event
            LeadTask::where('venue_id', $request_venueId)->where('event_id', $request_eventId)->delete();
            LeadCategory::where('event_id', $request_eventId)->where('venue_id', $request_venueId)->delete();

            // Step 2: Copy fresh categories and tasks
            $categories = Category::where('event_id', $session_event_id)->get();

            foreach ($categories as $category) {
                // Log::info('Categories loop reporting id: ' . $reportingId);
                $leadCategory = LeadCategory::create([
                    'title'    => $category->title,
                    'venue_id' => $request_venueId,
                    'event_id' => $request_eventId,
                    'reporting_id' => $reportingId,
                ]);

                foreach ($category->tasks as $task) {
                    // Log::info('Tasks loop reporting id: ' . $reportingId);
                    LeadTask::create([
                        'title'           => $task->title,
                        'category_id'     => $leadCategory->id,
                        'venue_id'        => $request_venueId,
                        'event_id'        => $request_eventId,
                        'reporting_id'    => $reportingId,
                        'completed_flag'       => false,
                        // 'status_id'       => $task->status_id,
                        // 'status_color_id' => $task->status_color_id,
                        // 'due_date'        => $task->due_date,
                    ]);
                }
            }

            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Lead categories and tasks refreshed successfully'
            ]);
        } catch (\Exception $e) {
            Log::error('Copy to LeadTask failed: ' . $e->getMessage());
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to copy tasks'
            ], 500);
        }
    }
    // public function copyToLead(Request $request)
    // {
    //     $rules = [
    //         'event_id' => 'required|exists:events,id',
    //         'venue_id' => 'required|exists:venues,id',
    //     ];
    //     $validator = Validator::make($request->all(), $rules);
    //     if ($validator->fails()) {
    //         $message = implode($validator->errors()->all('<div>:message</div>'));
    //         return response()->json([
    //             'success' => false,
    //             'message' => $message,
    //         ], 422);
    //     }

    //     try {
    //         appLog('Starting copy to LeadTask process');
    //         appLog('Request Data: ' . json_encode($request->all()));
    //         appLog('Session EVENT_ID: ' . session('EVENT_ID'));
    //         appLog('Session VENUE_ID: ' . session('VENUE_ID'));

    //         $session_event_id = session('EVENT_ID');
    //         $session_venue_id = session('VENUE_ID');

    //         $request_venueId = $request->venue_id;
    //         $request_eventId = $request->event_id;
    //         // Step 1: Delete old Lead data for this venue + event
    //         LeadTask::where('venue_id', $request_venueId)->where('event_id', $request_eventId)->delete();
    //         LeadCategory::where('event_id', $request_eventId)->where('venue_id', $request_venueId)->delete();

    //         // Step 2: Copy fresh categories and tasks
    //         $categories = Category::where('event_id', $session_event_id)->get();

    //         foreach ($categories as $category) {
    //             $leadCategory = LeadCategory::create([
    //                 'title'    => $category->title,
    //                 'venue_id' => $request_venueId,
    //                 'event_id' => $request_eventId,
    //             ]);

    //             foreach ($category->tasks as $task) {
    //                 LeadTask::create([
    //                     'title'           => $task->title,
    //                     'category_id'     => $leadCategory->id,
    //                     'venue_id'        => $request_venueId,
    //                     'event_id'        => $request_eventId,
    //                     'completed_flag'       => false,
    //                     // 'status_id'       => $task->status_id,
    //                     // 'status_color_id' => $task->status_color_id,
    //                     // 'due_date'        => $task->due_date,
    //                 ]);
    //             }
    //         }

    //         return response()->json([
    //             'success' => true,
    //             'message' => 'Lead categories and tasks refreshed successfully'
    //         ]);
    //     } catch (\Exception $e) {
    //         Log::error('Copy to LeadTask failed: ' . $e->getMessage());
    //         return response()->json([
    //             'success' => false,
    //             'message' => 'Failed to copy tasks'
    //         ], 500);
    //     }
    // }

    public function switch($id)
    {
        if ($id) {
            if (Event::findOrFail($id)) {
                Log::info('Event ID: ' . $id);

                session()->put('EVENT_ID', $id);
                Log::info('Event ID: ' . session()->get('EVENT_ID'));
                return redirect()->route('chl.admin.tasks.index')->with('message', 'Event Switched.');
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
                return redirect()->route('chl.admin')->with('message', 'Venue Switched.');
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
        if ($request->event_id) {
            if (Event::findOrFail($request->event_id) && !session()->has('EVENT_ID')) {
                session()->put('EVENT_ID', $request->event_id);
            }

            // if (Venue::findOrFail($request->venue_id) && !session()->has('VENUE_ID')) {
            //     session()->put('VENUE_ID', $request->venue_id);
            // }
            return redirect()->route('chl.admin.tasks.index')->with('message', 'Event/Venue Switched.');
        }
        //  else {
        // return back()->with('error', 'Workspace not found.');
        Log::info('event_id is null');
        return redirect()->route('chl.admin.tasks.index')->with('error', 'Event not found.');
        // }
    }
}
