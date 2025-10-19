@extends('chl.admin.layout.template')
@section('main')


<!-- ===============================================-->
<!--    Main Content-->
<!-- ===============================================-->

{{-- <div class="content"> --}}
{{-- <div class="container-fluid"> --}}
<div class="d-flex justify-content-between m-2">
    <div>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb breadcrumb-style1">
                <li class="breadcrumb-item">
                    <a href="{{route('home')}}"><?= get_label('home', 'Home') ?></a>
                </li>
                <li class="breadcrumb-item active">
                    <?= get_label('category', 'Categories') ?>
                </li>
            </ol>
        </nav>
    </div>
    <div>
        <x-button_insert_modal bstitle='Add Category' bstarget="#create_categories_modal" />
        <!-- <a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#create_categories_modal"><button type="button" class="btn btn-sm btn-primary" data-bs-toggle="tooltip" data-bs-placement="right" data-bs-original-title=" <?= get_label('create_category', 'Create Category') ?>"><i class="bx bx-plus"></i></button></a> -->
    </div>
</div>
<x-settings.task-category />
{{-- </div> --}}

@include('setting.modals.category_modals')

@endsection

@push('script')
<script>
    var label_update = '<?= get_label('update', 'Update') ?>';
    var label_delete = '<?= get_label('delete', 'Delete') ?>';
    var label_not_assigned = '<?= get_label('not_assigned', 'Not assigned') ?>';
    var label_duplicate = '<?= get_label('duplicate', 'Duplicate') ?>';
</script>
<script src="{{asset('assets/js/pages/setting/category.js')}}"></script>

@endpush