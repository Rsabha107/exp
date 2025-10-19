<div class="modal fade" id="create_booking_status_modal" tabindex="-1" data-bs-backdrop="static" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-top">
        <div class="modal-content bg-100">
            <div class="modal-header bg-modal-header">Add booking status
                {{-- <h3 class="mb-0" id="staticBackdropLabel"><?= get_label('create_booking_status', 'Add booking status') ?></h3> --}}
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form class="needs-validation px-3 form-submit-event" id="create_booking_status_form" novalidate="" action="{{ route('mds.setting.status.booking.store') }}" method="POST">
                @csrf

                <input type="hidden" name="table" value="booking_status_table">

                <div class="modal-body">
                    <div class="row">
                        <div class="col mb-3">
                            <label for="booking_status_title" class="form-label"><?= get_label('title', 'Title') ?> <span class="asterisk">*</span></label>
                            <input type="text" id="addTitle" class="form-control" name="title" required placeholder="<?= get_label('please_enter_title', 'Please enter title') ?>" />
                        </div>
                    </div>
                    <div class="row">
                        <div class="col mb-3">
                            <label for="nameBasic" class="form-label"><?= get_label('color', 'Color') ?> <span class="asterisk">*</span></label>
                            <select class="form-select" id="color" name="color">
                                <option class="badge badge-phoenix badge-phoenix-primary" value="primary"><?= get_label('primary', 'Primary') ?></option>
                                <option class="badge badge-phoenix badge-phoenix-secondary" value="secondary"><?= get_label('secondary', 'Secondary') ?></option>
                                <option class="badge badge-phoenix badge-phoenix-success" value="success"><?= get_label('success', 'Success') ?></option>
                                <option class="badge badge-phoenix badge-phoenix-danger" value="danger"><?= get_label('danger', 'Danger') ?></option>
                                <option class="badge badge-phoenix badge-phoenix-warning" value="warning"><?= get_label('warning', 'Warning') ?></option>
                                <option class="badge badge-phoenix badge-phoenix-info" value="info"><?= get_label('info', 'Info') ?></option>
                                <option class="badge badge-phoenix badge-phoenix-dark" value="dark"><?= get_label('dark', 'Dark') ?></option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal"><?= get_label('close', 'Close') ?></label></button>
                    <button type="submit" class="btn btn-primary" id="submit_btn"><?= get_label('save','Save') ?></label></button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="edit_booking_status_modal" tabindex="-1" data-bs-backdrop="static" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-top">
        <div class="modal-content bg-100">
            <div class="modal-header bg-modal-header">Edit Booking Status
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form class="needs-validation form-submit-event" id="edit_booking_status_form" novalidate="" action="{{ route('mds.setting.status.booking.update') }}" method="POST">
                @csrf

                <input type="hidden" id="edit_booking_status_id" name="id" value="">
                <input type="hidden" id="edit_booking_status_table" name="table" value="">

                <div class="modal-body">
                    <div class="row">
                        <div class="col mb-3">
                            <label for="booking_status_title" class="form-label"><?= get_label('title', 'Title') ?> <span class="asterisk">*</span></label>
                            <input type="text" id="edit_booking_status_title" class="form-control" name="title" required placeholder="<?= get_label('please_enter_title', 'Please enter title') ?>" />
                        </div>
                    </div>
                    <div class="row">
                        <div class="col mb-3">
                            <label for="nameBasic" class="form-label"><?= get_label('color', 'Color') ?> <span class="asterisk">*</span></label>
                            <select class="form-select" id="edit_booking_status_color" name="color">
                                <option class="badge badge-phoenix badge-phoenix-primary" value="primary"><?= get_label('primary', 'Primary') ?></option>
                                <option class="badge badge-phoenix badge-phoenix-secondary" value="secondary"><?= get_label('secondary', 'Secondary') ?></option>
                                <option class="badge badge-phoenix badge-phoenix-success" value="success"><?= get_label('success', 'Success') ?></option>
                                <option class="badge badge-phoenix badge-phoenix-danger" value="danger"><?= get_label('danger', 'Danger') ?></option>
                                <option class="badge badge-phoenix badge-phoenix-warning" value="warning"><?= get_label('warning', 'Warning') ?></option>
                                <option class="badge badge-phoenix badge-phoenix-info" value="info"><?= get_label('info', 'Info') ?></option>
                                <option class="badge badge-phoenix badge-phoenix-dark" value="dark"><?= get_label('dark', 'Dark') ?></option>
                            </select>
                        </div>
                    </div>
                </div>


                <div class="modal-footer">
                    <button class="btn btn-outline-danger" type="button" data-bs-dismiss="modal">Cancel</button>
                    <button class="btn btn-primary" type="submit" id="submit_btn">Save</button>
                </div>
            </form>
        </div>
    </div>
</div>