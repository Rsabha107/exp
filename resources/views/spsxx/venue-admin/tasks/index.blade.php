@extends('sps.venue-admin.layout.template')
@section('main')
<div class="content" style="background-color: #ffffff; border-radius: 8px; padding: 20px; box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);">
    <div class="mb-9">
        <h2 class="mb-4">
            Daily Operations Checklist
            <span class="text-body-tertiary fw-normal">({{ $venueTasks->count() }})</span>
        </h2>
        <!-- <a class="fw-bold fs-9 mt-4" href="javascript:void(0)" data-bs-toggle="modal" data-bs-target="#createTaskModal">
            Add new task
        </a> -->

        @include('sps.venue-admin.tasks.modals.create')

        <div class="mb-4 todo-list" id="tasks-list">
            @include('sps.venue-admin.tasks.partials.list', ['venueTasks' => $venueTasks, 'categories' => $categories])
        </div>

    </div>
</div>
@endsection

@push('script')

<script>
    $(function() {
        // CSRF token for AJAX
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        // Handle toggle of completed status
        $('#tasks-list').on('change', '.task-toggle', function() {
            var venueTaskId = $(this).data('id'); // venue_tasks.id
            var url = '/sps/user/tasks/' + venueTaskId + '/toggle';

            $.ajax({
                url: url,
                type: 'PATCH',
                success: function(html) {
                    $('#tasks-list').html(html); // refresh list
                    toastr.success('Task status updated successfully!');
                },
                error: function(xhr) {
                    toastr.error('Failed to update task status!');
                    console.error(xhr.responseText);
                }
            });
        });

        // Handle delete if allowed (optional)
        $('#tasks-list').on('click', '.task-delete', function() {
            var venueTaskId = $(this).data('id');
            var url = '/sps/user/tasks/' + venueTaskId;

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
                if (result.isConfirmed) {
                    $.ajax({
                        url: url,
                        type: 'POST',
                        data: {
                            _method: 'DELETE'
                        },
                        success: function(response) {
                            if (response.success && response.html) {
                                $('#tasks-list').html(response.html);
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Deleted!',
                                    text: 'Task deleted successfully.',
                                    timer: 1500,
                                    showConfirmButton: false
                                });
                            }
                        },
                        error: function(xhr) {
                            Swal.fire({
                                icon: 'error',
                                title: 'Oops...',
                                text: 'Failed to delete task.'
                            });
                            console.error(xhr.responseText);
                        }
                    });
                }
            });
        });

        // Create task (venue tasks are usually cloned from admin tasks)
        $('#createTaskForm').submit(function(e) {
            e.preventDefault();
            var form = $(this);

            $.ajax({
                url: "{{ route('sps.user.tasks.store') }}", // should point to VenueTask creation
                type: 'POST',
                data: form.serialize(),
                success: function(html) {
                    $('#createTaskModal').modal('hide');
                    toastr.success('Task created successfully!');
                    $('#tasks-list').html(html);
                    form[0].reset();
                },
                error: function(xhr) {
                    toastr.error('Failed to create task.');
                    console.error(xhr.responseText);
                }
            });
        });

    });
</script>

@endpush