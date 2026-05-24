@extends('layouts.frontbase')

@section('content')

@include('frontend.includes.page-header', ['pageKey' => 'booking'])

@php
    $selectedChannel = old('fulfillment_choice', $selectedChannel ?? '');
    $channelLabels = [
        'direct_pay' => 'Book directly',
        'whatsapp' => 'Book through WhatsApp',
        'email' => 'Book through email',
        'booking_com' => 'Booking.com',
        'expedia' => 'Expedia',
        'emerging_travel' => 'Emerging Travel Group',
    ];
@endphp

<section class="ma-room-booking py-100 rpy-70 bg-white rel z-1">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-xl-10 col-lg-11">
                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $err)
                                <li>{{ $err }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                @if (session('info'))
                    <div class="alert alert-info">{{ session('info') }}</div>
                @endif

                <div id="rb-step-channels" class="ma-room-booking__step {{ $selectedChannel ? 'd-none' : '' }}">
                    <h2 class="h4 mb-3">How would you like to book?</h2>
                    @include('frontend.includes.booking-channels-grid', ['compact' => true])
                    <p class="small text-muted mt-3 mb-0">After you pick an option above, you will complete your dates and contact details on the next screen.</p>
                </div>

                <form method="post" action="{{ route('room.booking.store') }}" class="ma-room-booking__form border rounded-3 p-4 p-md-4 bg-light shadow-sm {{ $selectedChannel ? '' : 'd-none' }}" id="rb-step-form">
                    @csrf
                    <input type="hidden" name="fulfillment_choice" id="rb-fulfillment-hidden" value="{{ $selectedChannel }}">

                    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-4">
                        <div>
                            <span class="text-muted small d-block">Booking via</span>
                            <strong id="rb-channel-label">{{ $channelLabels[$selectedChannel] ?? '' }}</strong>
                        </div>
                        <button type="button" class="btn btn-sm btn-outline-secondary" id="rb-change-channel">Change option</button>
                    </div>

                    <h3 class="h5 mb-3">Stay details</h3>
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label class="form-label" for="rb-room">Room <span class="text-muted fw-normal">(optional)</span></label>
                            <select class="form-select ma-room-booking__select" id="rb-room" name="room_id">
                                <option value="">Let the hotel suggest a room</option>
                                @foreach ($rooms as $r)
                                    <option value="{{ $r->id }}" @selected((int) ($selectedRoomId ?? 0) === (int) $r->id)>
                                        {{ $r->roomName }} — {{ \App\Support\Currency::formatRoomPriceLabel($r->price) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label" for="rb-checkin">Check-in</label>
                            <input type="date" class="form-control ma-room-booking__control" id="rb-checkin" name="check_in" value="{{ old('check_in') }}" required>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label" for="rb-checkout">Check-out</label>
                            <input type="date" class="form-control ma-room-booking__control" id="rb-checkout" name="check_out" value="{{ old('check_out') }}" required>
                        </div>
                        <div class="col-12">
                            <p class="form-label mb-2">Airport transfers <span class="text-muted fw-normal small">(optional)</span></p>
                            <div class="row g-2 ma-room-booking__airport-row">
                                <div class="col-md-6">
                                    <label class="ma-rb-airport">
                                        <input class="ma-rb-airport__input" type="checkbox" value="1" name="airport_pickup" @checked(old('airport_pickup'))>
                                        <span class="ma-rb-airport__box">
                                            <span class="ma-rb-airport__check" aria-hidden="true"></span>
                                            <span class="ma-rb-airport__text">
                                                <span class="ma-rb-airport__title">Airport pickup</span>
                                                <span class="ma-rb-airport__hint">Collection at Kigali International Airport</span>
                                            </span>
                                        </span>
                                    </label>
                                </div>
                                <div class="col-md-6">
                                    <label class="ma-rb-airport">
                                        <input class="ma-rb-airport__input" type="checkbox" value="1" name="airport_dropoff" @checked(old('airport_dropoff'))>
                                        <span class="ma-rb-airport__box">
                                            <span class="ma-rb-airport__check" aria-hidden="true"></span>
                                            <span class="ma-rb-airport__text">
                                                <span class="ma-rb-airport__title">Airport drop-off</span>
                                                <span class="ma-rb-airport__hint">Return transfer to the airport</span>
                                            </span>
                                        </span>
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="col-12">
                            <label class="form-label" for="rb-extra">Additional requests</label>
                            <textarea class="form-control" id="rb-extra" name="additional_requests" rows="3" placeholder="Flight number, late arrival, celebration, accessibility…">{{ old('additional_requests') }}</textarea>
                        </div>
                    </div>

                    <h3 class="h5 mb-3">Your contact</h3>
                    <div class="row g-3 mb-4 ma-room-booking__contact">
                        <div class="col-md-6">
                            <label class="form-label small mb-1" for="rb-name">Full name</label>
                            <input type="text" class="form-control form-control-sm ma-room-booking__control" id="rb-name" name="guest_name" value="{{ old('guest_name') }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small mb-1" for="rb-phone">Phone</label>
                            <input type="text" class="form-control form-control-sm ma-room-booking__control" id="rb-phone" name="guest_phone" value="{{ old('guest_phone') }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small mb-1" for="rb-email">Email</label>
                            <input type="email" class="form-control form-control-sm ma-room-booking__control" id="rb-email" name="guest_email" value="{{ old('guest_email') }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small mb-1" for="rb-country">Country</label>
                            <input type="text" class="form-control form-control-sm ma-room-booking__control" id="rb-country" name="guest_country" value="{{ old('guest_country') }}" required>
                        </div>
                    </div>

                    <div class="d-flex flex-wrap gap-2 justify-content-between align-items-center pt-2">
                        <p class="small text-muted mb-0">By continuing you agree your request may be stored for hotel operations.</p>
                        <button type="submit" class="theme-btn">Submit booking request <i class="far fa-angle-right ms-2"></i></button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>

@include('frontend.includes.youtube-stories-widget', ['variant' => 'cream'])

<script>
(function () {
    var labels = @json($channelLabels);
    var stepChannels = document.getElementById('rb-step-channels');
    var stepForm = document.getElementById('rb-step-form');
    var hidden = document.getElementById('rb-fulfillment-hidden');
    var labelEl = document.getElementById('rb-channel-label');
    var changeBtn = document.getElementById('rb-change-channel');

    function showForm(channel) {
        if (!channel || !labels[channel]) return;
        hidden.value = channel;
        labelEl.textContent = labels[channel];
        stepChannels.classList.add('d-none');
        stepForm.classList.remove('d-none');
        try {
            var u = new URL(window.location.href);
            u.searchParams.set('channel', channel);
            window.history.replaceState({}, '', u);
        } catch (e) {}
    }

    document.querySelectorAll('.ma-book-channels a[href*="channel="]').forEach(function (a) {
        a.addEventListener('click', function (e) {
            var m = a.href.match(/channel=([a-z_]+)/);
            if (!m) return;
            e.preventDefault();
            showForm(m[1]);
        });
    });

    if (changeBtn) {
        changeBtn.addEventListener('click', function () {
            stepForm.classList.add('d-none');
            stepChannels.classList.remove('d-none');
            hidden.value = '';
        });
    }

    function initFromQuery() {
        var p = new URLSearchParams(window.location.search);
        var ch = p.get('channel') || hidden.value;
        if (ch && labels[ch]) showForm(ch);
    }

    document.addEventListener('ma:spa-content', initFromQuery);
    initFromQuery();
})();
</script>
@endsection
