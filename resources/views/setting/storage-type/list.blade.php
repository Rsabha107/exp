@extends('chl.admin.layout.template')
@section('main')
    <!-- ===============================================-->
    <!--    Main Content-->
    <!-- ===============================================-->

    <div class="d-flex justify-content-between m-2">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb breadcrumb-style1">
                    <li class="breadcrumb-item">
                        <a href="{{ route('home') }}"><?= get_label('home', 'Home') ?></a>
                    </li>
                    <li class="breadcrumb-item active">
                        <?= get_label('storage_type', 'Storage Types') ?>
                    </li>
                </ol>
            </nav>
        </div>
        <div>
            <x-button_insert_modal bstitle='Add Storage Type' bstarget="#create_storage_type_modal" />
            <!-- <a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#create_locations_modal"><button type="button" class="btn btn-sm btn-primary" data-bs-toggle="tooltip" data-bs-placement="right" data-bs-original-title=" <?= get_label('create_location', 'Create Venue') ?>"><i class="bx bx-plus"></i></button></a> -->
        </div>
    </div>
    <x-settings.storage-type-card />

    {{-- @include('setting.modals.storage_type_modals') --}}
    <script src="{{ asset('assets/js/pages/setting/storage_type.js') }}"></script>
@endsection

@push('script')
@endpush
