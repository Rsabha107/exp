<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Task;
use App\Models\VenueTask;
use Illuminate\Support\Facades\Auth;

class UserTaskController extends Controller
{
    /**
     * Display all tasks for the logged-in venue user.
     */
    public function index()
    {
        $venueId = auth()->user()->venue_id; // assuming each venue user has a venue_id
        $venueTasks = \App\Models\VenueTask::with('task')
            ->where('venue_id', $venueId)
            ->get();

        $categories = [
            'preevent' => 'Pre-Event Operations / Match Day',
            'duringevent' => 'During Event Operations',
            'postevent' => 'Post Event Operations'
        ];

        return view('sps.venue-admin.tasks.index', compact('venueTasks', 'categories'));
    }


    /**
     * Toggle the completed status of a task for this venue user.
     */
    public function toggle($id)
    {
        $venueTask = VenueTask::findOrFail($id);
        $venueTask->completed = !$venueTask->completed;
        $venueTask->save();

        return response()->json(['success' => true, 'completed' => $venueTask->completed]);
    }

    /**
     * Refresh only the task list for AJAX.
     */
    public function tasksList()
    {
        $venueId = Auth::user()->venue_id;

        $venueTasks = VenueTask::with('task')
            ->where('venue_id', $venueId)
            ->orderBy('created_at', 'desc')
            ->get();

        $categories = [
            'preevent' => 'Pre-Event Operations / Match Day',
            'duringevent' => 'During Event Operations',
            'postevent' => 'Post Event Operations'
        ];

        return view('sps.venue-admin.tasks.partials.list', compact('venueTasks', 'categories'));
    }
}
