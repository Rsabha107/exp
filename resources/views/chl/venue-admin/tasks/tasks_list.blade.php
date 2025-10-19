@if($tasks->isEmpty())
<p class="text-center text-muted p-3">No tasks available</p>
@else
<ul class="list-group list-group-flush">
    @foreach($tasks as $task)
    <li class="list-group-item d-flex justify-content-between align-items-center">
        <div class="d-flex align-items-center gap-2">
            <!-- Completed Checkbox -->
            <input
                type="checkbox"
                class="form-check-input task-toggle"
                data-id="{{ $task->id }}"
                data-toggle-url="{{ route('sps.user.tasks.toggle', ['id' => $task->id]) }}"
                @checked($task->completed)>


            <!-- Task Title -->
            <span class="{{ $task->completed ? 'text-decoration-line-through text-muted' : '' }}">
                {{ $task->title }}
            </span>

            <!-- Status Badge -->
            @if($task->status)
            <span class="badge bg-{{ $task->status_color }} ms-2">
                {{ strtoupper($task->status) }}
            </span>
            @endif
        </div>

        <div class="d-flex align-items-center gap-2">
            <!-- Due Date -->
            @if($task->due_date)
            <small class="text-muted">
                {{ $task->due_date->format('d M, Y h:i A') }}
            </small>
            @endif

            <!-- Edit Button -->
            <a href="{{ route('sps.user.tasks.edit', $task->id) }}" class="btn btn-sm btn-outline-secondary">
                <i class="bi bi-pencil"></i>
            </a>

            <!-- Delete Button -->
            <button class="btn btn-sm btn-outline-danger task-delete" data-id="{{ $task->id }}">
                <i class="bi bi-trash"></i>
            </button>
        </div>
    </li>
    @endforeach
</ul>
@endif