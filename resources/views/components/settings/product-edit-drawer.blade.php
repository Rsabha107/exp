<div class="offcanvas-body">
    <div class="row">
        <div class="col-sm-12">
            <form class="row g-3 needs-validation form-submit-event" id="{{ $formId }}" novalidate=""
                action="{{ $formAction }}" method="POST">
                @csrf
                <input type="hidden" id="edit_id" name="id" value="" />
                <input type="hidden" id="edit_table" name="table" value="products_table" />
                <div class="card">
                    <div class="card-header d-flex align-items-center border-bottom">
                        <div class="ms-3">
                            <h5 class="mb-0 fs-sm">Edit Booking Schedule</h5>
                        </div>
                    </div>
                    <div class="card-body">
                        <x-formy.form_input class="col-sm-6 col-md-12 mb-3" floating="1" inputValue=""
                            name="product_name" elementId="edit_product_name" inputType="text" inputAttributes=""
                            label="Product Name" required="required" disabled="0" />

                        <x-formy.form_input class="col-sm-6 col-md-12 mb-3" floating="1" inputValue="0"
                            name="product_price" elementId="edit_product_price" inputType="number" inputAttributes=""
                            label="Product Price" required="required" disabled="0" />

                        <x-formy.form_select class="col-sm-6 col-md-12 mb-3" floating="1" selectedValue=""
                            name="unit_type_id" elementId="edit_unit_type_id" label="Unit" required="required"
                            :forLoopCollection="$unitTypes" itemIdForeach="id" itemTitleForeach="title" style=""
                            addDynamicButton="0" />

                        <x-formy.form_textarea class="col-sm-12 col-md-12 mb-3" floating="1" inputValue=""
                            name="product_description" elementId="edit_product_description" label="Description"
                            required="" disabled="0" />


                        <div class="text-center mb-3">
                            <div class="mb-3 text-start">
                                <input type="file" name="file_name" id="edit_file_name" data-height="200"
                                    data-default-file="" />
                            </div>
                        </div>

                        <div class="col-12 gy-3">
                            <div class="row g-3 justify-content-end">
                                <a href="javascript:void(0)" class="col-auto">
                                    <button type="button" class="btn btn-phoenix-danger px-5"
                                        data-bs-toggle="tooltip" data-bs-placement="right"
                                        data-bs-dismiss="offcanvas">
                                        Cancel
                                    </button>
                                </a>
                                <div class="col-auto">
                                    <button class="btn btn-primary px-5 px-sm-15" id="submit_btn">Create
                                        Project</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
