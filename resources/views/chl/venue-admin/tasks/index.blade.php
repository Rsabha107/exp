@extends('chl.venue-admin.layout.template')

@section('main')
<div class="content mb-5"
    style="background-color: #ffffff; border-radius: 8px; padding: 20px; box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);">
    <div class="mb-9">
        {{-- <h2 class="mb-4">Daily Operations Checklist <span class="text-body-tertiary fw-normal">({{ $tasks->count() }})</span></h2> --}}
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h2 class="mb-4">
                Daily Operations Checklist
                <span class="text-body-tertiary fw-normal">
                    ({{ $categories->sum(fn($category) => $category->leadTasks->count()) }})
                </span>
            </h2>
            <a href="{{ route('chl.venue.admin.tasks.export.pdf') }}" class="btn btn-danger" target="_blank">
                Generate PDF and Send Email
            </a>
        </div>
        <div class="col">
            <h5 class="mb-0 text-primary position-relative fw-bold">
                <span class=" pe-2">{{ $currentEvent?->name ?? 'N/A' }} &amp; Charts</span>
            </h5>
            <p class="mb-0 fw-bold">{{ $currentVenue?->title ?? 'N/A' }}</p>
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
<!-- <script>
    $(function() {
        // CSRF token for AJAX
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $('#tasks-list').on('change', '.task-toggle', function() {
            var taskId = $(this).data('id');
            var url = '/chl/venue-admin/tasks/' + taskId + '/toggle';

            $.ajax({
                url: url,
                type: 'POST', // make sure it matches the route
                success: function(html) {
                    console.log(html);
                    $('#tasks-list').html(html);
                    toastr.success('Task status updated successfully!');
                },
                error: function(xhr) {
                    console.log(xhr.responseText); // log full error to console
                    toastr.error('Failed to update task status!');
                }
            });
        });

    });
</script> -->
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
</script>
@endpush