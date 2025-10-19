@extends('hr.layout.template')
@section('main')
    <div class="container">
        <div class="row flex-center min-vh-100 py-5">
            <div class="col-sm-10 col-md-8 col-lg-5 col-xxl-4">
                <div class="card shadow-sm">
                    <div class="card-body p-4 p-sm-5">
                        <div class="px-xxl-5">
                            <div class="text-center mb-6">
                                <h4 class="text-body-highlight">Enter the verification code</h4>
                                <p class="text-body-tertiary mb-2">An email containing a 6-digit verification code has been
                                    sent to your email.</p>
                                @if (count($errors) > 0)
                                    <div class="alert alert-danger">
                                        <ul>
                                            @foreach ($errors->all() as $error)
                                                <li>{{ $error }}</li>
                                            @endforeach
                                        </ul>
                                    </div>
                                @endif
                                <form class="verification-form" data-2fa-form="tracki-2fa-form" method="POST"
                                    action="{{ route('auth.otp.post') }}">
                                    @csrf

                                    <div class="d-flex align-items-center gap-2 mb-3">
                                        <input class="form-control pe-6 text-center" name="otp" id="otp"
                                            type="text" placeholder="OTP" />

                                    </div>

                                    <button class="btn btn-primary w-100 mb-5" type="submit">Verify</button>
                                    <a class="fs-9" href="{{ route('otp.resend.get') }}">Didn’t receive the code? </a>
                                    <br>
                                    <a class="fs-9" href="{{ route('auth.login') }}">Back to login page </a>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('script')
@endpush
