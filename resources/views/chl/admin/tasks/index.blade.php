@extends('chl.admin.layout.template')

@section('main')
<div class="content mb-5"
    style="background-color: #ffffff; border-radius: 8px; padding: 20px; box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);">
    <div class="mb-9">
        <h2 class="mb-4">
            Daily Operations Checklist
            <span class="text-body-tertiary fw-normal">
                ({{ $categories->sum(fn($category) => $category->tasks->count()) }})
            </span>
        </h2>
        <div class="d-flex justify-content-between mb-3">
            <a class="fw-bold fs-9 mt-4" href="javascript:void(0)" data-bs-toggle="modal" data-bs-target="#createTaskModal">
                Add new task
            </a>
            <!-- Generate Tasks Button -->
            <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal"
                data-bs-target="#generateTasksModal">
                Generate Tasks from this Predefined List
            </button>
        </div>
        @include('chl.admin.tasks.modals.create_tasks_modal')

        <div class="mb-4 todo-list" id="tasks-list">
            @include('chl.admin.tasks.list', ['categories' => $categories, 'venues' => $venues])
        </div>

        <!-- Modal -->
        <div class="modal fade" id="generateTasksModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <form method="POST" action="{{ route('chl.admin.tasks.copyToLead') }}" id="form_copy_to_lead" class="copy-task-form form-submit-event">
                        @csrf

                        <div class="modal-header">
                            <h5 class="modal-title">Select Event & Venue</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                aria-label="Close"></button>
                        </div>

                        <div class="modal-body">
                            <div class="mb-3">
                                <label for="date" class="form-label">Date</label>
                                <input class="form-control datetimepicker" id="reporting_date" name="reporting_date" required type="text" placeholder="dd/mm/yyyy" data-options='{"disableMobile":true,"dateFormat":"d/m/Y"}' />
                            </div>
                            <div class="mb-3">
                                <label for="event_id" class="form-label">Event</label>
                                <select id="event_dropdown" name="event_id" class="form-select">
                                    <option value="">Select Event</option>
                                    @foreach ($events as $event)
                                    <option value="{{ $event->id }}">{{ $event->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="mb-3">
                                <label for="venue_id" class="form-label">Venue</label>
                                <select id="venue_dropdown" name="venue_id" class="form-select">
                                    <option value="">Select Venue</option>
                                    @foreach ($venues as $venue)
                                    <option value="{{ $venue->id }}">{{ $venue->title }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="mb-3">
                                <label for="lead_id" class="form-label">Lead</label>
                                <select id="lead_dropdown" name="lead_id" class="form-select">
                                    <option value="">Select Lead</option>
                                    @foreach ($leads as $lead)
                                    <option value="{{ $lead->id }}">{{ $lead->name_email }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="modal-footer">
                            <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal"><?= get_label('close', 'Close') ?></button>
                            <button type="submit" class="btn btn-primary" id="submit_btn"><?= get_label('generate', 'Generate') ?></button>
                        </div>
                    </form>

                </div>
            </div>
        </div>

    </div>
</div>
@endsection

@push('script')
<script src="{{ asset('assets/js/pages/chl/admin/tasks.js') }}"></script>

<script>
    $(document).ready(function() {
        console.log("Document is ready.");

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        console.log("CSRF token setup completed:", $('meta[name="csrf-token"]').attr('content'));

        $('#tasks-list').on('click', '.task-delete', function(e) {
            e.preventDefault();
            console.log("Delete button clicked.");

            const taskId = $(this).data('id');
            console.log("Task ID to delete:", taskId);

            const url = '/chl/admin/tasks/delete/' + taskId;
            console.log("AJAX URL:", url);

            Swal.fire({
                title: 'Are you sure?',
                text: "This action cannot be undone.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Yes, delete it!',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                console.log("Swal result:", result);

                if (result.isConfirmed) {
                    console.log("User confirmed deletion.");

                    $.ajax({
                        url: url,
                        type: 'DELETE',
                        success: function(response) {
                            console.log("AJAX success response:", response);

                            if (response.success) {
                                // Remove the task div from the list
                                const taskDiv = $('[data-todo-offcanvas-target="todoOffcanvas-' + response.task_id + '"]');
                                taskDiv.fadeOut(300, function() {
                                    $(this).remove();
                                    console.log("Task div removed from DOM:", response.task_id);

                                    // Optionally, remove category label if it has no tasks left
                                    const categoryDiv = taskDiv.closest('h5.category-label').nextUntil('h5.category-label');
                                    if (categoryDiv.length === 0) {
                                        taskDiv.closest('h5.category-label').remove();
                                    }
                                });

                                Swal.fire({
                                    icon: 'success',
                                    title: 'Deleted!',
                                    text: response.message,
                                    timer: 1500,
                                    showConfirmButton: false
                                });
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Oops...',
                                    text: response.message
                                });
                            }
                        },
                        error: function(xhr) {
                            console.error("AJAX error response:", xhr.responseText);
                            Swal.fire({
                                icon: 'error',
                                title: 'Error!',
                                text: 'Something went wrong.'
                            });
                        }
                    });
                } else {
                    console.log("User canceled deletion.");
                }
            });
        });
    });
</script>
@endpush