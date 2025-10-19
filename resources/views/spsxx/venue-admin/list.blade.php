@extends('sps.venue-admin.layout.template')
@section('main')
    <!-- ===============================================-->
    <!--    Main Content-->
    <!-- ===============================================-->

    <!-- <div class="content"> -->
    <!-- <div class="container-fluid"> -->
    @php
        $current_event_id = session()->get('EVENT_ID');
        $event = App\Models\Setting\Event::find($current_event_id);
        $current_venue_id = session()->get('VENUE_ID');
        $venue = App\Models\Setting\Venue::find($current_venue_id);
        $user_venues = auth()->user()->venues->where('active_flag', 1)->sortBy('name');
    @endphp

    <div class="d-flex justify-content-between m-2">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb breadcrumb-style1">
                    <li class="breadcrumb-item"><a href="#">Home</a></li>
                    <li class="breadcrumb-item active"> {{ optional(auth()->user())->name }} </li>
                    <li class="breadcrumb-item active"> {{ optional($event)->name }} </li>
                    <li class="breadcrumb-item active"> {{ optional($venue)->title }} </li>

                    <li class="nav-item dropdown ms-2 me-2">
                        <a href="#" style="min-width: 2.25rem" role="button" data-bs-toggle="dropdown"
                            aria-haspopup="true" aria-expanded="false" data-bs-auto-close="outside"><span class="d-block"
                                style="height:20px;width:20px;"><i class="fa-solid fa-repeat"></i></span></a>
                        <div class="dropdown-menu dropdown-menu-end notification-dropdown-menu py-0 shadow border navbar-dropdown-caret"
                            id="navbarDropdownNotfication" aria-labelledby="navbarDropdownNotfication">
                            <div class="card position-relative border-0">
                                <div class="card-header p-2">
                                    <div class="d-flex justify-content-between">
                                        <h5 class="text-body-emphasis mb-0">Venues</h5>
                                        {{-- <button class="btn btn-secondary p-0 fs-9 fw-normal" type="button">Switch to another event</button> --}}
                                    </div>
                                </div>
                                <div class="card-body p-0">
                                    <div class="scrollbar-overlay" style="height: 27rem;">
                                        @foreach ($user_venues as $venue)
                                            @if (session()->get('VENUE_ID') == $venue->id)
                                                @php
                                                    $avatar_status = 'status-online';
                                                    $read = 'read';
                                                @endphp
                                            @else
                                                @php
                                                    $avatar_status = '';
                                                    $read = 'unread';
                                                @endphp
                                            @endif
                                            <a href="{{ route('sps.venue.admin.venue.switch', $venue->id) }}"
                                                class="text-decoration-none text-body-emphasis">
                                                <div
                                                    class="px-2 px-sm-3 py-3 notification-card position-relative {{ $read }} border-bottom">
                                                    <div
                                                        class="d-flex align-items-center justify-content-between position-relative">
                                                        <div class="d-flex">
                                                            <div class="avatar avatar-m {{ $avatar_status }} me-3">
                                                                <img class="rounded-circle"
                                                                    src="/storage/event/logo/{{ $venue->event_logo ? $venue->event_logo : 'default.png' }}"
                                                                    alt="" />
                                                            </div>
                                                            <div class="flex-1 me-sm-3">
                                                                <h4 class="fs-9 text-body-emphasis">{{ $venue?->title }}
                                                                </h4>
                                                            </div>
                                                        </div>

                                                    </div>
                                                </div>
                                            </a>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>
                    </li>
                    <li class="breadcrumb-item active"> {{ optional(auth()->user())->storageType?->title }} </li>

                </ol>
            </nav>
        </div>
        <div>

            <x-formy.button_insert_js title='Add Items' selectionId="offcanvas-add-stored-item" dataId="0"
                table="storage_table" icon="fa-solid fa-plus" />

            <button class="btn px-3 btn-phoenix-secondary" type="button" data-bs-toggle="offcanvas"
                data-bs-target="#bookingFilterOffcanvas" aria-haspopup="true" aria-expanded="false"
                data-bs-reference="parent"><svg class="svg-inline--fa fa-filter text-primary" data-fa-transform="down-3"
                    aria-hidden="true" focusable="false" data-prefix="fas" data-icon="filter" role="img"
                    xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" data-fa-i2svg=""
                    style="transform-origin: 0.5em 0.6875em;">
                    <g transform="translate(256 256)">
                        <g transform="translate(0, 96)  scale(1, 1)  rotate(0 0 0)">
                            <path fill="currentColor"
                                d="M3.9 54.9C10.5 40.9 24.5 32 40 32H472c15.5 0 29.5 8.9 36.1 22.9s4.6 30.5-5.2 42.5L320 320.9V448c0 12.1-6.8 23.2-17.7 28.6s-23.8 4.3-33.5-3l-64-48c-8.1-6-12.8-15.5-12.8-25.6V320.9L9 97.3C-.7 85.4-2.8 68.8 3.9 54.9z"
                                transform="translate(-256 -256)"></path>
                        </g>
                    </g>
                </svg><!-- <span class="fa-solid fa-filter text-primary" data-fa-transform="down-3"></span> Font Awesome fontawesome.com -->
            </button>
        </div>
    </div>

    @include('sps.venue-admin.modals.storage_modals')

    <x-sps.venue-admin.storage-card />
    <!-- </div> -->

    <script src="{{ asset('assets/js/pages/sps/venue-admin/items.js') }}"></script>
@endsection

@push('script')
@endpush
