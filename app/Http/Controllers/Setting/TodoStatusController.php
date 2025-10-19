<?php

namespace App\Http\Controllers\Setting;
use App\Http\Controllers\Controller;

use App\Models\Category;
use App\Models\TodoStatus;
use Illuminate\Http\Request;

class TodoStatusController extends Controller
{
    // List all todo statuses
    public function index()
    {
        $todoStatuses = TodoStatus::all();
        return view('setting.todo_status.index', compact('todoStatuses'));
    }

    // Show form to create todo status
    public function create()
    {
        return view('setting.todo_status.create');
    }

    // Store new todo status
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
        ]);

        TodoStatus::create($request->only('title'));

        return redirect()->route('setting.todo_status.index')
            ->with('success', 'Todo status created successfully.');
    }

    // Show form to edit todo status
    public function edit(TodoStatus $todoStatus)
    {
        return view('setting.todo_status.edit', compact('todoStatus'));
    }

    // Update todo status
    public function update(Request $request, TodoStatus $todoStatus)
    {
        $request->validate([
            'title' => 'required|string|max:255',
        ]);

        $todoStatus->update($request->only('title'));

        return redirect()->route('setting.todo_status.index')
            ->with('success', 'Todo status updated successfully.');
    }

    // Delete todo status
    public function destroy(TodoStatus $todo_status)
    {
        // Optional: check if todo_status has tasks
        if ($todo_status->tasks()->count()) {
            return redirect()->route('setting.todo_status.index')
                ->with('error', 'Cannot delete todo status with tasks.');
        }

        $todo_status->delete();

        return redirect()->route('setting.todo_status.index')
            ->with('success', 'Todo status deleted successfully.');
    }
}
