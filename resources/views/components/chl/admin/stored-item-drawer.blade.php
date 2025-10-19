<div class="offcanvas-body">
    <div class="row">
        <div class="col-sm-12">
            <form class="row g-3 needs-validation form-submit-event" id="{{ $formId }}" novalidate=""
                action="{{ $formAction }}" method="POST">
                @csrf
                <input type="hidden" name="table" value="storage_table" />

                <div class="card">
                    <div class="card-header">
                        <div class="row gy-3 justify-content-between">
                            <div class="col-md-9 col-auto">
                                <h5 class="mb-2 text-body-emphasis">New Items</h5>
                                <h6 class="text-body-tertiary fw-semibold">Fill the form below to create a new storage
                                    items</h6>
                            </div>
                            {{-- <div class="col-md-3 col-auto" style="text-align: right;">
                                <h5 class="mb-2 text-body-emphasis">{{ $user->contractor->company_name }} ({{ $user->contractor->name }})</h5>
                            <h6 class="text-body-tertiary fw-semibold">{{ format_date(now()) }}</h6>
                        </div> --}}
                    </div>

                </div>
                <div class="card-body">

                    <div class="row mb-3">

                        <x-formy.form_select class="col-sm-6 col-md-12 mb-3" floating="1"
                            selectedValue="" name="event_id" elementId="add_event_id"
                            label="Event" required="required" :forLoopCollection="$events"
                            itemIdForeach="id" itemTitleForeach="name" style=""
                            addDynamicButton="0" />

                        <x-formy.form_select class="col-sm-6 col-md-6 mb-3" floating="1"
                            selectedValue="" name="venue_id" elementId="add_venue_id"
                            label="Venue" required="required" :forLoopCollection="$venues"
                            itemIdForeach="id" itemTitleForeach="title" style=""
                            addDynamicButton="0" />

                        <x-formy.form_select class="col-sm-6 col-md-6 mb-3" floating="1"
                            selectedValue="" name="location_id" elementId="add_location_id"
                            label="Location" required="required" :forLoopCollection="$locations"
                            itemIdForeach="id" itemTitleForeach="title" style=""
                            addDynamicButton="0" />


                        <x-formy.form_input class="col-sm-6 col-md-6 mb-3" floating="1" inputValue=""
                            name="first_name" elementId="add_first_name" inputType="text" inputAttributes=""
                            label="First Name" required="required" disabled="0" />

                        <x-formy.form_input class="col-sm-6 col-md-6 mb-3" floating="1" inputValue=""
                            name="last_name" elementId="add_last_name" inputType="text" inputAttributes=""
                            label="Last Name" required="" disabled="0" />

                        <x-formy.form_input class="col-sm-6 col-md-6 mb-3" floating="1" inputValue=""
                            name="email_address" elementId="add_email_address" inputType="email" inputAttributes=""
                            label="Email Address" required="required" disabled="0" />

                        <x-formy.form_input class="col-sm-6 col-md-6 mb-3" floating="1" inputValue=""
                            name="phone" elementId="add_phone" inputType="phone" inputAttributes=""
                            label="Phone Number" required="required" disabled="0" />
                    </div>

                </div>
        </div>

        <div class="card mb-3">
            <div class="card-body">
                <div class="btn-container mb-2">
                    <a id="addNew" class="btn btn-sm">
                        <i class="fas fa-plus-circle text-success me-2"></i>Add new item
                    </a>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover" id="add_item">
                        <thead>
                            <tr>
                                <th scope="col" style="width:2%"></th>
                                <th scope="col" class="fs-9 lh-sm" style="width:10%">Restricated Item</th>
                                <th scope="col" class="fs-9 lh-sm" style="width:15%">Description</th>
                                <th scope="col" class="fs-9 lh-sm" style="width:10%">Image</th>

                            </tr>
                        </thead>
                        <tbody>
                            <tr id="row_1">
                                <th id="delete_1" scope="row" class="delete_row">
                                    <i class="fas fa-minus-circle text-danger"></i>
                                </th>

                                <td>
                                    <x-formy.form_select class="col-sm-6 col-md-12 mb-3" floating="1"
                                        selectedValue="" name="prohibited_item_id[]" elementId="add_prohibited_item_id"
                                        label="Restricted Items" required="required" :forLoopCollection="$prohibitedItems"
                                        itemIdForeach="id" itemTitleForeach="item_name" style=""
                                        addDynamicButton="0" />
                                </td>
                                <td>
                                    <x-formy.form_textarea class="col-sm-6 col-md-12 mb-3" floating="1"
                                        inputValue="" name="item_description[]"
                                        elementId="add_item_description1" inputType="text" inputAttributes=""
                                        label="Item Description" required="required" disabled="0" />
                                </td>
                                <td>
                                    <div class="mb-3 text-start">
                                        <div class="form-icon-container mb-3">
                                            <input class="form-control form-icon-input" name="file_name[]"
                                                type="file" id="item_image_1" />
                                            <span class="fas fa-file text-body fs-9 form-icon"></span>
                                        </div>
                                    </div>
                                </td>

                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="col-12 gy-3 mb-3">
                    <div class="row g-3 justify-content-end">
                        <a href="javascript:void(0)" class="col-auto">
                            <button type="button" class="btn btn-phoenix-danger px-5" data-bs-toggle="tooltip"
                                data-bs-placement="right" data-bs-dismiss="offcanvas">
                                Cancel
                            </button>
                        </a>
                        <div class="col-auto">
                            <button class="btn btn-primary px-5 px-sm-15" id="submit_btn">Save</button>
                        </div>
                    </div>
                </div>
                {{-- <div class="btn-container">
                            <a id="addNew">
                                <i class="fas fa-plus-circle text-success"></i>
                            </a>
                        </div> --}}
            </div>
        </div>
        </form>
        <!-- FORM END -->
    </div>
</div>
</div>

<!--========== Start of add multiple class with ajax ==============-->

<script type="text/javascript">
    // $(document).ready(function() {
    function initializeFlatpickr(line) {
        console.log("Initializing flatpickr for line: " + line);
        flatpickr(".datetimepicker_" + line, {
            dateFormat: "d/m/Y",
            disableMobile: true,
            monthSelectorType: 'static',
            allowInput: true,

        });
    }

    var SmartAuto = (function() {
        var addBtn, html, rowCount, tableBody;

        addBtn = $("#addNew")
        rowCount = $("#add_item tbody tr").length + 1;
        tableBody = $("#add_item tbody");

        function formHtml() {
            html = '    <tr id="row_' + rowCount + '">'
            html += '        <th id="delete_' + rowCount + '" scope="row" class="delete_row">'
            html += '             <i class="fas fa-minus-circle text-danger"></i>'
            html += '        </th>'

            html += '<td>'
            html += '     <div class="input-group mb-3">'
            html += '      <div class="form-floating">'
            html += '        <select name="prohibited_item_id[]"'
            html += '           class="form-select form-select-sm item_selected" '
            html += '           id="prohibited_item_id_' + rowCount + '"'
            html += '           required>'
            html += '        <option selected="selected" value="">Select...</option>'
            html += '        @foreach ($prohibitedItems as $key => $item)'
            html += '            <option value="{{ $item->id }}">'
            html += '                {{ $item->item_name }}'
            html += '            </option>'
            html += '        @endforeach'
            html += '        </select>'
            html += '        <div class="invalid-feedback">'
            html += '          Please select item.'
            html += '        </div>'
            html += '        <label for="add_prohibited_item">Item Category</label>'
            html += '      </div>'
            html += '     </div>'
            html += '</td>'



            html += '<td>'
            html += '        <div class="mb-3 text-start">'
            html += '           <div class="form-floating">'
            html +=
                '               <textarea name="item_description[]" class="form-control" id="add_item_description' +
                rowCount + '" placeholder="Item Description" style="height: 45px" required="" 0=""></textarea>'
            html += '               <label for="add_item_description">Item Description</label>'
            html += '          </div>'
            html += '       </div>'
            html += '</td>'
            html += '<td>'
            html += '       <div class="mb-3 text-start">'
            html += '           <div class="form-icon-container mb-3">'
            html +=
                '               <input class="form-control form-icon-input" name="file_name[]" type="file" id="item_image_' +
                rowCount + '" />'
            html += '               <span class="fas fa-file text-body fs-9 form-icon"></span>'
            html += '           </div>'
            html += '        </div>'
            html += '</td>'

            rowCount++;
            return html;
        }

        function getId(element) {
            var id, idArr;
            id = element.attr('id');
            idArr = id.split("_");
            return idArr[idArr.length - 1]
        }

        function addNewRow() {
            tableBody.append(formHtml());
            initializeFlatpickr(rowCount - 1);
        }

        function deleteRow() {
            var currentEle, rowNo;
            currentEle = $(this);
            rowNo = getId(currentEle);
            $("#row_" + rowNo).remove();
        }


        function registerEvents() {
            // console.log('inside registerEvents')
            addBtn.on("click", addNewRow);
            $(document).on("click", ".delete_row", deleteRow);
        }

        function init() {
            registerEvents();
        }

        return {
            init: init
        }
    })();

    $(document).ready(function() {
        SmartAuto.init();
    });
</script>