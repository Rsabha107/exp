@extends('chl.venue-admin.layout.template')

@section('main')
<div class="content mb-5"
    style="background-color: #ffffff; border-radius: 8px; padding: 20px; box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);">
    <div class="mb-9">
        {{-- <h2 class="mb-4">Daily Operations Checklist <span class="text-body-tertiary fw-normal">({{ $tasks->count() }})</span></h2> --}}
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h3 class="mb-4">
                Daily Operations Checklist
                <span class="text-body-tertiary fw-normal">
                    ({{ $categories->sum(fn($category) => $category->leadTasks->count()) }})
                </span>
            </h3>
            <a data-id="{{ $lead_report->id }}" class="btn btn-warning submit-checklist-btn" target="_blank">
                Submit Checklist
            </a>
        </div>
        <div class="col">
            <h5 class="mb-0 text-primary position-relative fw-bold">
                <span class=" pe-2">{{ $currentEvent?->name ?? 'N/A' }}</span>
            </h5>
            <p class="mb-0 fw-bold">{{ $currentVenue?->title ?? 'N/A' }}</p>
            <p class="mb-0 fw-bold">{{ format_date($lead_report->reporting_date) }}</p>
        </div>
        <!-- <span class="text-body-tertiary fw-normal">
            (Venue: {{ $currentVenue?->title ?? 'N/A' }},
            Event: {{ $currentEvent?->name ?? 'N/A' }})
        </span> -->
        <div class="mb-4 todo-list" id="tasks-list">

            @include('chl.venue-admin.tasks.list', [
            'categories_id' => $categories,
            ])
        </div>
    </div>
</div>
@endsection

@push('script')

<script>
    $(function() {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $('#tasks-list').on('change', '.task-toggle', function() {
            var $checkbox = $(this);
            var taskId = $checkbox.data('id');
            var url = '/chl/venue-admin/tasks/' + taskId + '/toggle';

            $.ajax({
                url: url,
                type: 'POST',
                success: function(response) {
                    toastr.success('Task status updated successfully!');
                    // Optionally, update a status text or icon beside it
                    if (response.status === 'completed') {
                        $checkbox.closest('.task-item').addClass('completed');
                    } else {
                        $checkbox.closest('.task-item').removeClass('completed');
                    }
                },
                error: function(xhr) {
                    toastr.error('Failed to update task status!');
                    // Revert toggle if request failed
                    $checkbox.prop('checked', !$checkbox.prop('checked'));
                }
            });
        });
    });

    $("body").on("click", ".submit-checklist-btn", function(e) {
        e.preventDefault();

        console.log('clicked submit checklist button');
        var id = $(this).data("id");
        var post = $(this).data("post");
        console.log("Delete button clicked. Category ID:", id);
        console.log("Post URL:", post);

        Swal.fire({
            icon: 'warning',
            title: 'This will submit your daily checklist.',
            html: "You won't be able to edit it afterwards.<br>Even if there are incomplete tasks.",
            showCancelButton: true,
            confirmButtonText: 'Yes, submit it!',
            // cancelButtonText: 'Cancel',
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            customClass: {
                popup: 'small-alert',
                title: 'small-alert-title',
                htmlContainer: 'small-alert-text',
                // confirmButton: 'btn btn-primary btn-sm me-3',
                // cancelButton: 'btn btn-danger btn-sm'
            },
            buttonsStyling: true
        }).then((result) => {
            if (result.isConfirmed) {
                console.log("User confirmed deletion for ID:", id);

                $.ajax({
                    url: '/chl/venue-admin/tasks/export/pdf/' + id,
                    type: "get",
                    headers: {
                        "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr(
                            "content"
                        ),
                    },
                    // data: { id: id },
                    dataType: "json",
                    success: function(result) {
                        // console.log("AJAX success response:", result);

                        if (!result.error) {
                            toastr.success(
                                result.message ||
                                "Daily Operations Checklist submitted successfully!"
                            );

                            window.location.href = "{{ route('chl.venue.admin.tasks.report') }}";

                            // Refresh table using its fixed ID
                            console.log("Refreshing table: #category_table");
                            $("#category_table").bootstrapTable("refresh");
                        } else {
                            toastr.error(result.message || "Delete failed");
                            console.log(
                                "Delete failed with message:",
                                result.message
                            );
                        }
                    },
                    error: function(xhr, status, error) {
                        toastr.error(
                            "Something went wrong while submitting the daily checklist."
                        );
                        console.log(
                            "AJAX error:",
                            status,
                            error,
                            xhr.responseText
                        );
                    },
                });
            } else {
                console.log("User cancelled deletion.");
            }
        });
    });
</script>
@endpush