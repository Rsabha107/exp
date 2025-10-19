<script>
    $("body").on("click", ".delete-comment-btn", function(e) {
        console.log('clicked delete comment button');
        var id = $(this).data("id");
        const commentDiv = this.closest(".comment-item");
        e.preventDefault();
        console.log('Comment ID to delete:', id);

        Swal.fire({
            title: "Are you sure?",
            text: "This comment will be permanently deleted.",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#d33",
            cancelButtonColor: "#6c757d",
            confirmButtonText: "Yes, delete it!",
            cancelButtonText: "Cancel",
        }).then((result) => {
            if (result.isConfirmed) {
                fetch("{{ route('chl.admin.tasks.comment.delete') }}", {
                        method: "DELETE",
                        headers: {
                            "X-CSRF-TOKEN": document.querySelector(
                                'input[name="_token"]'
                            ).value,
                            "Content-Type": "application/json",
                        },
                        body: JSON.stringify({
                            id: id
                        }),
                    })
                    .then((res) => res.json())
                    .then((data) => {
                        console.log('Delete response:', data);
                        if (data.success) {
                            commentDiv.remove();
                            Swal.fire({
                                icon: "success",
                                title: "Deleted!",
                                text: "Comment deleted successfully.",
                                timer: 1500,
                                showConfirmButton: false,
                            });
                        } else {
                            Swal.fire({
                                icon: "error",
                                title: "Failed!",
                                text: "Could not delete the comment.",
                            });
                        }
                    })
                    .catch((err) => {
                        console.error(err);
                        Swal.fire({
                            icon: "error",
                            title: "Error!",
                            text: "Something went wrong while deleting.",
                        });
                    });
            }
        });
    });
</script>
@foreach ($categories as $category)
<h5 class="category-label mt-4 mb-2 text-white">{{ $category->title }}</h5>

@foreach ($category->leadTasks as $task)
<div class="row justify-content-between align-items-md-center hover-actions-trigger btn-reveal-trigger border-translucent py-3 gx-0 cursor-pointer border-top"
    data-todo-offcanvas-toogle="data-todo-offcanvas-toogle" data-todo-offcanvas-target="todoOffcanvas-{{ $task->id }}">

    <div class="col-12 col-md-auto flex-1">
        <div>
            <div class="form-check mb-1 mb-md-0 d-flex align-items-center lh-1">
                <input type="checkbox"
                    class="form-check-input flex-shrink-0 form-check-line-through mt-0 me-2 task-toggle"
                    data-id="{{ $task->id }}" @checked($task->completed)>
                <label class="form-check-label mb-0 fs-8 me-2 line-clamp-1 flex-grow-1 flex-md-grow-0 cursor-pointer">
                    {{ $task->title }}
                </label>
            </div>
        </div>
    </div>
</div>
@endforeach

@endforeach

<div class="mt-6">
    <h3>User for Event: {{ $currentEvent->name }}</h3>
    <ul class="list-group w-auto">
        <li class="list-group-item d-flex justify-content-between">
            <strong>Name:</strong> <span>{{ $currentUser->name }}</span>
        </li>
        <li class="list-group-item d-flex justify-content-between">
            <strong>Email:</strong> <span>{{ $currentUser->email }}</span>
        </li>
        <li class="list-group-item d-flex justify-content-between">
            <strong>Phone:</strong> <span>{{ $currentUser->phone ?? '-' }}</span>
        </li>
        <li class="list-group-item d-flex justify-content-between">
            <strong>Stadium:</strong> <span>{{ $categories->first()?->venue?->title ?? 'N/A' }}</span>
        </li>
        <li class="list-group-item d-flex flex-column">
            <strong class="mb-2">Report Comments / Things to Report:</strong>
            <div id="event-comments" class="d-flex flex-column gap-2">
                @foreach($categories as $category)
                {{-- @foreach($category->leadComments ?? [] as $comment) --}}
                @foreach(($category->leadComments ?? collect())->sortByDesc('created_at') as $comment)
                <div class="comment-item" data-comment-id="{{ $comment->id }}">
                    <div>
                        <span class="comment-text">{{ $comment->comment }}</span>
                        <small class="text-muted d-block mt-1">{{ $category->title }}</small>
                    </div>

                    <div>
                        @if ($comment->created_at)
                        <small class="text-muted mb-0">
                            {{ $comment->created_at->format('d M, Y') }}
                        </small>
                        <small class="text-muted mb-0">
                            {{ $comment->created_at?->format('h:i A') ?? '—' }}
                        </small>
                        @endif
                        <button type="button" class="btn btn-sm btn-outline-danger delete-comment-btn" data-id="{{ $comment->id }}">
                            <i class="bi bi-trash"></i>
                        </button>
                    </div>
                </div>
                @endforeach
                @endforeach
            </div>
        </li>

        <div class="mt-3">
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#commentModal">
                <i class="bi bi-plus-lg me-2"></i> Add Comment
            </button>
        </div>
    </ul>
</div>

<!-- Comment Modal -->
<div class="modal fade" id="commentModal" tabindex="-1" aria-labelledby="commentModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form class="form-submit-event" 
                action="{{ route('chl.venue.admin.tasks.comment') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="commentModalLabel">Add Your Comment</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">
                    <div class="mb-3">
                        <label for="category_id" class="form-label">Select Category</label>
                        <select class="form-select" name="category_id" id="category_id" required>
                            <option value="" disabled selected>-- Choose Category --</option>
                            @foreach($categories as $category)
                            <option value="{{ $category->id }}">{{ $category->title }}</option>
                            @endforeach
                        </select>
                    </div>
                    <textarea class="form-control" name="comment" rows="4" placeholder="Enter your comment here"
                        required></textarea>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary" id="submit_btn">Save</button>
                </div>
            </form>
        </div>
    </div>
</div>


<style>
    .category-label {
        background-color: #0d6efd;
        padding: 0.5rem 1rem;
        border-radius: 0.25rem;
        font-weight: 600;
    }

    .comment-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        background-color: #f8f9fa;
        border-left: 4px solid #0d6efd;
        padding: 0.5rem;
        border-radius: 0.25rem;
        transition: background-color 0.3s ease;
        position: relative;
    }

    .comment-item:hover {
        background-color: #e9ecef;
    }

    /* 🔹 Hide delete button initially */
    .delete-comment-btn {
        opacity: 0;
        pointer-events: none;
        /* prevents clicking when hidden */
        transition: opacity 0.3s ease;
    }

    /* 🔹 Show delete button when hovering over the comment */
    .comment-item:hover .delete-comment-btn {
        opacity: 1;
        pointer-events: auto;
        /* enables clicking when visible */
    }

    .comment-text {
        font-weight: 500;
    }

    #event-comments {
        max-height: 250px;
        overflow-y: auto;
    }
</style>
<script src="{{ asset('assets/js/pages/chl/venue-admin/comment.js') }}"></script>