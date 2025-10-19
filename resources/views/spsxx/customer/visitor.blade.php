@extends('sps.customer.layout.template')
@section('main')

@php
// $params = decryptUrlSafe($encrypted);
// Log::info('ProfileController index called ');
// Log::info('Decrypted data: ' . json_encode($params));
$data = (object) $data;
Log::info('Data: ' . json_encode($data));
@endphp

<div class="container">
    <form method="POST" action="{{ route('visitor.store') }}" class="forms-sample" id="spinner-form"
        enctype="multipart/form-data">
        @csrf
        <input type="hidden" name="event_id" value="{{ $data->e }}">
        <input type="hidden" name="venue_id" value="{{ $data->v }}">
        <input type="hidden" name="location_id" value="{{ $data->l }}">
        <input type="hidden" name="storage_type_id" value="{{ $data->s }}">
        <div class="row flex-center min-vh-100 py-5">

            <div class="col-sm-10 col-md-8 col-lg-5 col-xl-5 col-xxl-3">
                <div class="card shadow-sm">
                    <div class="card-body p-4 p-sm-5">
                        <div class="text-center mb-7">
                            <h3 class="text-body-highlight"><a href="{{ route('sps.admin') }}">SPS</a></h3>
                            <p class="text-body-tertiary">All fields are required.</p>
                        </div>
                        @if (count($errors) > 0)
                        <div class="alert alert-danger">
                            <ul>
                                @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                        @endif
                        <!-- <button class="btn btn-phoenix-secondary w-100 mb-3"><span class="fab fa-google text-danger me-2 fs-9"></span>Sign in with google</button>
                                            <button class="btn btn-phoenix-secondary w-100"><span class="fab fa-facebook text-primary me-2 fs-9"></span>Sign in with facebook</button>
                                            <div class="position-relative">
                                            <hr class="bg-body-secondary mt-5 mb-4" />
                                            <div class="divider-content-center">or use email</div>
                                            </div> -->
                        <div class="mb-3 text-start">
                            <div class="form-floating">
                                <input name="first_name" class="form-control" id="add_first_name" type="text" placeholder="First Name" value="{{ old('first_name') }}" required="">
                                <label for="add_first_name">First Name</label>
                            </div>
                        </div>
                        <!-- <x-formy.form_input class="mb-3 text-start" floating="1" inputValue="{{ old('first_name') }}" name="first_name"
                            elementId="add_first_name" inputType="text" inputAttributes="" label="First Name"
                            required="required" disabled="0" /> -->

                        <x-formy.form_input class="mb-3 text-start" floating="1" inputValue="{{ old('last_name') }}" name="last_name"
                            elementId="add_last_name" inputType="text" inputAttributes="" label="Last Name"
                            required="required" disabled="0" />
                        <x-formy.form_input class="mb-3 text-start" floating="1" inputValue="{{ old('email_address') }}" name="email_address"
                            elementId="add_email_address" inputType="email" inputAttributes="" label="Email"
                            required="required" disabled="0" />
                        <x-formy.form_input class="mb-3 text-start" floating="1" inputValue="{{ old('phone') }}" name="phone"
                            elementId="add_phone" inputType="phone" inputAttributes="" label="Phone Number"
                            required="required" disabled="0" />
                        <div class="mt-2 col-sm-6 col-md-12 mb-3 item-block" id="item_block_1">
                            <hr>
                            <div class="input-group mb-3">
                                <div class="form-floating">
                                    <select name="prohibited_item_id[]"
                                        class="form-select form-select-sm item_selected"
                                        id="prohibited_item_id_1"
                                        required>
                                        <option selected="selected" value="">Select...</option>
                                        @foreach ($prohibitedItems as $key => $item)
                                        <option value="{{ $item->id }}" {{ old('prohibited_item_id.'.$key) == $item->id ? 'selected' : '' }}>
                                            {{ $item->item_name }}
                                        </option>
                                        @endforeach
                                    </select>
                                    <div class="invalid-feedback">
                                        Please select item.
                                    </div>
                                    <label for="add_prohibited_item">Item Category</label>
                                </div>
                            </div>
                            <div class="mb-3 text-start">
                                <div class="form-floating">
                                    <textarea name="item_description[]" class="form-control" id="add_item_description_1" placeholder="Item Description" style="height: 100px" ></textarea>
                                    <label for="add_item_description_1">Item Description</label>
                                </div>
                            </div>
                            <div class="mb-3 text-start">
                                <div class="form-icon-container mb-3">
                                    <input class="form-control form-icon-input" name="file_name[]" type="file" id="item_image_1" />
                                    <span class="fas fa-file text-body fs-9 form-icon"></span>
                                </div>
                            </div>
                            <div id="visitor_items_container" class="mb-3 text-start"></div>
                            <div class="mb-3 text-start">
                                <x-formy.href_insert_js table="x" selectionId="add-item" dataId="0"
                                    title="Add Item" buttonClass="btn btn-subtle-primary px-3 w-100 px-sm-5 "
                                    icon="fa-solid fa-plus me-2" />
                            </div>

                            <button class="btn btn-primary w-100 mb-3" id="save-button">
                                <span id="save-text">Save</span>
                                <span id="save-spinner"
                                    class="spinner-border spinner-border-sm ms-2 d-none"></span>
                            </button>
                        </div>
                    </div>
                </div>
    </form>
</div>

<script>
    // Replace the form page in browser history

    // $('#save-spinner').addClass('d-none');
    // $('#save-text').text('Save');
    // $('#save-button').prop('enabled', true);

    //     document.getElementById('save-form').addEventListener('submit', function() {
    //         // document.getElementById('save-text').textContent = 'Choosing event...';
    //         document.getElementById('save-text').textContent = '';
    //         document.getElementById('save-spinner').classList.remove('d-none');
    //         document.getElementById('save-button').disabled = true;
    //     });

    $("body").on("click", "#add-item", function() {
        console.log('inside add_item')
        var addBtn, html, rowCount, tableBody;
        rowCount = $("#visitor_items_container").children().length + 1;
        console.log("rowCount: " + rowCount);

        // html = '       <hr>'

        html = '    <div class="mt-2 col-sm-6 col-md-12 mb-3 item-block" id="item_block_' + rowCount + '">'
        html += '       <hr>'
        html += '       <div class="input-group mb-3">'
        html += '           <div class="form-floating">'
        html += '               <select name="prohibited_item_id[]"'
        html += '                   class="form-select form-select-sm item_selected" '
        html += '                   id="prohibited_item_id_' + rowCount + '"'
        html += '                   required>'
        html += '                   <option selected="selected" value="">Select...</option>'
        html += '                   @foreach ($prohibitedItems as $key => $item)'
        html += '                       <option value="{{ $item->id }}">'
        html += '                           {{ $item->item_name }}'
        html += '                       </option>'
        html += '                   @endforeach'
        html += '               </select>'
        html += '               <div class="invalid-feedback">'
        html += '                   Please select item.'
        html += '               </div>'
        html += '               <label for="add_prohibited_item">Item Category</label>'
        html += '           </div>'
        html += '       </div>'
        html += '       <div class="mb-3 text-start">'
        html += '           <div class="form-floating">'
        html += '               <textarea name="item_description[]" class="form-control" id="add_item_description' +
            rowCount + '" placeholder="Item Description" style="height: 100px" required=""></textarea>'
        html += '               <label for="add_item_description">Item Description</label>'
        html += '          </div>'
        html += '       </div>'
        html += '       <div class="mb-3 text-start">'
        html += '           <div class="form-icon-container mb-3">'
        html += '               <input class="form-control form-icon-input" name="file_name[]" type="file" id="item_image_' + rowCount + '" />'
        html += '               <span class="fas fa-file text-body fs-9 form-icon"></span>'
        html += '           </div>'
        html += '       </div>'

        html += '       <div class="mb-3 text-start">'
        html += '           <a href="javascript:void(0)" data-table="x" id="remove-item" data-id="0" class="btn btn-subtle-danger w-100 px-sm-5 remove-block">'
        html += '           <span class="fa-solid fa-minus me-sm-2"></span><span class="d-none d-sm-inline">Remove Item</span>'
        html += '           </a>'
        html += '       </div>'
        // html += '       <hr>'
        html += '   </div>'

        // html = '<h5 class="text-body-tertiary">Item ' + rowCount + '</h5>';
        $('#visitor_items_container').append(html);

    });
</script>
{{-- @include('sps.modals.storage_modals') --}}

@endsection

@push('script')
<script src="{{ asset('assets/js/pages/sps/visitor_items.js') }}"></script>
@endpush