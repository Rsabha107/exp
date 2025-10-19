@extends('sps.admin.layout.template')

@section('main')
<div class="container">
    <div class="card shadow-sm">
        <div class="card-header">
            <h5 class="mb-0">Edit Task</h5>
        </div>
        <div class="card-body">
            <form action="{{ route('sps.admin.tasks.update', $task->id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="mb-3">
                    <label class="form-label">Title</label>
                    <input type="text" name="title" value="{{ $task->title }}" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-select">
                        <option value="">Select Status</option>
                        <option value="draft" @selected($task->status == 'draft')>Draft</option>
                        <option value="urgent" @selected($task->status == 'urgent')>Urgent</option>
                        <option value="on process" @selected($task->status == 'on process')>On Process</option>
                        <option value="close" @selected($task->status == 'close')>Close</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label">Status Color (Bootstrap)</label>
                    <select name="status_color" class="form-select">
                        <option value="secondary" @selected($task->status_color == 'secondary')>Grey</option>
                        <option value="danger" @selected($task->status_color == 'danger')>Red</option>
                        <option value="warning" @selected($task->status_color == 'warning')>Yellow</option>
                        <option value="success" @selected($task->status_color == 'success')>Green</option>
                        <option value="primary" @selected($task->status_color == 'primary')>Blue</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label">Due Date</label>
                    <input type="datetime-local" name="due_date"
                           value="{{ $task->due_date ? $task->due_date->format('Y-m-d\TH:i') : '' }}"
                           class="form-control">
                </div>

                <div class="form-check mb-3">
                    <input class="form-check-input" type="checkbox" name="completed" value="1" id="completedCheck"
                           @checked($task->completed)>
                    <label class="form-check-label" for="completedCheck">Completed</label>
                </div>

                <button class="btn btn-primary">Update</button>
                <a href="{{ route('sps.admin.tasks.index') }}" class="btn btn-secondary">Cancel</a>
            </form>
        </div>
