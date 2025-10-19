<div class="offcanvas-body">
    <div class="row">
        <div class="col-sm-12">
            <form class="row g-3 needs-validation form-submit-event" id="{{ $formId }}" novalidate=""
                action="{{ $formAction }}" method="POST">
                @csrf
                <input type="hidden" name="table" value="item_master_table" />
                <div>

                    <div class="card">
                        <div class="card-header d-flex align-items-center border-bottom">
                            <div class="ms-3">
                                <h5 class="mb-0 fs-sm">Add Product</h5>
                            </div>
                        </div>
                        <div class="card-body">

                            <div class="row mb-3">
                                <div class="col-sm-6 col-md-6 mb-3">
                                    <div class="form-floating">
                                        <input class="form-control" name="item_name" id="add_item_name" type="text"
                                            placeholder="Product Name" required />
                                        <div class="invalid-feedback">
                                            Please enter Product Name.
                                        </div>
                                        <label for="add_item_name">Product Name <span class="red">*</span></label>
                                    </div>
                                </div>
                                <div class="col-sm-6 col-md-6 mb-3">
                                    <div class="form-floating">
                                        <input class="form-control" name="SKU" id="add_item_sku" type="text"
                                            placeholder="SKU" />
                                        <div class="invalid-feedback">
                                            Please enter SKU.
                                        </div>
                                        <label for="add_item_sku">SKU</label>
                                    </div>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-sm-6 col-md-6  mb-3">
                                    <div class="input-group">
                                        <div class="form-floating">
                                            <select name="item_category_id" class="form-select"
                                                id="add_item_category_id">
                                                <option selected="selected" value="">Select...</option>
                                                @foreach ($categories as $key => $item)
                                                <option value="{{ $item->id }}">
                                                    {{ $item->name }}
                                                </option>
                                                @endforeach
                                            </select>
                                            <label for="add_item_category_id">Category</label>
                                        </div>
                                        <div class="invalid-feedback">
                                            Please select Sub Category.
                                        </div>
                                        <button type="button" class="btn btn-phoenix-primary px-3"
                                            data-bs-toggle="modal" data-bs-target="#add_procurement_category_modal">
                                            <i class="fas fa-plus-circle text-success"></i>
                                        </button>
                                    </div>
                                </div>
                                <div class="col-sm-6 col-md-6  mb-3">
                                    <div class="input-group">
                                        <div class="form-floating ">
                                            <select name="item_subcategory_id" class="form-select"
                                                id="add_item_subcategory_id">
                                                <option selected="selected" value="">Select...</option>
                                                @foreach ($subcategories as $key => $item)
                                                <option value="{{ $item->id }}">
                                                    {{ $item->name }}
                                                </option>
                                                @endforeach
                                            </select>
                                            <label for="item_subcategory_id">Sub Category</label>
                                        </div>
                                        <div class="invalid-feedback">
                                            Please select Sub Category.
                                        </div>
                                        <button type="button" class="btn btn-phoenix-primary px-3"
                                            data-bs-toggle="modal" data-bs-target="#exampleModal">
                                            <i class="fas fa-plus-circle text-success"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-sm-6 col-md-4">
                                    <div class="input-group">
                                        <div class="form-floating">
                                            <select name="unit_type_id" class="form-select" id="add_unit_type_id">
                                                <option selected="selected" value="">Select...</option>
                                                @foreach ($unittypes as $key => $item)
                                                <option value="{{ $item->id }}">
                                                    {{ $item->title }}
                                                </option>
                                                @endforeach
                                            </select>
                                            <label for="add_unit_type_id">Unit Type</label>
                                        </div>
                                        <button type="button" class="btn btn-phoenix-primary px-3"
                                            data-bs-toggle="modal" data-bs-target="#add_procurement_unit_type_modal">
                                            <i class="fas fa-plus-circle text-success"></i>
                                        </button>
                                    </div>
                                </div>
                                <div class="col-sm-6 col-md-4">
                                    <div class="form-group">
                                        <input class="form-control" name="course_image" type="file" id="image">
                                    </div>
                                </div>
                                <div class="col-sm-6 col-md-4 image-display">
                                    <img id="showImage" src="{{ url('upload/no_image.jpg') }}" alt="Admin"
                                        class="rounded-circle p-1 bg-primary" width="100">
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-sm-6 col-md-6 mb-3">
                                    <div class="form-floating">
                                        <input class="form-control" name="item_price" id="add_item_price"
                                            type="text" placeholder="Selling Price" required />
                                        <div class="invalid-feedback">
                                            Please enter Selling Price.
                                        </div>
                                        <label for="add_item_price">Selling Price<span class="red">*</span></label>
                                    </div>
                                </div>
                                <div class="col-sm-6 col-md-6 mb-3">
                                    <div class="form-floating">
                                        <input class="form-control" name="item_cost" id="add_item_cost"
                                            type="text" placeholder="Selling Cost" required />
                                        <div class="invalid-feedback">
                                            Please enter Cost price.
                                        </div>
                                        <label for="add_item_cost">Cost Price<span class="red">*</span></label>
                                    </div>
                                </div>
                            </div>

                            <div class="col-12 gy-3">
                                <div class="form-floating mb-3">
                                    <textarea class="form-control" name="description" id="add_item_description" placeholder="Product Description"
                                        style="height: 100px"></textarea>
                                    <div class="invalid-feedback">
                                        Please enter Description.
                                    </div>
                                    <label for="floatingProjectOverview">Description</label>
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
                </div>
            </form>
        </div>
    </div>
</div>

<script src="{{asset('assets/js/pages/procurement/admin/create_new_category.js')}}"></script>
<script src="{{asset('assets/js/pages/procurement/admin/create_new_unit_type.js')}}"></script>

<script type="text/javascript">
    $(document).ready(function() {
        $('#image').change(function(e) {
            var reader = new FileReader();
            reader.onload = function(e) {
                $('#showImage').attr('src', e.target.result);
            }
            reader.readAsDataURL(e.target.files['0']);
        });
    });
</script>