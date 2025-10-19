<script src="{{ asset('fnx/assets/js/phoenix.js') }}"></script>

<script>
    // showing the offcanvas for the task creation
    $(document).ready(function() {
        console.log('ready');
        $('.dropify').dropify();

    });
</script>

<input type="hidden" id="edit_schedule_id" name="id" value="{{ $event->id }}">
<input type="hidden" id="edit_event_table" name="table" value='event_table'>
<div class="modal-body">
    {{-- <div class="row">
        <div class="col mb-3">
            <label for="nameBasic" class="form-label"><?= get_label('name', 'name') ?> <span
                    class="asterisk">*</span></label>
            <input type="text" id="edit_event_name" class="form-control" name="name" value="{{ $event->name }}"
                placeholder="Please enter name" />
        </div>
    </div> --}}
    <div class="text-center mb-3">
        <div class="mb-3 text-start">
            <input type="file" name="file_name" class="dropify" data-height="200"
                data-default-file="{{ !empty($event->event_logo) ? route('chl.setting.event.file', $event->event_logo) : url('storage/upload/default.png') }}" />
                {{-- data-default-file="{{ !empty($event->event_logo) ? url('private/mds/event/logo/' . $event->event_logo) : url('storage/upload/default.png') }}" /> --}}
        </div>
    </div>
    <x-formy.form_input class="col mb-3" floating="1" inputValue="{{ $event->name }}" name="name"
        elementId="edit_name" inputType="text" inputAttributes="" label="Name" required="required" disabled="0" />

    <x-formy.form_select class="mb-4" floating="1" selectedValue="{{ $event->active_flag }}" name="active_flag"
        elementId="edit_event_id" label="Event" required="required" :forLoopCollection="$globalStatus" itemIdForeach="id"
        itemTitleForeach="name" style="" addDynamicButton="0" />
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
        Close</label>
    </button>
    <button type="submit" class="btn btn-primary" id="submit_btn">Save</label></button>
</div>
