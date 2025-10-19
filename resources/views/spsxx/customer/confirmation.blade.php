@extends('sps.customer.layout.template')
@section('main')
    {{-- @php
        // Log::info('ProfileController confirmation called');
        // $profile = Crypt::decrypt($profile);
        // Log::info('Decrypted profile: ' . json_encode($profile));
        // // $profile = Crypt::decrypt($profile);
        // $profile = new \App\Models\Sps\Profile::find($profile->id);
    @endphp --}}
    <div class="container text-center">

        <div class="row flex-center min-vh-100 py-5">

            <div class="col-sm-10 col-md-8 col-lg-5 col-xl-5 col-xxl-3"><a
                    class="d-flex flex-center text-decoration-none mb-4" href="../../../index.html">
                    {{-- <div class="d-flex align-items-center fw-bolder fs-3 d-inline-block"><img src="../../../assets/img/icons/logo.png" alt="phoenix" width="58" />
                    </div> --}}
                </a>

                <div class="card shadow-sm">
                    <div class="card-body p-4 p-sm-5">
                        <div class="text-center mb-4">
                            <h3 class="text-body-highlight">SPS</h3>
                            <p class="text-body-tertiary">Your details.</p>
                        </div>
                        <div class="card p-4">
                            <h3>{{ $profile->first_name }} {{ $profile->last_name }}</h3>
                            <p class="fs-9 text-body-tertiary fw-bold mb-1 mt-2 white-space-nowrap">
                                <strong>Refernce#:</strong> {{ $profile->ref_number ?? $profile->id }}</p>
                            <p class="fs-9 text-body-tertiary fw-bold mb-1 white-space-nowrap"><strong>Phone:</strong>
                                {{ $profile->phone }}</p>
                            <p class="fs-9 text-body-tertiary fw-bold mb-1 white-space-nowrap"><strong>email:</strong>
                                {{ $profile->email_address }}</p>
                            <p class="fs-9 text-body-tertiary fw-bold mb-1 white-space-nowrap"><strong># of items:</strong>
                                {{ $profile->items->count() }}</p>

                            <div class="mt-4">

                                {{-- {!! QrCode::size(200)->generate($profile->ref_number) !!} --}}

                                <img src="data:image/png;base64,{{ $qrBase64 }}" alt="QR Code" width="200">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    @endsection

    @push('script')
    @endpush
