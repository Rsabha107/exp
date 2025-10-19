

<div class="modal fade" id="create_categories_modal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content bg-100">
            <div class="modal-header bg-modal-header">
                Add Category
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form novalidate class="modal-content form-submit-event needs-validation" id="form_submit_event" action="{{ route('setting.category.store') }}" method="POST">
                @csrf
                <input type="hidden" name="table" value="category_table">

                <div class="modal-body">
                    <!-- Title Input -->
                    <div class="col-md-12 mb-3">
                        <label for="category_title" class="form-label"><?= get_label('title', 'Title') ?> <span class="asterisk">*</span></label>
                        <input required type="text" id="category_title" class="form-control" name="title" placeholder="<?= get_label('please_enter_title', 'Please enter title') ?>" />
                    </div>

                    <!-- Event Multi-Select -->
                    <div class="col-md-12 mb-3">
                        <label for="event_id" class="form-label">Event</label>
                        <select class="form-select" id="event_id" name="event_id" required>
                            <option value="">-- Select Event --</option>
                            @foreach ($events as $event)
                            <option value="{{ $event->id }}">{{ $event->name }}</option>
                            @endforeach
                        </select>
                    </div>

                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal"><?= get_label('close', 'Close') ?></button>
                    <button type="submit" class="btn btn-primary" id="submit_btn"><?= get_label('save', 'Save') ?></button>
                </div>
            </form>
        </div>
    </div>
</div>


<div class="modal fade" id="edit_categories_modal" tabindex="-1" data-bs-backdrop="static" aria-labelledby="editCategoryLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content bg-100">
            <div class="modal-header bg-modal-header">
                Edit Category
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <form novalidate class="form-submit-event needs-validation" id="editCategoryForm" method="POST">
                @csrf
                @method('PUT')
                <input type="hidden" id="edit_category_id" name="id">
                <input type="hidden" id="edit_category_table" name="table" value="category_table">

                <div class="modal-body">

                    <div class="col-md-12 mb-3">
                        <label class="form-label">Title <span class="asterisk">*</span></label>
                        <input type="text" id="edit_category_title" class="form-control" name="title" placeholder="Enter title" required>
                    </div>

                    <div class="col-md-12 mb-3">
                        <label class="form-label">Event</label>
                        <select class="form-select js-select-event-assign-multiple" id="edit_category_event" name="event" multiple>
                            @foreach ($events as $event)
                                <option value="{{ $event->id }}">{{ $event->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-12 mb-3">
                        <label class="form-label">Venue</label>
                        <select class="form-select js-select-venue-assign-multiple" id="edit_category_venue" name="venue" multiple>
                            @foreach ($venues as $venue)
                                <option value="{{ $venue->id }}">{{ $venue->title }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>
