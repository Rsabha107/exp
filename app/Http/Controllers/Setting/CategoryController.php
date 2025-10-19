<?php

namespace App\Http\Controllers\Setting;

use App\Http\Controllers\Controller;

use App\Models\Category;
use App\Models\Setting\Event;
use App\Models\Setting\Venue;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    // List all categories
    public function index()
    {
        $categories = Category::all();
        $events = Event::all();   // fetch events
        $venues = Venue::all();   // fetch venues
        return view('setting.category.index', compact('categories', 'events', 'venues'));
    }

    public function list(Request $request)
    {
        // Server-side pagination parameters
        $limit = $request->input('limit', 10);
        $offset = $request->input('offset', 0);
        $search = $request->input('search', '');
        $sort = $request->input('sort', 'id');
        $order = $request->input('order', 'asc');

        // Query with optional search
        $query = Category::query();

        if (!empty($search)) {
            $query->where('title', 'like', "%{$search}%")
                ->orWhere('event', 'like', "%{$search}%")
                ->orWhere('venue', 'like', "%{$search}%");
        }

        // Total count
        $total = $query->count();

        // Get paginated results
        $categories = $query->orderBy($sort, $order)
             ->where('event', session('EVENT_ID'))
            ->skip($offset)
            ->take($limit)
            ->get();

        // Transform for Bootstrap Table
        $rows = $categories->map(function ($category) {
            $eventNames = Event::whereIn('id', explode(',', $category->event))
                ->pluck('name')
                ->toArray();

            // $venueNames = Venue::whereIn('id', explode(',', $category->venue))
            //     ->pluck('title')
            //     ->toArray();

            return [
                'id' => $category->id,
                'title' => $category->title,
                'event' => $eventNames,   // show event names
                // 'venue' => $venueNames,   // show venue titles
                'actions' => ''
            ];
        });

        return response()->json([
            'total' => $total,
            'rows' => $rows
        ]);
    }


    // Show form to create category
    public function create()
    {
        return view('setting.category.create');
    }

    // Store new category
    // Store new category
    // Store
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'event' => 'required|exists:events,id',

        ]);

        $category = Category::create([
            'title' => $request->title,
            'event' => $request->event,   // store ID
            // 'venue' => $request->venue,   // store ID
        ]);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Category created successfully!',
                // 'data' => $category->load('event', 'venue')
            ]);
        }

        return redirect()->route('setting.category.index')
            ->with('success', 'Category created successfully.');
    }




    // Show form to edit category
    public function edit(Category $category)
    {
        return view('setting.category.edit', compact('category'));
    }

    // Update category
    // public function update(Request $request, Category $category)
    // {
    //     $request->validate([
    //         'title' => 'required|string|max:255',
    //     ]);

    //     $category->update([
    //         'title' => $request->title,
    //         'event' => $request->event_id,
    //         'venue' => $request->venue_id,
    //     ]);

    //     if ($request->ajax()) {
    //         return response()->json([
    //             'success' => true,
    //             'message' => 'Category updated successfully!',
    //             'data' => $category
    //         ]);
    //     }

    //     return redirect()->route('setting.category.index')
    //         ->with('success', 'Category updated successfully.');
    // }

    public function update(Request $request, Category $category)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'event' => 'required|exists:events,id',
            // 'venue' => 'required|exists:venues,id',
        ]);

        // Update the category
        $category->update([
            'title' => $request->title,
            'event' => $request->event,
            // 'venue' => $request->venue,
        ]);

        // Return updated data if request is AJAX
        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Category updated successfully!',
                'data' => [
                    'id' => $category->id,
                    'title' => $category->title,
                    'event' => $category->event,
                    // 'venue' => $category->venue,
                ]
            ]);
        }

        return redirect()->route('setting.category.index')
            ->with('success', 'Category updated successfully.');
    }


    // Delete category
    // public function destroy(Category $category)
    // {
    //     // Optional: check if category has tasks
    //     if ($category->tasks()->count()) {
    //         return redirect()->route('setting.category.index')
    //             ->with('error', 'Cannot delete category with tasks.');
    //     }

    //     $category->delete();

    //     return redirect()->route('setting.category.index')
    //         ->with('success', 'Category deleted successfully.');
    // }

    public function delete($id)
    {
        $category = Category::find($id);
        if (!$category) {
            return response()->json(['error' => true, 'message' => 'Category not found']);
        }

        if ($category->tasks()->count()) {
            return response()->json(['error' => true, 'message' => 'Cannot delete category with tasks']);
        }

        $category->delete();

        return response()->json(['error' => false, 'message' => 'Category deleted successfully']);
    }
}
