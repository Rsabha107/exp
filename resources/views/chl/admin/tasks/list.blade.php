@foreach ($categories as $category)
<!-- Section Label -->
<h5 class="category-label mt-4 mb-2 p-2 text-white rounded">
    {{ $category->title }}
</h5>

@if ($category->tasks->isEmpty())
<p class="text-center text-muted p-3">No tasks available</p>
@else
@foreach ($category->tasks as $task)

<div class="row justify-content-between align-items-md-center hover-actions-trigger btn-reveal-trigger border-translucent py-3 gx-0 cursor-pointer border-top"
    data-todo-offcanvas-toogle="data-todo-offcanvas-toogle" data-todo-offcanvas-target="todoOffcanvas-{{ $task->id }}">

    <div class="col-12 col-md-auto flex-1">
        <div>
            <div class="form-check mb-1 mb-md-0 d-flex align-items-center lh-1">
                {{-- <input type="checkbox"
                    class="form-check-input flex-shrink-0 form-check-line-through mt-0 me-2 task-toggle"
                    data-id="{{ $task->id }}" data-url="{{ route('chl.admin.tasks.toggle', $task->id) }}"
                @checked($task->completed)> --}}

                <label class="form-check-label mb-0 fs-8 me-2 line-clamp-1 flex-grow-1 flex-md-grow-0 cursor-pointer">
                    {{ $task->title }}
                </label>

                <!-- @if ($task->status)
                <span class="badge badge-phoenix fs-10 badge-phoenix-{{ optional($task->statusColor)->class ?? 'secondary' }}">
                    {{ strtoupper($task->status->title ?? '') }}
                </span>
                @endif -->


            </div>
        </div>
    </div>

    <div class="col-12 col-md-auto">
        <div class="d-flex ms-4 lh-1 align-items-center">
            @if ($task->created_at)
            <p class="text-body-tertiary fs-10 mb-md-0 me-2 me-md-3 mb-0">
                {{ $task->created_at->format('d M, Y') }}
            </p>
            @endif

            <div class="hover-actions end-0">
                <a href="{{ route('chl.admin.tasks.edit', $task->id) }}"
                    class="btn btn-phoenix-secondary btn-icon me-1 fs-10 text-body px-0">
                    <i class="bi bi-pencil"></i>
                </a>
                <!-- <button class="btn btn-phoenix-secondary btn-icon fs-10 text-danger px-0 task-delete"
                    data-id="{{ $task->id }}">
                    <i class="bi bi-trash"></i>
                </button> -->
                <button class="btn btn-phoenix-secondary btn-icon fs-10 text-danger px-0 task-delete"
                    data-id="{{ $task->id }}">
                    <i class="bi bi-trash"></i>
                </button>


            </div>

            <div class="hover-md-hide hover-lg-show hover-xl-hide">
                <p class="text-body-tertiary fs-10 ps-md-3 border-start-md fw-bold mb-md-0 mb-0">
                    {{ $task->created_at?->format('h:i A') ?? '—' }}
                </p>
            </div>
        </div>
    </div>
</div>
@endforeach
@endif
@endforeach

<style>
    .category-label {
        background-color: rgb(00, 117, 201);
        /* background-color: #0d6efd; */
        padding: 0.5rem 1rem;
        border-radius: 0.25rem;
        font-weight: 600;
    }
</style>