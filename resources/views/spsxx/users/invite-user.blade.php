@extends('sps.admin.layout.template')
@section('main')

    <div class="d-flex justify-content-between m-2">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb breadcrumb-style1">
                    <li class="breadcrumb-item">
                        <a href="{{ route('home') }}"><?= get_label('home', 'Home') ?></a>
                    </li>
                    <li class="breadcrumb-item"><a href="javascript:void(0);">
                            <?= get_label('invite_user', 'Invite User') ?></a>
                    </li>
                    <li class="breadcrumb-item active">
                        <?= get_label('save', 'Save') ?>
                    </li>
                </ol>
            </nav>
        </div>
    </div>

    <div class="container">
        @if (session('message'))
            <div class="alert">{{ session('message') }}</div>
        @endif
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <div class="card shadow-none border my-4 col-md-8" style="margin:0 auto;" data-component-card="data-component-card">
            <div class="card-header p-4 border-bottom bg-body">
                <div class="row g-3 justify-content-between align-items-center">
                    <div class="col-12 col-md">
                        <h4 class="text-body mb-0" data-anchor="data-anchor">Invite New User</h4>
                    </div>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="p-4 code-to-copy">
                    <form class="row g-3  px-3 needs-validation" action="{{ route('sps.admin.users.invite.send') }}"
                        id="form-1" novalidate method="POST">
                        @csrf
                        <input type="hidden" id="add_schedule_period_id" name="schedule_period_id" value="">
                        <input type="hidden" id="add_booking_date" name="booking_date">

                        <x-formy.form_input floating="0" name="name" elementId="add_name"
                            classLabel="col-sm-3 col-form-label-sm" label="Name" inputType="text"
                            inputValue="{{ old('name') }}" class="row mt-2" inputWrappingClass="col-sm-8"
                            required="required" disabled="" />

                        <x-formy.form_input floating="0" name="email" elementId="add_email"
                            classLabel="col-sm-3 col-form-label-sm" label="Email" inputType="text"
                            inputValue="{{ old('email') }}" class="row mt-2" inputWrappingClass="col-sm-8"
                            required="required" disabled="" />

                        <!-- <div class="invisible">.</div> -->
                        <div class="col-12 d-flex justify-content-end mt-6">
                            <button class="btn btn-primary" type="submit">Send Invite</button>
                        </div>
                        <!-- <button class="btn btn-primary" type="submit">Submit</button> -->
                    </form>
                </div>
            </div>
            <!-- <br /> -->
            <!-- &nbsp; -->
        </div>
    </div>
@endsection
