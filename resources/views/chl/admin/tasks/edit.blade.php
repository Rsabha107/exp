@extends('chl.admin.layout.template')

@section('main')
<div class="container">
    <div class="card shadow-sm">
        <div class="card-header">
            <h5 class="mb-0">Edit Task</h5>
        </div>
        <div class="card-body">
            <form action="{{ route('chl.admin.tasks.update') }}" method="POST">
                @csrf
                <input type="hidden" name="task_id" value="{{ $task->id }}">

                <div class="mb-3">
                    <label class="form-label">Title</label>
                    <input type="text" name="title" value="{{ $task->title }}" class="form-control" required>
                </div>

                <div class="form-check mb-3">
                    <input class="form-check-input" type="checkbox" name="completed" value="1" id="completedCheck"
                        @checked($task->completed)>
                    <label class="form-check-label" for="completedCheck">Completed</label>
                </div>

                <button class="btn btn-primary">Update</button>
                <a href="{{ route('chl.admin.tasks.index') }}" class="btn btn-secondary">Cancel</a>
            </form>
        </div>
    </div>
</div>
@endsection