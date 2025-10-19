@extends('sps.venue-admin.layout.template')

@section('main')
<div class="content"
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
                Generate PDF
            </a>
        </div>
        <div class="mb-4 todo-list" id="tasks-list">
            <span class="text-body-tertiary fw-normal">
                (Venue: {{ $currentVenue?->title ?? 'N/A' }},
                Event: {{ $currentEvent?->name ?? 'N/A' }})
            </span>

            @include('sps.venue-admin.tasks.partials.list', [
            'categories_id' => $categories,
            ])
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

        $('#tasks-list').on('change', '.task-toggle', function() {
            var taskId = $(this).data('id');
            var url = '/exp/venue-admin/tasks/' + taskId + '/toggle';

            $.ajax({
                url: url,
                type: 'POST', // make sure it matches the route
                success: function(html) {
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
</script>
@endpush