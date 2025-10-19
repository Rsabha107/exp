@extends('sps.admin.layout.template')
@section('main')
    {{-- <div class="container"> --}}
    <form method="POST" action="{{ route('sps.admin.get') }}" class="forms-sample needs-validation" novalidate=""
        id="visitor_form">
        @csrf
        <!-- <input type="hidden" name="venue" value="LUS"> -->
        <div class="row flex-center">

            <div class="col-sm-10 col-md-8 col-lg-10 col-xl-10 col-xxl-12">
                <div class="card shadow-sm mb-3">
                    <div class="card-body p-4 p-sm-5">
                        <div class="text-center mb-1">
                            <h3 class="text-body-highlight">SPS</h3>
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
                        <div class="mb-3 text-start">
                            <label class="form-label" for="email">Reference Number</label>
                            <div class="form-icon-container">
                                <input class="form-control form-icon-input" name="ref_number" type="text"
                                    id="find_ref_number" required /><span
                                    class="fas fa-user text-body fs-9 form-icon"></span>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary w-100 mb-0" id="submitBtn">Find</button>
                    </div>
                    <div class="row flex-between-center mb-5">
                        <div class="col-auto">

                        </div>
                        {{-- <div class="col-auto">
                            <a class="fs-9 fw-semibold me-5" href="{{ route('sps.admin') }}">Back to Admin</a>
                        </div> --}}
                    </div>
                </div>

                <div id="visitor-stored-item-content">

                </div>
            </div>
        </div>
    </form>
    {{-- </div> --}}

    @include('sps.modals.storage_modals')
    <script src="{{ asset('assets/js/pages/sps/find.js') }}"></script>

@endsection

@push('script')
@endpush
