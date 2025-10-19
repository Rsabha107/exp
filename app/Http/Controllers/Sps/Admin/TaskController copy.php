<?php

namespace App\Http\Controllers\Sps\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\LeadCategory;
use App\Models\LeadTask;
use App\Models\Setting\Event;
use App\Models\Setting\Venue;
use App\Models\StatusColor;
use App\Models\TodoStatus;
use App\Models\Task;
use Illuminate\Http\Request;
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
        }])->get();

        $events = Event::all();
        $venues = Venue::all();

        // Load additional data for dropdowns or UI elements
        $venues = Venue::orderBy('title')->get();
        $todo_statuses = TodoStatus::all();
        $statusColors = StatusColor::all();
        Log::info('Fetched categories: ' . $categories->count());
        Log::info('Fetched todo statuses: ' . $todo_statuses->count());
        Log::info('Fetched status colors: ' . $statusColors->count());

        // Return the main view with all necessary data
        return view('sps.admin.tasks.index', compact('categories', 'venues', 'events', 'todo_statuses', 'statusColors'));
    }


    /**
     * Show form for creating a new task.
     */
    public function create()
    {
        $todo_statuses = TodoStatus::all();
        $categories = Category::all();
        $statusColors = StatusColor::all();

        return view('sps.admin.tasks.create', compact('todo_statuses', 'categories', 'statusColors'));
    }

    /**
     * Store a newly created task.
     */
    public function store(Request $request)
    {
        Log::info('Storing a new task');
        $rules = [
            'title' => 'required|string|max:255',
            'due_date' => 'nullable|string',
            'status_id' => 'nullable|exists:todo_statuses,id',
            'category_id' => 'nullable|exists:categories,id',
            'status_color_id' => 'nullable|exists:status_colors,id',
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

        $due_date = $request->due_date
            ? \Carbon\Carbon::createFromFormat('d/m/Y H:i', $request->due_date)
            : null;

        // Fetch category to get event and venue
        $event = $venue = null;
        if ($request->category_id) {
            $category = Category::find($request->category_id);
            if ($category) {
                $event = $category->event;
                $venue = $category->venue;
            }
        }

        Task::create([
            'title' => $request->title,
            'category_id' => $request->category_id,
            'event' => $event,         // assign event from category
            'venue' => $venue,         // assign venue from category
            'completed' => false,
            'status_id' => $request->status_id,
            'status_color_id' => $request->status_color_id ?? 1,
            'due_date' => $due_date
        ]);

        if ($request->ajax()) {
            return response()->json([
                'error' => false,
                'message' => 'Task created successfully!'
            ]);
        }

        return redirect()->route('sps.admin.tasks.index')->with('success', 'Task created successfully.');
    }


    /**
     * Show form for editing a task.
     */
    public function edit(Task $task)
    {
        $todo_statuses = TodoStatus::all();
        $categories = Category::all();
        $statusColors = StatusColor::all();

        return view('sps.admin.tasks.edit', compact('task', 'todo_statuses', 'categories', 'statusColors'));
    }

    /**
     * Update a task.
     */
    public function update(Request $request, Task $task)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'due_date' => 'nullable|string',
            'status_id' => 'nullable|exists:todo_statuses,id',
            'category_id' => 'nullable|exists:categories,id',
            'status_color_id' => 'nullable|exists:status_colors,id',
        ]);

        $due_date = $request->due_date
            ? \Carbon\Carbon::createFromFormat('d/m/Y H:i', $request->due_date)
            : null;

        $task->update([
            'title' => $request->title,
            'completed' => $request->completed ?? false,
            'status_id' => $request->status_id,
            'category_id' => $request->category_id,
            'status_color_id' => $request->status_color_id,
            'due_date' => $due_date
        ]);

        return redirect()->route('sps.admin.tasks.index')->with('success', 'Task updated successfully.');
    }

    /**
     * Delete a task.
     */
    public function destroy($id, Request $request)
    {
        $task = Task::find($id);
        if (!$task) {
            return response()->json(['success' => false, 'message' => 'Task not found']);
        }

        $task->delete();

        if ($request->ajax()) {
            $categories = Category::with(['tasks' => function ($query) {
                $query->with(['status', 'statusColor'])->orderBy('created_at', 'desc');
            }])->get();

            $html = view('sps.admin.tasks.partials.list', compact('categories'))->render();
            return response()->json(['success' => true, 'html' => $html]);
        }

        return redirect()->route('sps.admin.tasks.index')->with('success', 'Task deleted successfully.');
    }


    /**
     * Toggle completed status.
     */
    public function toggle($id)
    {
        $task = Task::findOrFail($id);
        $task->completed = !$task->completed;
        $task->save();

        $categories = Category::with(['tasks' => function ($query) {
            $query->with(['status', 'statusColor'])->orderBy('created_at', 'desc');
        }])->get();

        return view('sps.admin.tasks.partials.list', compact('categories'));
    }

    public function copyToLead(Request $request)
    {
        try {
            $venueId = $request->venue_id ?? session('VENUE_ID');
            $eventId = $request->event_id ?? session('EVENT_ID');

            if (!$venueId || !$eventId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Venue or Event not selected'
                ], 422);
            }

            // Categories copy
            $categories = Category::where('event', $eventId)->get();
            // $categories = Category::where('event', $eventId)->where('venue', $venueId)->get();
            foreach ($categories as $category) {
                $lead_category =  LeadCategory::create([
                    'title'    => $category->title,
                    'venue_id' => $venueId,
                    'event_id' => $eventId,
                ]);

                $lead_category_id = $lead_category->id;


                // Tasks copy
                $tasks = Task::all();
                foreach ($category->tasks as $task) {
                    LeadTask::create([
                        'title'          => $task->title,
                        'category_id'    => $lead_category_id,
                        'venue_id'       => $venueId,
                        'event_id'       => $eventId,
                        'completed'      => false,
                        'status_id'      => $task->status_id,
                        'status_color_id' => $task->status_color_id,
                        'due_date'       => $task->due_date,
                    ]);
                }
            }

            return response()->json([
                'success' => true,
                'message' => 'Tasks copied successfully'
            ]);
        } catch (\Exception $e) {
            Log::error('Copy to LeadTask failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to copy tasks'
            ], 500);
        }
    }
}