<!-- Create Task Modal -->
<div class="modal fade" id="createTaskModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form id="createTaskForm">
                @csrf
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
                        <label class="form-label">Status</label>
                        <select name="status" class="form-select">
                            <option value="">Select Status</option>
                            <option value="draft">Draft</option>
                            <option value="urgent">Urgent</option>
                            <option value="on process">On Process</option>
                            <option value="close">Close</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Category</label>
                        <select name="category" class="form-select">
                            <option value="">Select category</option>
                            <option value="preevent">Pre-event Operations/ Match Day </option>
                            <option value="duringevent">During Event Operations </option>
                            <option value="postevent">Post Event Operations</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Status Color</label>
                        <select name="status_color" class="form-select">
                            <option value="secondary">Grey</option>
                            <option value="danger">Red</option>
                            <option value="warning">Yellow</option>
                            <option value="success">Green</option>
                            <option value="primary">Blue</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Due Date</label>
                        <input
                            class="form-control datetimepicker"
                            type="text"
                            name="due_date"
                            placeholder="d/m/Y H:i"
                            data-options='{"enableTime":true,"dateFormat":"d/m/Y H:i","disableMobile":true,"allowInput":true}'>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Save Task</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                </div>
            </form>
        </div>
    </div>
</div>


