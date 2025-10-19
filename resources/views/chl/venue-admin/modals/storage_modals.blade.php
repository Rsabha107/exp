<div class="offcanvas offcanvas-end offcanvas-global-modal in65" id="offcanvas-add-stored-item-modal" tabindex="-1"
    aria-labelledby="offcanvasWithBackdropLabel">
    <a class="close-task-detail in" id="close-task-detail" style="display: block;" data-bs-dismiss="offcanvas">
        <span>
            <i class="fa fa-times"></i>
        </span>
    </a>
    <x-sps.venue-admin.stored-item-drawer id="" formAction="{{ route('sps.admin.visitor.store') }}"
        formId="add_stored_item_form" :prohibitedItems="$prohibitedItems" :events="$events" :venues="$venues" :locations="$locations" />
</div>

<div class="modal fade" id="add_stored_item_modal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content bg-100">
            <div class="modal-header bg-modal-header">
                <h3 class="mb-0" id="staticBackdropLabel">Add Item Description</h3>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form novalidate="" class="modal-content form-submit-event needs-validation" id="form_submit_event"
                action="{{ route('sps.admin.item.store') }}" method="POST">
                @csrf
                <input type="hidden" name="profile_id" id="add_profile_id" value="">
                <input type="hidden" name="table" id="add_table" value="storage_table">

                <div class="modal-body">
                    <div class="row">
                        <x-formy.form_select class="mb-3 text-start" floating="1" selectedValue=""
                            name="prohibited_item_id" elementId="add_prohibited_item" label="Item Category"
                            required="required" :forLoopCollection="$prohibitedItems" itemIdForeach="id" itemTitleForeach="item_name"
                            style="" addDynamicButton="0" />
                        <x-formy.form_textarea class="col-sm-6 col-md-12 mb-3" floating="1" inputValue=""
                            name="item_description" elementId="add_item_description" inputType="text" inputAttributes=""
                            label="Item Description" required="required" disabled="0" />
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                        <?= get_label('close', 'Close') ?></label>
                    </button>
                    <button type="submit" class="btn btn-primary"
                        id="submit_btn"><?= get_label('save', 'Save') ?></label></button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="stored_item_detail_modal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content bg-100">
            <div class="modal-header bg-modal-header">
                <h3 class="mb-0 text-white" id="staticBackdropLabel">Item Description</h3>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div id="global-stored-item-content"></div>
        </div>
    </div>
</div>

<div class="modal fade" id="statusModal" tabindex="-1" data-bs-backdrop="static" aria-labelledby="staticBackdropLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-top">
        <div class="modal-content bg-100">
            <div class="modal-header bg-modal-header">
                <h3 class=" text-white mb-0" id="staticBackdropLabel">Change Status</h3>
                <button class="btn p-1" type="button" data-bs-dismiss="modal" aria-label="Close"><span
                        class="fas fa-times fs--1 text-danger"></span></button>
            </div>
            <form class="needs-validation form-submit-event" novalidate=""
                action="{{ route('sps.admin.item.status.update') }}" method="POST" id="order_status">
                <!-- <form class="needs-validation" novalidate="" action="{{ url('/tracki/task/status/update') }}" method="POST" id="task_status"> -->
                @csrf
                <div class="modal-body">
                    <div class="modal-body px-0">
                        <div class="row g-4">
                            <div class="col-lg-12">
                                <input type="hidden" id="editId" name="id">
                                <input type="hidden" id="statusTable" name="table" value="order_table">
                                {{-- @foreach ($item_statuses as $key => $item)
                                    <option value="{{ $item->id }}">
                                        {{ $item->title }}
                                    </option>
                                @endforeach
                                </select>  --}}
                                <x-formy.form_select class="col-sm-6 col-md-12 mb-3" floating="1" selectedValue=""
                                    name="status_id" elementId="add_status_id" label="Status" required="required"
                                    :forLoopCollection="$item_statuses" itemIdForeach="id" itemTitleForeach="title" style=""
                                    addDynamicButton="0" />

                                {{-- <x-formy.form_input class="mb-3 text-start" floating="1" inputValue="" name="storage_location"
                                    elementId="add_storage_location" inputType="text" inputAttributes="" label="Storage Location"
                                    required="required" disabled="0" />

                                <x-formy.form_input class="mb-3 text-start" floating="1" inputValue="" name="storage_tag_number"
                                    elementId="add_storage_tag_number" inputType="text" inputAttributes="" label="Tag Number"
                                    required="required" disabled="0" /> --}}
                            </div>
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

{{-- <div class="offcanvas offcanvas-end offcanvas-filter-modal in" id="scheduleFilterOffcanvas" tabindex="-1"
    aria-labelledby="offcanvasWithBackdropLabel">
    <x-setting.admin-schedule-filter-drawer id="" formAction="" formId="filter_schedule_form"
        :events="$events" :venues="$venues" :rsps="$rsps" :schedules="$schedules" :globalYn="$global_yn" />
</div> --}}
