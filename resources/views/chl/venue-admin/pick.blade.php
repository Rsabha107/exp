@extends('chl.layout.template')
@section('main')
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

                        // $user_events = Event::where('active_flag', 1)->orderBy('name')->get();
                        $user_events = auth()->user()->events;
                        $user_events = $user_events->where('active_flag', 1)->sortBy('name');

                        $user_venues = auth()->user()->venues;
                        $user_venues = $user_venues->where('active_flag', 1)->sortBy('title');

                        @endphp

                        {{-- @hasrole('xxx') --}}
                        <div data-list='{"valueNames":["title"]}'>
                            <form class="position-relative" action="{{ route('chl.venue.admin.event.switch') }}"
                                method="POST" id="choose-event-form">
                                @csrf
                                <select class="form-select mb-3" name="event_id" required>
                                    <option value="" selected>Select ..</option>
                                    @foreach ($user_events as $event)
                                    <option value="{{ $event->id }}">{{ $event->name }}
                                    </option>
                                    @endforeach
                                </select>
                                <select class="form-select mb-3" name="venue_id" required>
                                    <option value="" selected>Select ..</option>
                                    @foreach ($user_venues as $venue)
                                    <option value="{{ $venue->id }}">{{ $venue->title }}
                                    </option>
                                    @endforeach
                                </select>
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
@endsection
@push('script')
@endpush