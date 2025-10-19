<!DOCTYPE html>
<html data-navigation-type="default" data-navbar-horizontal-shape="default" lang="en-US" dir="ltr">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- ===============================================-->
    <!--    Document Title-->
    <!-- ===============================================-->
    <title>Event Selection</title>

    <!-- ===============================================-->
    <!--    Favicons-->
    <!-- ===============================================-->
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('assets/img/favicons/apple-touch-icon.png') }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('assets/img/favicons/favicon-32x32.png') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('assets/img/favicons/favicon-16x16.png') }}">
    <link rel="shortcut icon" type="image/x-icon" href="{{ asset('assets/img/favicons/favicon.ico') }}">
    <link rel="manifest" href="{{ asset('assets/img/favicons/manifest.json') }}">
    <meta name="msapplication-TileImage" content="{{ asset('assets/img/favicons/mstile-150x150.png') }}">
    <meta name="theme-color" content="#ffffff">
    <script src="{{ asset('assets/vendors/imagesloaded/imagesloaded.pkgd.min.js') }}"></script>
    <script src="{{ asset('fnx/vendors/simplebar/simplebar.min.js') }}"></script>
    <script src="{{ asset('assets/js/config.js') }}"></script>

    <!-- ===============================================-->
    <!--    Stylesheets-->
    <!-- ===============================================-->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin="">
    <link href="https://fonts.googleapis.com/css2?family=Nunito+Sans:wght@300;400;600;700;800;900&amp;display=swap"
        rel="stylesheet">
    <link href="{{ asset('fnx/vendors/simplebar/simplebar.min.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="https://unicons.iconscout.com/release/v4.0.8/css/line.css">
    <link href="{{ asset('fnx/assets/css/theme-rtl.min.css') }}" type="text/css" rel="stylesheet" id="style-rtl">
    <link href="{{ asset('fnx/assets/css/theme.min.css') }}" type="text/css" rel="stylesheet" id="style-default">
    <link href="{{ asset('fnx/assets/css/user-rtl.min.css') }}" type="text/css" rel="stylesheet" id="user-style-rtl">
    <link href="{{ asset('fnx/assets/css/user.min.css') }}" type="text/css" rel="stylesheet" id="user-style-default">
    <link href="{{ asset('assets/css/pace.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/css/branding.css') }}" rel="stylesheet">
    <script>
        var phoenixIsRTL = window.config.config.phoenixIsRTL;
        if (phoenixIsRTL) {
            var linkDefault = document.getElementById('style-default');
            var userLinkDefault = document.getElementById('user-style-default');
            linkDefault.setAttribute('disabled', true);
            userLinkDefault.setAttribute('disabled', true);
            document.querySelector('html').setAttribute('dir', 'rtl');
        } else {
            var linkRTL = document.getElementById('style-rtl');
            var userLinkRTL = document.getElementById('user-style-rtl');
            linkRTL.setAttribute('disabled', true);
            userLinkRTL.setAttribute('disabled', true);
        }
    </script>
</head>

<body>

    <!-- ===============================================-->
    <!--    Main Content-->
    <!-- ===============================================-->
    <main class="main" id="top">
        <div class="px-3">
            <div class="row min-vh-100 flex-center p-5">
                <div class="col-12 col-xl-10 col-xxl-8">
                    <div class="row justify-content-center align-items-center g-5">
                        @if (count($errors) > 0)
                        <div class="alert alert-danger">
                            <ul>
                                @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                        @endif
                        {{-- <div class="col-12 col-lg-6 text-center order-lg-1"><img
                                    class="img-fluid w-lg-100 d-dark-none"
                                    src="{{ asset('assets/img/spot-illustrations/404-illustration.png') }}"
                        alt="" width="400" /><img class="img-fluid w-md-50 w-lg-100 d-light-none"
                            src="../../assets/img/spot-illustrations/dark_404-illustration.png" alt="" width="540" />
                    </div> --}}
                    <div class="col-12 col-lg-6 text-center text-lg-start">
                        {{-- <img class="img-fluid mb-6 w-50 w-lg-75 d-dark-none"
                                    src="{{ asset('assets/img/spot-illustrations/1.png') }}" alt="" />
                        <img class="img-fluid mb-6 w-50 w-lg-75 d-light-none"
                            src="../../assets/img/spot-illustrations/dark_404.png" alt="" /> --}}
                        <h2 class="text-white fw-bolder mb-3">Choose an Event and Venue!</h2>
                        <p class="text-white mb-2 fw-bold">Please choose an event and venue from the list below.
                        </p>
                        <!-- @php
                                    $user_events = auth()->user()->events;
                                    $user_events = $user_events->where('active_flag', 1)->sortBy('name');
                                    $user_venues = auth()->user()->venues;
                                    $user_venues = $user_venues->where('active_flag', 1)->sortBy('title');
                                    // $user_events = App\Models\Setting\Event::where('active
                                @endphp -->

                        @php
                        use App\Models\Setting\Event;
                        use App\Models\Setting\Venue;

                        $user_events = Event::where('active_flag', 1)->orderBy('name')->get();
                        // $user_events = auth()->user()->events;
                        // $user_events = $user_events->where('active_flag', 1)->sortBy('name');
                        $user_venues = Venue::where('active_flag', 1)->orderBy('title')->get();
                        @endphp

                        {{-- @hasrole('xxx') --}}
                        <div data-list='{"valueNames":["title"]}'>
                            <form class="position-relative" action="{{ route('chl.admin.event.switch') }}"
                                method="POST" id="choose-event-form">
                                @csrf
                                <select class="form-select mb-3" name="event_id" required>
                                    <option value="" selected>Select ..</option>
                                    @foreach ($user_events as $event)
                                    <option value="{{ $event->id }}">{{ $event->name }}
                                    </option>
                                    @endforeach
                                </select>
                                {{-- <select class="form-select mb-3" name="venue_id" required>
                                    <option value="" selected>Select ..</option>
                                    @foreach ($user_venues as $venue)
                                    <option value="{{ $venue->id }}">{{ $venue->title }}
                                    </option>
                                    @endforeach
                                </select> --}}
                                <button class="btn btn-subtle-primary w-100 mb-3" id="choose-event-btn" type="submit">
                                    <span id="choose-event-text">Choose Event</span>
                                    <span id="choose-event-spinner"
                                        class="spinner-border spinner-border-sm ms-2 d-none"></span>
                                </button>
                            </form>
                        </div>
                        <div class="d-flex justify-content-center mt-3">
                            <form action="{{ route('logout') }}" method="POST">
                                @csrf
                                <button type="submit" class="btn btn-sm btn-purple text-white">
                                    Logout
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        </div>
        <script>
            var navbarTopStyle = window.config.config.phoenixNavbarTopStyle;
            var navbarTop = document.querySelector('.navbar-top');
            if (navbarTopStyle === 'darker') {
                navbarTop.setAttribute('data-navbar-appearance', 'darker');
            }

            var navbarVerticalStyle = window.config.config.phoenixNavbarVerticalStyle;
            var navbarVertical = document.querySelector('.navbar-vertical');
            if (navbarVertical && navbarVerticalStyle === 'darker') {
                navbarVertical.setAttribute('data-navbar-appearance', 'darker');
            }

            document.getElementById('choose-event-form').addEventListener('submit', function() {
                // document.getElementById('choose-event-text').textContent = 'Choosing event...';
                document.getElementById('choose-event-text').textContent = '';
                document.getElementById('choose-event-spinner').classList.remove('d-none');
                document.getElementById('choose-event-btn').disabled = true;
            });
        </script>

    </main>
    <!-- ===============================================-->
    <!--    End of Main Content-->
    <!-- ===============================================-->

    <!-- ===============================================-->
    <!--    JavaScripts-->
    <!-- ===============================================-->
    <script src="{{ asset('assets/vendors/popper/popper.min.js') }}"></script>
    <script src="{{ asset('assets/vendors/bootstrap/bootstrap.min.js') }}"></script>
    <script src="{{ asset('assets/vendors/anchorjs/anchor.min.js') }}"></script>
    <script src="{{ asset('assets/vendors/is/is.min.js') }}"></script>
    <script src="{{ asset('assets/vendors/fontawesome/all.min.js') }}"></script>
    <script src="{{ asset('assets/vendors/lodash/lodash.min.js') }}"></script>
    <script src="{{ asset('assets/vendors/list.js/list.min.js') }}"></script>
    <script src="{{ asset('assets/vendors/feather-icons/feather.min.js') }}"></script>
    <script src="{{ asset('assets/vendors/dayjs/dayjs.min.js') }}"></script>
    <script src="{{ asset('fnx/assets/js/phoenix.js') }}"></script>
    <script src="{{ asset('assets/js/pace.min.js') }}"></script>

</body>

</html>