@extends('sps.admin.layout.template')

@section('main')
<div class="content"
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
                Generate Tasks from Predefined List
            </button>
        </div>
        @include('sps.admin.tasks.modals.create_tasks_modal')

        <div class="mb-4 todo-list" id="tasks-list">
            @include('sps.admin.tasks.partials.list', ['categories' => $categories, 'venues' => $venues])
        </div>

        <!-- Modal -->
        <div class="modal fade" id="generateTasksModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <form method="POST" action="{{ route('chl.admin.tasks.copyToLead') }}" id="form_submit_event" class="copy-task-form form-submit-event">
                        @csrf

                        <div class="modal-header">
                            <h5 class="modal-title">Select Event & Venue</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                aria-label="Close"></button>
                        </div>

                        <div class="modal-body">
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
<script src="{{ asset('assets/js/pages/sps/admin/tasks.js') }}"></script>
<script>
    $(function() {
        // CSRF token for AJAX
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
    });
</script>
@endpush