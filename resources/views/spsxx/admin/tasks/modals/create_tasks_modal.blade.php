<!-- Create Task Modal -->
<div class="modal fade" id="createTaskModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form id="form_submit_event" class="form-submit-event" action="{{ route('chl.admin.tasks.store') }}" method="POST">
                @csrf
                <input type="hidden" name="table" value="tasks-list">
                <div class="modal-header">
                    <h5 class="modal-title">Add Task</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Title</label>
                        <input type="text" name="title" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Category</label>
                        <select name="category_id" class="form-select">
                            <option value="">Select category</option>
                            @foreach($categories as $category)
                            <option value="{{ $category->id }}">{{ $category->title }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- <div class="mb-3">
                        <label class="form-label">Due Date</label>
                        <input
                            class="form-control datetimepicker"
                            type="text"
                            name="due_date"
                            placeholder="d/m/Y H:i"
                            data-options='{"enableTime":true,"dateFormat":"d/m/Y H:i","disableMobile":true,"allowInput":true}'>
                    </div> -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal"><?= get_label('close', 'Close') ?></button>
                    <button type="submit" class="btn btn-primary" id="submit_btn"><?= get_label('save', 'Save') ?></button>
                </div>
            </form>
        </div>
    </div>
</div>