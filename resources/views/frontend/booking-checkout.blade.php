@extends('layouts.frontbase')

@section('content')

@include('frontend.includes.page-header', ['pageKey' => 'booking', 'title' => 'Confirm booking'])

@php
    $termsUrl = route('terms');
@endphp

<section class="ma-stay-checkout py-80 rpy-60 bg-white rel z-1">
    <div class="container">
        <div class="ma-stay-checkout__top d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
            <a href="{{ route('home') }}" class="text-muted small"><i class="fas fa-arrow-left me-1"></i> Back to home</a>
            <nav class="ma-stay-checkout__steps small" aria-label="Booking progress">
                <span class="text-muted">1. Your cart</span>
                <span class="mx-2">›</span>
                <span class="fw-bold text-success">2. Confirm booking</span>
            </nav>
        </div>

        @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach ($errors->all() as $err)
                        <li>{{ $err }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="post" action="{{ route('booking.checkout.store') }}" id="stay-checkout-form" class="ma-stay-checkout__grid">
            @csrf
            <input type="hidden" name="cart_json" id="stay-checkout-cart-json" value="{{ old('cart_json') }}">

            <div class="ma-stay-checkout__main">
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-body p-4">
                        <h2 class="h4 mb-1">Primary guest details</h2>
                        <p class="small text-muted mb-4">All fields marked with <span class="text-danger">*</span> are required.</p>

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label" for="guest_first_name">First name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="guest_first_name" name="guest_first_name" value="{{ old('guest_first_name') }}" required maxlength="120">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label" for="guest_last_name">Last name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="guest_last_name" name="guest_last_name" value="{{ old('guest_last_name') }}" required maxlength="120">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label" for="guest_phone">Mobile number (WhatsApp) <span class="text-danger">*</span></label>
                                <input type="tel" class="form-control" id="guest_phone" name="guest_phone" value="{{ old('guest_phone') }}" required maxlength="64" placeholder="+250 7XX XXX XXX" autocomplete="tel">
                                <div class="form-text">Use the number you use on WhatsApp — required for pay-at-hotel reservations.</div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label" for="guest_email">Email <span class="text-danger">*</span></label>
                                <input type="email" class="form-control" id="guest_email" name="guest_email" value="{{ old('guest_email') }}" required maxlength="255" autocomplete="email">
                            </div>
                            <div class="col-12">
                                <label class="form-label" for="guest_country">Country / region <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="guest_country" name="guest_country" value="{{ old('guest_country') }}" required maxlength="120">
                            </div>
                        </div>

                        <details class="mt-4">
                            <summary class="fw-semibold cursor-pointer">Special requests</summary>
                            <div class="mt-3">
                                <div class="row g-2 mb-3">
                                    <div class="col-md-6">
                                        <label class="form-check">
                                            <input class="form-check-input" type="checkbox" name="airport_pickup" value="1" @checked(old('airport_pickup'))>
                                            <span class="form-check-label">Airport pickup</span>
                                        </label>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-check">
                                            <input class="form-check-input" type="checkbox" name="airport_dropoff" value="1" @checked(old('airport_dropoff'))>
                                            <span class="form-check-label">Airport drop-off</span>
                                        </label>
                                    </div>
                                </div>
                                <textarea class="form-control" name="additional_requests" rows="3" placeholder="Dietary needs, late arrival, experience preferences…">{{ old('additional_requests') }}</textarea>
                            </div>
                        </details>
                    </div>
                </div>

                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-body p-4">
                        <h2 class="h5 mb-3">Select payment method</h2>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="ma-stay-pay-option border rounded-3 p-3 d-block h-100">
                                    <input type="radio" name="payment_method" value="pay_now" class="form-check-input me-2" @checked(old('payment_method', 'pay_now') === 'pay_now') required>
                                    <strong>Pay now</strong>
                                    <p class="small text-muted mb-0 mt-1">Secure online payment — confirm your reservation instantly.</p>
                                </label>
                            </div>
                            <div class="col-md-6">
                                <label class="ma-stay-pay-option border rounded-3 p-3 d-block h-100 {{ (!$hotelWhatsappReady && !$hotelEmailReady) ? 'opacity-50' : '' }}">
                                    <input type="radio" name="payment_method" value="pay_at_hotel" class="form-check-input me-2" @checked(old('payment_method') === 'pay_at_hotel') @disabled(!$hotelWhatsappReady && !$hotelEmailReady)>
                                    <strong>Pay at hotel</strong>
                                    <p class="small text-muted mb-0 mt-1">Reserve now and pay on arrival. Send your request via WhatsApp or email.</p>
                                </label>
                            </div>
                        </div>

                        <div id="pay-at-hotel-channels" class="mt-4 {{ old('payment_method') === 'pay_at_hotel' ? '' : 'd-none' }}">
                            <p class="small fw-semibold mb-2">How should we receive your reservation?</p>
                            <div class="d-flex flex-wrap gap-3">
                                @if ($hotelWhatsappReady)
                                    <label class="ma-stay-pay-option border rounded-3 px-3 py-2">
                                        <input type="radio" name="pay_at_hotel_channel" value="whatsapp" class="form-check-input me-2" @checked(old('pay_at_hotel_channel') === 'whatsapp')>
                                        <i class="fab fa-whatsapp text-success"></i> WhatsApp
                                    </label>
                                @endif
                                @if ($hotelEmailReady)
                                    <label class="ma-stay-pay-option border rounded-3 px-3 py-2">
                                        <input type="radio" name="pay_at_hotel_channel" value="email" class="form-check-input me-2" @checked(old('pay_at_hotel_channel') === 'email')>
                                        <i class="fas fa-envelope"></i> Email
                                    </label>
                                @endif
                            </div>
                            <p class="small text-muted mt-2 mb-0">Your mobile number and email must be correct — we use them to confirm pay-at-hotel bookings.</p>
                        </div>
                    </div>
                </div>

                <div class="form-check mb-4">
                    <input class="form-check-input" type="checkbox" name="terms_accepted" value="1" id="terms_accepted" @checked(old('terms_accepted')) required>
                    <label class="form-check-label small" for="terms_accepted">
                        I have read and agree to the <a href="{{ $termsUrl }}" target="_blank" rel="noopener">Hotel Policy and Terms &amp; Conditions</a>.
                    </label>
                </div>

                <button type="submit" class="theme-btn btn-lg w-100 ma-stay-checkout__submit" id="stay-checkout-submit">
                    Confirm booking
                </button>
            </div>

            <aside class="ma-stay-checkout__aside">
                <div class="ma-stay-summary card border-0 shadow-sm sticky-top">
                    <div class="card-header bg-white border-bottom py-3">
                        <h2 class="h5 mb-0">Your stay summary</h2>
                    </div>
                    <div class="card-body p-0">
                        <div id="checkout-summary-lines" class="ma-stay-summary__lines"></div>
                        <p class="small text-muted px-3 py-3 mb-0 d-none" id="checkout-summary-empty">
                            Your cart is empty.
                            <a href="{{ route('rooms') }}">Browse rooms</a> or
                            <a href="{{ route('experiences') }}">add experiences</a>.
                        </p>
                    </div>
                    <div class="card-footer bg-light border-top">
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="text-muted small" id="checkout-pay-label">Estimated total</span>
                            <strong class="fs-4" id="checkout-summary-total">$0.00</strong>
                        </div>
                    </div>
                </div>

                @if ($rooms->isNotEmpty())
                <div class="card border-0 shadow-sm mt-3">
                    <div class="card-body p-3">
                        <p class="small fw-semibold mb-2">Quick add a room</p>
                        <select class="form-select form-select-sm mb-2" id="checkout-quick-room">
                            <option value="">Choose a room…</option>
                            @foreach ($rooms as $r)
                                <option value="{{ $r->id }}"
                                    data-slug="{{ $r->slug }}"
                                    data-name="{{ $r->roomName }}"
                                    data-price="{{ $r->price }}"
                                    data-image="{{ $r->image ? asset('storage/images/rooms/'.$r->image) : '' }}"
                                    @selected($prefillRoom && (int) $prefillRoom->id === (int) $r->id)>
                                    {{ $r->roomName }}
                                </option>
                            @endforeach
                        </select>
                        <button type="button" class="btn btn-sm btn-outline-success w-100" id="checkout-quick-add-room">Add room with dates…</button>
                    </div>
                </div>
                @endif
            </aside>
        </form>
    </div>
</section>

@endsection

@section('scripts')
<script>
(function () {
    function initCheckout() {
        if (!window.IsangeStayCart) return;

        @if (session('clear_stay_cart'))
            IsangeStayCart.clear();
        @endif

        @if (old('cart_json'))
        try {
            localStorage.setItem('isange_stay_cart', @json(old('cart_json')));
        } catch (e) {}
        @endif

        @if ($prefillRoom)
        (function prefillOnce() {
            var key = 'isange_prefill_room_{{ $prefillRoom->id }}';
            if (sessionStorage.getItem(key)) return;
            var btn = document.querySelector('[data-add-room][data-room-id="{{ $prefillRoom->id }}"]');
            if (btn) btn.click();
            else {
                var sel = document.getElementById('checkout-quick-room');
                if (sel) {
                    sel.value = '{{ $prefillRoom->id }}';
                    document.getElementById('checkout-quick-add-room')?.click();
                }
            }
            sessionStorage.setItem(key, '1');
        })();
        @endif

        var form = document.getElementById('stay-checkout-form');
        var cartInput = document.getElementById('stay-checkout-cart-json');
        var linesEl = document.getElementById('checkout-summary-lines');
        var emptyEl = document.getElementById('checkout-summary-empty');
        var totalEl = document.getElementById('checkout-summary-total');
        var payLabel = document.getElementById('checkout-pay-label');
        var payAtHotel = document.getElementById('pay-at-hotel-channels');
        var submitBtn = document.getElementById('stay-checkout-submit');

        function formatMoney(n) {
            return '$' + (Number(n) || 0).toFixed(2);
        }

        function renderSummary() {
            var cart = IsangeStayCart.get();
            if (cartInput) {
                cartInput.value = JSON.stringify(cart);
            }

            if (!linesEl) return;

            linesEl.innerHTML = '';
            var hasItems = cart.rooms.length + cart.experiences.length > 0;

            if (emptyEl) {
                emptyEl.classList.toggle('d-none', hasItems);
            }

            cart.rooms.forEach(function (room, idx) {
                var nights = room.nights || 1;
                var lineTotal = (parseFloat(String(room.price || '').replace(/[^0-9.]/g, '')) || 0) * nights;
                var el = document.createElement('div');
                el.className = 'ma-stay-summary__line px-3 py-3 border-bottom';
                el.innerHTML =
                    '<div class="d-flex justify-content-between gap-2">' +
                    '<div><strong>' + (room.name || 'Room') + '</strong>' +
                    '<div class="small text-muted">' + (room.check_in || '') + ' → ' + (room.check_out || '') + '</div>' +
                    '<div class="small">' + nights + ' night(s) · ' + (room.adults || 1) + ' adult(s), ' + (room.children || 0) + ' child(ren)</div>' +
                    '</div>' +
                    '<div class="text-end text-nowrap">' +
                    '<button type="button" class="btn btn-link btn-sm text-danger p-0 mb-1" data-rm-room="' + idx + '">Remove</button>' +
                    (lineTotal > 0 ? '<div class="fw-semibold">' + formatMoney(lineTotal) + '</div>' : '') +
                    '</div></div>';
                linesEl.appendChild(el);
            });

            cart.experiences.forEach(function (exp) {
                var el = document.createElement('div');
                el.className = 'ma-stay-summary__line px-3 py-3 border-bottom';
                el.innerHTML =
                    '<div class="d-flex justify-content-between gap-2">' +
                    '<div><strong><i class="fas ' + (exp.icon || 'fa-star') + ' me-1 text-warning"></i>' + (exp.title || '') + '</strong>' +
                    '<div class="small text-muted">Experience interest — pricing on request</div></div>' +
                    '<button type="button" class="btn btn-link btn-sm text-danger p-0" data-rm-exp="' + exp.id + '">Remove</button>' +
                    '</div>';
                linesEl.appendChild(el);
            });

            linesEl.querySelectorAll('[data-rm-room]').forEach(function (btn) {
                btn.addEventListener('click', function () {
                    IsangeStayCart.removeRoom(parseInt(btn.getAttribute('data-rm-room'), 10));
                });
            });
            linesEl.querySelectorAll('[data-rm-exp]').forEach(function (btn) {
                btn.addEventListener('click', function () {
                    IsangeStayCart.removeExperience(btn.getAttribute('data-rm-exp'));
                });
            });

            var total = IsangeStayCart.estimateTotalUsd();
            if (totalEl) totalEl.textContent = formatMoney(total);

            var payNow = form && form.querySelector('input[name="payment_method"]:checked');
            if (payLabel) {
                payLabel.textContent = payNow && payNow.value === 'pay_now' ? 'Pay now' : 'Estimated total';
            }
            if (submitBtn) {
                submitBtn.textContent = payNow && payNow.value === 'pay_now' ? 'Confirm & pay now' : 'Confirm booking';
            }
        }

        IsangeStayCart.onChange(renderSummary);
        renderSummary();

        form.querySelectorAll('input[name="payment_method"]').forEach(function (radio) {
            radio.addEventListener('change', function () {
                if (payAtHotel) {
                    payAtHotel.classList.toggle('d-none', radio.value !== 'pay_at_hotel');
                }
                renderSummary();
            });
        });

        form.addEventListener('submit', function (e) {
            if (!IsangeStayCart.hasItems()) {
                e.preventDefault();
                alert('Please add at least one room or experience to your cart.');
                return;
            }
            cartInput.value = IsangeStayCart.toJson();

            var payMethod = form.querySelector('input[name="payment_method"]:checked');
            if (payMethod && payMethod.value === 'pay_at_hotel') {
                var channel = form.querySelector('input[name="pay_at_hotel_channel"]:checked');
                if (!channel) {
                    e.preventDefault();
                    alert('Choose WhatsApp or email to send your pay-at-hotel reservation.');
                    return;
                }
                var phone = (form.querySelector('[name="guest_phone"]') || {}).value || '';
                var email = (form.querySelector('[name="guest_email"]') || {}).value || '';
                if (phone.replace(/\D/g, '').length < 8) {
                    e.preventDefault();
                    alert('Enter a valid WhatsApp mobile number.');
                    return;
                }
                if (!email || email.indexOf('@') < 1) {
                    e.preventDefault();
                    alert('Enter a valid email address.');
                    return;
                }
            }
        });

        var quickAdd = document.getElementById('checkout-quick-add-room');
        if (quickAdd) {
            quickAdd.addEventListener('click', function () {
                var sel = document.getElementById('checkout-quick-room');
                var opt = sel && sel.options[sel.selectedIndex];
                if (!opt || !opt.value) {
                    alert('Choose a room first.');
                    return;
                }
                var fake = document.createElement('button');
                fake.setAttribute('data-add-room', '1');
                fake.setAttribute('data-room-id', opt.value);
                fake.setAttribute('data-room-slug', opt.getAttribute('data-slug') || '');
                fake.setAttribute('data-room-name', opt.getAttribute('data-name') || '');
                fake.setAttribute('data-room-price', opt.getAttribute('data-price') || '');
                fake.setAttribute('data-room-image', opt.getAttribute('data-image') || '');
                document.body.appendChild(fake);
                fake.click();
                fake.remove();
            });
        }

        var today = new Date().toISOString().slice(0, 10);
        document.querySelectorAll('#stay-ar-checkin, #stay-ar-checkout').forEach(function (el) {
            el.setAttribute('min', today);
        });
    }

    document.addEventListener('DOMContentLoaded', initCheckout);
    document.addEventListener('ma:spa-content', initCheckout);
})();
</script>
@endsection
