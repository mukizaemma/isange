@extends('layouts.frontbase')

@section('body_class', 'is-checkout-page')

@section('content')

@include('frontend.includes.page-header', ['pageKey' => 'booking', 'title' => 'Confirm booking'])

@php
    $termsUrl = route('terms');
    $prefillPayAtHotelChannel = $prefillPayAtHotelChannel ?? (in_array(request('channel'), ['whatsapp', 'email'], true) ? request('channel') : null);
@endphp

<section class="ma-stay-checkout py-80 rpy-60">
    <div class="container">
        <div class="ma-stay-checkout__top d-flex flex-wrap justify-content-between align-items-center gap-3 mb-3">
            <a href="{{ route('home') }}" class="text-muted small"><i class="fas fa-arrow-left me-1"></i> Back to home</a>
        </div>

        @include('frontend.includes.booking-benefits')

        <div id="checkout-flow">

        <nav class="ma-checkout-wizard mb-4" id="checkout-wizard" aria-label="Booking steps">
            <ol class="ma-checkout-wizard__list">
                <li class="ma-checkout-wizard__item is-active" data-wizard-step="1">
                    <button type="button" class="ma-checkout-wizard__btn" data-goto-step="1">
                        <span class="ma-checkout-wizard__num">1</span>
                        <span class="ma-checkout-wizard__label">Stay</span>
                    </button>
                </li>
                <li class="ma-checkout-wizard__item" data-wizard-step="2">
                    <button type="button" class="ma-checkout-wizard__btn" data-goto-step="2">
                        <span class="ma-checkout-wizard__num">2</span>
                        <span class="ma-checkout-wizard__label">Guest</span>
                    </button>
                </li>
                <li class="ma-checkout-wizard__item" data-wizard-step="3">
                    <button type="button" class="ma-checkout-wizard__btn" data-goto-step="3">
                        <span class="ma-checkout-wizard__num">3</span>
                        <span class="ma-checkout-wizard__label">Confirm</span>
                    </button>
                </li>
            </ol>
        </nav>

        @if ($errors->any())
            <div class="alert alert-danger ma-stay-checkout__alert">
                <ul class="mb-0">
                    @foreach ($errors->all() as $err)
                        <li>{{ $err }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="post" action="{{ route('booking.checkout.store') }}" id="stay-checkout-form" class="ma-stay-checkout__grid" novalidate>
            @csrf
            <input type="hidden" name="cart_json" id="stay-checkout-cart-json" value="{{ old('cart_json') }}">
            <input type="hidden" name="payment_method" value="pay_at_hotel">

            <div class="ma-stay-checkout__main">

                {{-- Step 1: Your stay --}}
                <div class="ma-checkout-step is-active" data-checkout-step="1" id="checkout-step-1">
                <div class="ma-checkout-card mb-0" id="checkout-stay-dates-card">
                    <div class="ma-checkout-card__head">
                        <span class="ma-checkout-card__icon" aria-hidden="true"><i class="fas fa-calendar-check"></i></span>
                        <div>
                            <h2 class="ma-checkout-card__title">Your stay</h2>
                            <p class="ma-checkout-card__lead">Set your dates and guests, then choose a room to continue.</p>
                        </div>
                    </div>
                    <div class="ma-checkout-card__body">
                        <div class="row g-2 g-md-3 ma-checkout-stay-row">
                            <div class="col-6 col-md-3">
                                <label class="form-label" for="stay_check_in">Check-in <span class="text-danger">*</span></label>
                                <input type="date" class="form-control ma-checkout-input" id="stay_check_in" required>
                            </div>
                            <div class="col-6 col-md-3">
                                <label class="form-label" for="stay_check_out">Check-out <span class="text-danger">*</span></label>
                                <input type="date" class="form-control ma-checkout-input" id="stay_check_out" required>
                            </div>
                            <div class="col-4 col-md-2">
                                <label class="form-label" for="stay_adults">Adults <span class="text-danger">*</span></label>
                                <input type="number" class="form-control ma-checkout-input" id="stay_adults" min="1" max="20" value="2" required>
                            </div>
                            <div class="col-4 col-md-2">
                                <label class="form-label" for="stay_children">Children</label>
                                <input type="number" class="form-control ma-checkout-input" id="stay_children" min="0" max="20" value="0">
                            </div>
                            <div class="col-4 col-md-2">
                                <label class="form-label" for="stay_rooms_count">Rooms <span class="text-danger">*</span></label>
                                <input type="number" class="form-control ma-checkout-input" id="stay_rooms_count" min="1" max="10" value="1" required>
                            </div>
                        </div>

                        <div class="ma-checkout-stay-nights mt-2 d-none" id="stay-nights-badge">
                            <i class="fas fa-moon" aria-hidden="true"></i>
                            <span id="stay-nights-text">0 nights</span>
                        </div>

                        <div class="ma-checkout-cart-block mt-3">
                            <div id="checkout-step1-cart-items" class="ma-checkout-cart-items" aria-live="polite">
                                <p class="small text-muted mb-0" id="checkout-step1-empty">No room selected yet — tap “Add room” below to choose your accommodation.</p>
                            </div>
                            <div class="ma-checkout-add-actions mt-2">
                                <button type="button" class="ma-checkout-add-action" id="checkout-open-room-modal">
                                    <i class="fas fa-bed" aria-hidden="true"></i>
                                    <span>Add room</span>
                                </button>
                                <button type="button" class="ma-checkout-add-action ma-checkout-add-action--exp" id="checkout-open-exp-modal">
                                    <i class="fas fa-hiking" aria-hidden="true"></i>
                                    <span>Add experience</span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                </div>{{-- step 1 --}}

                {{-- Step 2: Guest --}}
                <div class="ma-checkout-step" data-checkout-step="2" id="checkout-step-2">
                <div class="ma-checkout-card mb-0">
                    <div class="ma-checkout-card__head">
                        <span class="ma-checkout-card__icon" aria-hidden="true"><i class="fas fa-user"></i></span>
                        <div>
                            <h2 class="ma-checkout-card__title">Primary guest</h2>
                            <p class="ma-checkout-card__lead">We will use these details to confirm your reservation.</p>
                        </div>
                    </div>
                    <div class="ma-checkout-card__body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label" for="guest_first_name">First name</label>
                                <input type="text" class="form-control ma-checkout-input" id="guest_first_name" name="guest_first_name" value="{{ old('guest_first_name') }}" maxlength="120">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label" for="guest_last_name">Last name</label>
                                <input type="text" class="form-control ma-checkout-input" id="guest_last_name" name="guest_last_name" value="{{ old('guest_last_name') }}" maxlength="120">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label" for="guest_phone">Mobile (WhatsApp) <span class="text-danger" id="guest_phone-required">*</span></label>
                                <input type="tel" class="form-control ma-checkout-input" id="guest_phone" name="guest_phone" value="{{ old('guest_phone') }}" maxlength="64" placeholder="+250 7XX XXX XXX" autocomplete="tel">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label" for="guest_email">Email <span class="text-danger" id="guest_email-required">*</span></label>
                                <input type="email" class="form-control ma-checkout-input" id="guest_email" name="guest_email" value="{{ old('guest_email') }}" maxlength="255" autocomplete="email">
                            </div>
                            <div class="col-12">
                                <label class="form-label" for="guest_country">Country / region</label>
                                <input type="text" class="form-control ma-checkout-input" id="guest_country" name="guest_country" value="{{ old('guest_country') }}" maxlength="120">
                            </div>
                        </div>

                        <details class="ma-checkout-details mt-4">
                            <summary>Special requests</summary>
                            <div class="mt-3">
                                <div class="row g-2 mb-3">
                                    <div class="col-md-6">
                                        <label class="ma-stay-inline-check">
                                            <input type="checkbox" name="airport_pickup" value="1" @checked(old('airport_pickup'))>
                                            <span class="ma-stay-inline-check__box" aria-hidden="true"></span>
                                            <span>Airport pickup</span>
                                        </label>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="ma-stay-inline-check">
                                            <input type="checkbox" name="airport_dropoff" value="1" @checked(old('airport_dropoff'))>
                                            <span class="ma-stay-inline-check__box" aria-hidden="true"></span>
                                            <span>Airport drop-off</span>
                                        </label>
                                    </div>
                                </div>
                                <textarea class="form-control ma-checkout-input" name="additional_requests" rows="3" placeholder="Dietary needs, late arrival, experience preferences…">{{ old('additional_requests') }}</textarea>
                            </div>
                        </details>
                    </div>
                </div>
                </div>{{-- step 2 --}}

                <div class="ma-checkout-step" data-checkout-step="3" id="checkout-step-3">
                    <div class="ma-checkout-card mb-3">
                        <div class="ma-checkout-card__head">
                            <span class="ma-checkout-card__icon" aria-hidden="true"><i class="fas fa-clipboard-check"></i></span>
                            <p class="ma-checkout-card__title mb-0">Review &amp; confirm</p>
                        </div>
                        <div class="ma-checkout-card__body">
                            <div id="checkout-review-summary" class="ma-checkout-review mb-4"></div>

                            <div class="ma-checkout-confirm-channel mb-4">
                                <h3 class="h6 mb-2">How should we send your confirmation?</h3>
                                @if (! $hotelWhatsappReady && ! $hotelEmailReady)
                                    <div class="alert alert-warning mb-0">
                                        Reservation by WhatsApp or email is not available right now. Please contact the hotel directly.
                                    </div>
                                @else
                                    <div id="pay-at-hotel-channels" class="ma-stay-pay-panel ma-stay-pay-panel--hotel">
                                        <div class="ma-channel-choices d-flex flex-wrap gap-3" role="radiogroup" aria-label="Reservation channel">
                                            @if ($hotelWhatsappReady)
                                                <label class="ma-channel-choice">
                                                    <input type="radio" name="pay_at_hotel_channel" value="whatsapp" class="ma-channel-choice__input" required @checked(old('pay_at_hotel_channel', $prefillPayAtHotelChannel ?? ($hotelWhatsappReady ? 'whatsapp' : null)) === 'whatsapp')>
                                                    <span class="ma-channel-choice__surface">
                                                        <span class="ma-channel-choice__indicator" aria-hidden="true"></span>
                                                        <i class="fab fa-whatsapp"></i>
                                                        <span>WhatsApp</span>
                                                    </span>
                                                </label>
                                            @endif
                                            @if ($hotelEmailReady)
                                                <label class="ma-channel-choice">
                                                    <input type="radio" name="pay_at_hotel_channel" value="email" class="ma-channel-choice__input" required @checked(old('pay_at_hotel_channel', $prefillPayAtHotelChannel) === 'email')>
                                                    <span class="ma-channel-choice__surface">
                                                        <span class="ma-channel-choice__indicator" aria-hidden="true"></span>
                                                        <i class="fas fa-envelope"></i>
                                                        <span>Email</span>
                                                    </span>
                                                </label>
                                            @endif
                                        </div>
                                        <p class="small text-muted mt-3 mb-0" id="pay-at-hotel-channel-hint">Choose WhatsApp or email — we will use the matching contact from your guest details.</p>
                                    </div>
                                @endif
                            </div>

                            <div class="ma-stay-terms-check mb-0">
                                <label class="ma-stay-terms-check__control" for="terms_accepted">
                                    <input type="checkbox" name="terms_accepted" value="1" id="terms_accepted" @checked(old('terms_accepted')) required>
                                    <span class="ma-stay-terms-check__box" aria-hidden="true"></span>
                                </label>
                                <span class="ma-stay-terms-check__label">
                                    I have read and agree to the
                                    <a href="{{ $termsUrl }}" target="_blank" rel="noopener noreferrer">Hotel Policy and Terms &amp; Conditions</a>.
                                </span>
                            </div>
                        </div>
                    </div>
                    <x-human-form-fields class="mb-3" />
                    <button type="submit" class="theme-btn btn-lg w-100 ma-stay-checkout__submit" id="stay-checkout-submit">
                        Confirm booking
                    </button>
                </div>

            </div>

            <aside class="ma-stay-checkout__aside">
                <div class="ma-stay-summary ma-checkout-summary card border-0 shadow-sm">
                    <div class="card-header ma-checkout-summary__head border-0">
                        <h2 class="h5 mb-0">Your stay summary</h2>
                    </div>
                    <div class="card-body p-0">
                        <div id="checkout-summary-meta" class="ma-checkout-summary__meta px-3 pt-3 d-none"></div>
                        <div id="checkout-summary-lines" class="ma-stay-summary__lines"></div>
                        <div id="checkout-summary-picks" class="ma-checkout-summary__picks px-3 py-3">
                            <div class="ma-checkout-summary__pick-actions" id="checkout-summary-pick-actions">
                                <button type="button" class="ma-checkout-summary__pick-btn" id="checkout-summary-add-room">
                                    <i class="fas fa-bed" aria-hidden="true"></i>
                                    <span>Add room</span>
                                </button>
                                <button type="button" class="ma-checkout-summary__pick-btn ma-checkout-summary__pick-btn--exp" id="checkout-summary-add-exp">
                                    <i class="fas fa-hiking" aria-hidden="true"></i>
                                    <span>Add experience</span>
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer ma-checkout-summary__foot border-0">
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="text-muted small" id="checkout-pay-label">Estimated total</span>
                            <strong class="ma-checkout-summary__total" id="checkout-summary-total">$0.00</strong>
                        </div>
                    </div>
                </div>
            </aside>

            <div class="ma-checkout-step-nav ma-stay-checkout__footer" id="checkout-step-nav">
                <button type="button" class="btn btn-outline-secondary ma-checkout-step-nav__back" id="checkout-step-back" disabled>
                    <i class="fas fa-arrow-left me-1"></i> Back
                </button>
                <div class="ma-checkout-step-nav__meta">
                    <span class="ma-checkout-step-nav__total" id="checkout-nav-total">$0.00</span>
                    <span class="ma-checkout-step-nav__step-label" id="checkout-nav-step">Step 1 of 3</span>
                </div>
                <button type="button" class="theme-btn ma-checkout-step-nav__next" id="checkout-step-next">
                    Continue <i class="fas fa-arrow-right ms-1"></i>
                </button>
            </div>
        </form>

        </div>{{-- #checkout-flow --}}
    </div>
</section>

@endsection

@push('body-modals')
@include('frontend.includes.checkout-catalog-modals')
@endpush

@section('scripts')
<script>
(function () {
    window.IsangeCheckout = window.IsangeCheckout || { next: null, back: null };

    function runCheckoutNext(e) {
        if (e) {
            e.preventDefault();
            e.stopPropagation();
        }
        if (typeof window.__checkoutAdvance === 'function') {
            window.__checkoutAdvance();
            return;
        }
        if (typeof window.__checkoutBoot === 'function') {
            window.__checkoutBoot();
        }
        if (typeof window.__checkoutAdvance === 'function') {
            window.__checkoutAdvance();
        } else {
            alert('Checkout is still loading. Please refresh the page and try again.');
        }
    }

    function runCheckoutBack(e) {
        if (e) {
            e.preventDefault();
            e.stopPropagation();
        }
        if (typeof window.__checkoutRetreat === 'function') {
            window.__checkoutRetreat();
            return;
        }
        if (typeof window.__checkoutBoot === 'function') {
            window.__checkoutBoot();
        }
        if (typeof window.__checkoutRetreat === 'function') {
            window.__checkoutRetreat();
        }
    }

    window.__checkoutRunNext = runCheckoutNext;
    window.__checkoutRunBack = runCheckoutBack;

    document.addEventListener('click', function (e) {
        if (!document.getElementById('stay-checkout-form')) {
            return;
        }
        if (e.target.closest('#checkout-step-next, #stay-cart-continue, #stay-cart-continue-modal')) {
            runCheckoutNext(e);
            return;
        }
        if (e.target.closest('#checkout-step-back')) {
            runCheckoutBack(e);
        }
    }, true);

    function initCheckout() {
        if (!window.IsangeStayCart) {
            return;
        }

        var form = document.getElementById('stay-checkout-form');
        if (!form) {
            window.IsangeCheckout.next = null;
            window.IsangeCheckout.back = null;
            return;
        }

        if (form.dataset.checkoutBound === '1') {
            if (typeof window.__checkoutRender === 'function') {
                window.__checkoutRender();
            }
            if (typeof window.__checkoutAdvance === 'function') {
                window.IsangeCheckout.next = window.__checkoutAdvance;
                window.IsangeCheckout.back = window.__checkoutRetreat;
            }
            return;
        }

        try {
        @if (old('cart_json'))
        try {
            var restoredCart = {!! json_encode(old('cart_json')) !!};
            if (typeof restoredCart === 'string' && restoredCart.length) {
                sessionStorage.setItem('isange_stay_cart', restoredCart);
            }
        } catch (e) {}
        @endif

        var cartInput = document.getElementById('stay-checkout-cart-json');
        var linesEl = document.getElementById('checkout-summary-lines');
        var summaryMeta = document.getElementById('checkout-summary-meta');
        var summaryPicks = document.getElementById('checkout-summary-picks');
        var summaryAddRoom = document.getElementById('checkout-summary-add-room');
        var summaryAddExp = document.getElementById('checkout-summary-add-exp');
        var step1CartItems = document.getElementById('checkout-step1-cart-items');
        var step1EmptyEl = document.getElementById('checkout-step1-empty');
        var openRoomModalBtn = document.getElementById('checkout-open-room-modal');
        var openExpModalBtn = document.getElementById('checkout-open-exp-modal');
        var totalEl = document.getElementById('checkout-summary-total');
        var payLabel = document.getElementById('checkout-pay-label');
        var payAtHotel = document.getElementById('pay-at-hotel-channels');
        var submitBtn = document.getElementById('stay-checkout-submit');
        var checkoutFlow = document.getElementById('checkout-flow');
        var nightsBadge = document.getElementById('stay-nights-badge');
        var nightsText = document.getElementById('stay-nights-text');
        var stepBackBtn = document.getElementById('checkout-step-back');
        var stepNextBtn = document.getElementById('checkout-step-next');
        var navTotalEl = document.getElementById('checkout-nav-total');
        var navStepEl = document.getElementById('checkout-nav-step');
        var reviewEl = document.getElementById('checkout-review-summary');
        var wizardItems = document.querySelectorAll('.ma-checkout-wizard__item');
        var wizardBtns = document.querySelectorAll('[data-goto-step]');
        var stepPanels = document.querySelectorAll('[data-checkout-step]');
        var checkoutStepCount = 3;
        var currentStep = 1;
        var maxStepReached = 1;

        function goToStep(step, opts) {
            step = Math.max(1, Math.min(checkoutStepCount, step));
            currentStep = step;
            if (step > maxStepReached) {
                maxStepReached = step;
            }
            stepPanels.forEach(function (panel) {
                var n = parseInt(panel.getAttribute('data-checkout-step'), 10);
                panel.classList.toggle('is-active', n === step);
            });
            wizardItems.forEach(function (item) {
                var n = parseInt(item.getAttribute('data-wizard-step'), 10);
                item.classList.toggle('is-active', n === step);
                item.classList.toggle('is-complete', n < step);
            });
            wizardBtns.forEach(function (btn) {
                var n = parseInt(btn.getAttribute('data-goto-step'), 10);
                btn.disabled = n > maxStepReached;
            });
            if (stepBackBtn) {
                stepBackBtn.disabled = step <= 1;
            }
            if (stepNextBtn) {
                if (step >= checkoutStepCount) {
                    stepNextBtn.classList.add('d-none');
                } else {
                    stepNextBtn.classList.remove('d-none');
                    stepNextBtn.innerHTML = 'Continue <i class="fas fa-arrow-right ms-1"></i>';
                }
            }
            if (navStepEl) {
                navStepEl.textContent = 'Step ' + step + ' of ' + checkoutStepCount;
            }
            if (step === checkoutStepCount) {
                syncPaymentPanels();
                renderReviewSummary();
            }
            var activePanel = document.getElementById('checkout-step-' + step);
            if (activePanel && !(opts && opts.silent)) {
                activePanel.scrollIntoView({ behavior: 'smooth', block: 'start' });
            } else if (step < checkoutStepCount && !(opts && opts.silent)) {
                var footerNav = document.getElementById('checkout-step-nav');
                if (footerNav) {
                    footerNav.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
                }
            }
        }

        function prepareStepOneCart(options) {
            options = options || {};
            pushStayToCart();
            var stay = readStayFields();
            if (!stay.check_in || !stay.check_out || stay.check_out <= stay.check_in) {
                alert('Please choose valid check-in and check-out dates.');
                if (stayCheckIn) {
                    stayCheckIn.focus();
                }
                return false;
            }
            if (options.requireRoom && !IsangeStayCart.hasSelectedRoom()) {
                alert(
                    'Please select a room before continuing.\n\n' +
                    'Tap "Add room" below to choose your accommodation.'
                );
                if (openRoomModalBtn) {
                    openRoomModalBtn.scrollIntoView({ behavior: 'smooth', block: 'center' });
                }
                return false;
            }
            pushStayToCart();
            return true;
        }

        function validateStep(step) {
            if (step === 1) {
                return prepareStepOneCart({ requireRoom: true });
            }
            if (step === 2) {
                return true;
            }
            return true;
        }

        function validateConfirmation() {
            if (!form.querySelector('input[name="pay_at_hotel_channel"]:checked')) {
                alert('Choose WhatsApp or email to send your reservation.');
                return false;
            }
            if (!validateGuestContactForPayment()) {
                return false;
            }
            return true;
        }

        function renderReviewSummary() {
            if (!reviewEl) return;
            var stay = readStayFields();
            var cart = IsangeStayCart.get();
            var payLabelText = 'Pay at hotel';
            var ch = form.querySelector('input[name="pay_at_hotel_channel"]:checked');
            if (ch) payLabelText += ' · ' + (ch.value === 'whatsapp' ? 'WhatsApp' : 'Email');
            var guestName = ((form.querySelector('[name="guest_first_name"]') || {}).value || '') + ' ' +
                ((form.querySelector('[name="guest_last_name"]') || {}).value || '');
            var html = '';
            if (stay.check_in && stay.check_out) {
                html += '<div class="ma-checkout-review__row"><span>Dates</span><strong>' +
                    escapeHtml(stay.check_in) + ' → ' + escapeHtml(stay.check_out) + '</strong></div>';
            }
            html += '<div class="ma-checkout-review__row"><span>Guest</span><strong>' + escapeHtml(guestName.trim()) + '</strong></div>';
            html += '<div class="ma-checkout-review__row"><span>Payment</span><strong>' + escapeHtml(payLabelText) + '</strong></div>';
            html += '<div class="ma-checkout-review__row"><span>Items</span><strong>' +
                cart.rooms.length + ' room(s), ' + cart.experiences.length + ' experience(s)</strong></div>';
            html += '<div class="ma-checkout-review__row"><span>Total</span><strong>' +
                formatMoney(IsangeStayCart.estimateTotalUsd()) + '</strong></div>';
            reviewEl.innerHTML = html;
        }

        var stayCheckIn = document.getElementById('stay_check_in');
        var stayCheckOut = document.getElementById('stay_check_out');
        var stayAdults = document.getElementById('stay_adults');
        var stayChildren = document.getElementById('stay_children');
        var stayRoomsCount = document.getElementById('stay_rooms_count');
        var today = new Date().toISOString().slice(0, 10);

        [stayCheckIn, stayCheckOut].forEach(function (el) {
            if (el) el.setAttribute('min', today);
        });

        function escapeHtml(str) {
            var d = document.createElement('div');
            d.textContent = str || '';
            return d.innerHTML;
        }

        function formatMoney(n) {
            return '$' + (Number(n) || 0).toFixed(2);
        }

        function formatDateShort(iso) {
            if (!iso) return '';
            try {
                return new Date(iso + 'T12:00:00').toLocaleDateString(undefined, { weekday: 'short', day: 'numeric', month: 'short' });
            } catch (e) {
                return iso;
            }
        }

        function readStayFields() {
            return {
                check_in: stayCheckIn ? stayCheckIn.value : '',
                check_out: stayCheckOut ? stayCheckOut.value : '',
                adults: stayAdults ? stayAdults.value : 2,
                children: stayChildren ? stayChildren.value : 0,
                rooms_count: stayRoomsCount ? stayRoomsCount.value : 1,
            };
        }

        function fillStayFields(stay) {
            if (!stay) return;
            var cart = IsangeStayCart.get();
            if ((!stay.check_in || !stay.check_out) && cart.rooms.length > 0) {
                var firstRoom = cart.rooms[0];
                stay = Object.assign({}, stay, {
                    check_in: stay.check_in || firstRoom.check_in || null,
                    check_out: stay.check_out || firstRoom.check_out || null,
                });
            }
            if (stayCheckIn && stay.check_in) stayCheckIn.value = stay.check_in;
            if (stayCheckOut && stay.check_out) stayCheckOut.value = stay.check_out;
            if (stayAdults) stayAdults.value = stay.adults || 2;
            if (stayChildren) stayChildren.value = stay.children || 0;
            if (stayRoomsCount) stayRoomsCount.value = stay.rooms_count || 1;
            updateNightsBadge();
        }

        function updateNightsBadge() {
            if (!nightsBadge || !nightsText) return;
            var ci = stayCheckIn ? stayCheckIn.value : '';
            var co = stayCheckOut ? stayCheckOut.value : '';
            if (!ci || !co || co <= ci) {
                nightsBadge.classList.add('d-none');
                return;
            }
            var a = new Date(ci + 'T12:00:00');
            var b = new Date(co + 'T12:00:00');
            var nights = Math.max(1, Math.round((b - a) / 86400000));
            nightsText.textContent = nights + ' night' + (nights !== 1 ? 's' : '');
            nightsBadge.classList.remove('d-none');
        }

        function pushStayToCart() {
            var partial = readStayFields();
            IsangeStayCart.setStay(partial);
            if (partial.rooms_count) {
                IsangeStayCart.setRoomsCount(partial.rooms_count);
            }
        }

        function getPayAtHotelChannel() {
            var ch = form.querySelector('input[name="pay_at_hotel_channel"]:checked');
            return ch ? ch.value : '';
        }

        function syncGuestContactRequired() {
            var phoneEl = form.querySelector('[name="guest_phone"]');
            var emailEl = form.querySelector('[name="guest_email"]');
            var phoneReq = document.getElementById('guest_phone-required');
            var emailReq = document.getElementById('guest_email-required');
            var hintEl = document.getElementById('pay-at-hotel-channel-hint');
            var channel = getPayAtHotelChannel();
            var needPhone = channel === 'whatsapp';
            var needEmail = channel === 'email';

            if (hintEl) {
                if (channel === 'whatsapp') {
                    hintEl.textContent = 'We will send your reservation confirmation to your WhatsApp number above.';
                } else if (channel === 'email') {
                    hintEl.textContent = 'We will send your reservation confirmation to your email above.';
                } else {
                    hintEl.textContent = 'Choose WhatsApp or email — only that contact detail is required to submit.';
                }
            }

            if (phoneEl) phoneEl.required = needPhone;
            if (emailEl) emailEl.required = needEmail;
            if (phoneReq) phoneReq.classList.toggle('d-none', !needPhone);
            if (emailReq) emailReq.classList.toggle('d-none', !needEmail);
        }

        function validateGuestContactForPayment() {
            var channel = getPayAtHotelChannel();
            var phone = (form.querySelector('[name="guest_phone"]') || {}).value || '';
            var email = (form.querySelector('[name="guest_email"]') || {}).value || '';
            if (channel === 'whatsapp') {
                if (phone.replace(/\D/g, '').length < 8) {
                    alert('Enter a valid WhatsApp mobile number to submit via WhatsApp.');
                    var phoneField = form.querySelector('[name="guest_phone"]');
                    if (phoneField) phoneField.focus();
                    return false;
                }
                return true;
            }
            if (channel === 'email') {
                if (!email || email.indexOf('@') < 1) {
                    alert('Enter a valid email address to submit via email.');
                    var emailField = form.querySelector('[name="guest_email"]');
                    if (emailField) emailField.focus();
                    return false;
                }
                return true;
            }
            return true;
        }

        function syncPaymentPanels() {
            if (payLabel) {
                payLabel.textContent = 'Estimated total';
            }
            if (submitBtn) {
                submitBtn.textContent = 'Confirm booking';
            }

            document.querySelectorAll('#pay-at-hotel-channels input[name="pay_at_hotel_channel"]').forEach(function (inp) {
                inp.required = true;
                inp.disabled = false;
            });

            document.querySelectorAll('.ma-channel-choice').forEach(function (label) {
                var input = label.querySelector('.ma-channel-choice__input');
                label.classList.toggle('ma-channel-choice--selected', input && input.checked);
            });
            syncGuestContactRequired();
        }

        function openCheckoutModal(id) {
            var el = document.getElementById(id);
            if (!el) return;
            if (el.parentElement !== document.body) {
                document.body.appendChild(el);
            }
            if (window.bootstrap && bootstrap.Modal) {
                bootstrap.Modal.getOrCreateInstance(el).show();
            }
        }

        function syncPickModals(cart) {
            var roomIds = cart.rooms.map(function (r) { return r.room_id; });
            var expIds = cart.experiences.map(function (e) { return e.id; });
            document.querySelectorAll('[data-checkout-add-room]').forEach(function (btn) {
                var rid = parseInt(btn.getAttribute('data-room-id'), 10);
                var added = roomIds.indexOf(rid) >= 0;
                btn.classList.toggle('is-added', added);
                btn.disabled = added;
                btn.textContent = added ? 'Added' : 'Add';
            });
            document.querySelectorAll('[data-checkout-add-experience]').forEach(function (btn) {
                var id = btn.getAttribute('data-exp-id');
                var added = expIds.indexOf(id) >= 0;
                btn.classList.toggle('is-added', added);
                btn.disabled = added;
                btn.textContent = added ? 'Added' : 'Add';
            });
        }

        function renderStep1CartItems(cart) {
            if (!step1CartItems) return;
            var realRooms = cart.rooms.filter(function (room) {
                return room && room.room_id;
            });
            var hasItems = realRooms.length + cart.experiences.length > 0;
            if (step1EmptyEl) {
                step1EmptyEl.classList.toggle('d-none', hasItems);
            }
            step1CartItems.querySelectorAll('.ma-checkout-step1-item').forEach(function (el) {
                el.remove();
            });
            if (!hasItems) return;

            realRooms.forEach(function (room) {
                var idx = cart.rooms.indexOf(room);
                var row = document.createElement('div');
                row.className = 'ma-checkout-step1-item';
                row.innerHTML =
                    '<span class="ma-checkout-step1-item__icon" aria-hidden="true"><i class="fas fa-bed"></i></span>' +
                    '<span class="ma-checkout-step1-item__body">' +
                    '<strong class="ma-checkout-step1-item__name">' + escapeHtml(room.name || 'Room') + '</strong>' +
                    (room.check_in && room.check_out
                        ? '<span class="ma-checkout-step1-item__meta">' + escapeHtml(room.check_in) + ' → ' + escapeHtml(room.check_out) + '</span>'
                        : '<span class="ma-checkout-step1-item__meta">Dates set above</span>') +
                    '</span>' +
                    '<button type="button" class="ma-checkout-step1-item__remove" data-step1-rm-room="' + idx + '" aria-label="Remove room">Remove</button>';
                step1CartItems.appendChild(row);
            });

            cart.experiences.forEach(function (exp) {
                var row = document.createElement('div');
                row.className = 'ma-checkout-step1-item ma-checkout-step1-item--exp';
                row.innerHTML =
                    '<span class="ma-checkout-step1-item__icon" aria-hidden="true"><i class="fas ' + escapeHtml(exp.icon || 'fa-star') + '"></i></span>' +
                    '<span class="ma-checkout-step1-item__body">' +
                    '<strong class="ma-checkout-step1-item__name">' + escapeHtml(exp.title || '') + '</strong>' +
                    '<span class="ma-checkout-step1-item__meta">Experience</span>' +
                    '</span>' +
                    '<button type="button" class="ma-checkout-step1-item__remove" data-step1-rm-exp="' + escapeHtml(exp.id) + '" aria-label="Remove experience">Remove</button>';
                step1CartItems.appendChild(row);
            });

            step1CartItems.querySelectorAll('[data-step1-rm-room]').forEach(function (btn) {
                btn.addEventListener('click', function () {
                    IsangeStayCart.removeRoom(parseInt(btn.getAttribute('data-step1-rm-room'), 10));
                });
            });
            step1CartItems.querySelectorAll('[data-step1-rm-exp]').forEach(function (btn) {
                btn.addEventListener('click', function () {
                    IsangeStayCart.removeExperience(btn.getAttribute('data-step1-rm-exp'));
                });
            });

            if (stayRoomsCount && cart.rooms.length > 0) {
                stayRoomsCount.value = cart.rooms.length;
            }
        }

        function renderSummary() {
            var cart = IsangeStayCart.get();
            var stay = IsangeStayCart.getStay();
            fillStayFields(stay);

            if (cartInput) {
                cartInput.value = IsangeStayCart.toJson();
            }

            renderStep1CartItems(cart);
            syncPickModals(cart);

            if (summaryMeta) {
                if (stay.check_in && stay.check_out) {
                    summaryMeta.classList.remove('d-none');
                    summaryMeta.innerHTML =
                        '<p class="ma-checkout-summary__dates mb-0">' +
                        '<strong>' + escapeHtml(formatDateShort(stay.check_in)) + '</strong>' +
                        ' <span class="text-muted">→</span> ' +
                        '<strong>' + escapeHtml(formatDateShort(stay.check_out)) + '</strong>' +
                        (cart.rooms.length ? ' · ' + cart.rooms.length + ' room' + (cart.rooms.length !== 1 ? 's' : '') : '') +
                        '</p>';
                } else {
                    summaryMeta.classList.add('d-none');
                    summaryMeta.innerHTML = '';
                }
            }

            if (!linesEl) return;

            linesEl.innerHTML = '';
            var hasRooms = cart.rooms.length > 0;
            var hasExperiences = cart.experiences.length > 0;
            var hasItems = hasRooms || hasExperiences;

            if (summaryAddRoom) {
                summaryAddRoom.classList.toggle('d-none', hasRooms);
            }
            if (summaryAddExp) {
                summaryAddExp.classList.toggle('d-none', hasExperiences);
            }
            if (summaryPicks) {
                summaryPicks.classList.toggle('d-none', hasRooms && hasExperiences);
            }

            cart.rooms.forEach(function (room, idx) {
                var nights = room.nights || 1;
                var lineTotal = (parseFloat(String(room.price || '').replace(/[^0-9.]/g, '')) || 0) * nights;
                var el = document.createElement('div');
                el.className = 'ma-stay-summary__line px-3 py-3 border-bottom';
                el.innerHTML =
                    '<div class="d-flex justify-content-between gap-2">' +
                    '<div><strong>' + escapeHtml(room.name || 'Room') + '</strong>' +
                    '<div class="small text-muted">' + (room.check_in && room.check_out ? room.check_in + ' → ' + room.check_out : 'Set dates above') + '</div>' +
                    '<div class="small">' + nights + ' night(s) · ' + (room.adults || 1) + ' adult(s), ' + (room.children || 0) + ' child(ren)</div>' +
                    '</div>' +
                    '<div class="text-end text-nowrap">' +
                    '<button type="button" class="btn btn-link btn-sm text-danger p-0 mb-1" data-rm-room="' + idx + '">Remove</button>' +
                    (lineTotal > 0 ? '<div class="fw-semibold">' + formatMoney(lineTotal) + '</div>' : '<div class="small text-muted">Rate on request</div>') +
                    '</div></div>';
                linesEl.appendChild(el);
            });

            cart.experiences.forEach(function (exp) {
                var el = document.createElement('div');
                el.className = 'ma-stay-summary__line px-3 py-3 border-bottom';
                el.innerHTML =
                    '<div class="d-flex justify-content-between gap-2">' +
                    '<div><strong><i class="fas ' + escapeHtml(exp.icon || 'fa-star') + ' me-1 text-warning"></i>' + escapeHtml(exp.title || '') + '</strong>' +
                    '<div class="small text-muted">Experience — we will help arrange</div></div>' +
                    '<button type="button" class="btn btn-link btn-sm text-danger p-0" data-rm-exp="' + escapeHtml(exp.id) + '">Remove</button>' +
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

            if (totalEl) totalEl.textContent = formatMoney(IsangeStayCart.estimateTotalUsd());
            if (navTotalEl) navTotalEl.textContent = formatMoney(IsangeStayCart.estimateTotalUsd());
            syncPaymentPanels();
        }

        window.__checkoutRender = renderSummary;

        [stayCheckIn, stayCheckOut, stayAdults, stayChildren, stayRoomsCount].forEach(function (el) {
            if (!el) return;
            el.addEventListener('change', function () {
                updateNightsBadge();
                pushStayToCart();
            });
        });

        form.querySelectorAll('input[name="pay_at_hotel_channel"]').forEach(function (radio) {
            radio.addEventListener('change', syncPaymentPanels);
        });

        IsangeStayCart.onChange(renderSummary);
        renderSummary();

        if (stayCheckIn && stayCheckOut && !stayCheckIn.value) {
            stayCheckIn.value = today;
            var tomorrow = new Date();
            tomorrow.setDate(tomorrow.getDate() + 1);
            stayCheckOut.value = tomorrow.toISOString().slice(0, 10);
            updateNightsBadge();
            pushStayToCart();
        }

        function advanceCheckoutStep() {
            if (validateStep(currentStep)) {
                goToStep(currentStep + 1);
            }
        }

        function retreatCheckoutStep() {
            if (currentStep > 1) {
                goToStep(currentStep - 1);
            }
        }

        window.__checkoutAdvance = advanceCheckoutStep;
        window.__checkoutRetreat = retreatCheckoutStep;
        window.IsangeCheckout.next = advanceCheckoutStep;
        window.IsangeCheckout.back = retreatCheckoutStep;
        form.dataset.checkoutBound = '1';

        @if ($errors->any())
        goToStep(3, { silent: true });
        @else
        goToStep(1, { silent: true });
        @endif

        if (stepBackBtn) {
            stepBackBtn.addEventListener('click', retreatCheckoutStep);
        }
        if (stepNextBtn) {
            stepNextBtn.addEventListener('click', advanceCheckoutStep);
        }
        wizardBtns.forEach(function (btn) {
            btn.addEventListener('click', function () {
                var target = parseInt(btn.getAttribute('data-goto-step'), 10);
                if (target <= currentStep) {
                    goToStep(target);
                    return;
                }
                var step = currentStep;
                while (step < target) {
                    if (!validateStep(step)) {
                        return;
                    }
                    step += 1;
                }
                goToStep(target);
            });
        });

        form.addEventListener('submit', function (e) {
            if (currentStep < checkoutStepCount) {
                e.preventDefault();
                if (validateStep(currentStep)) {
                    goToStep(currentStep + 1);
                }
                return;
            }
            pushStayToCart();

            if (!prepareStepOneCart({ requireRoom: true })) {
                e.preventDefault();
                return;
            }

            cartInput.value = IsangeStayCart.toJson();

            if (!validateConfirmation()) {
                e.preventDefault();
                return;
            }
            if (!form.querySelector('#terms_accepted:checked')) {
                e.preventDefault();
                alert('Please accept the hotel policy and terms to continue.');
                return;
            }
        });

        if (openRoomModalBtn) {
            openRoomModalBtn.addEventListener('click', function () {
                openCheckoutModal('checkoutPickRoomModal');
            });
        }
        if (openExpModalBtn) {
            openExpModalBtn.addEventListener('click', function () {
                openCheckoutModal('checkoutPickExperienceModal');
            });
        }
        if (summaryAddRoom) {
            summaryAddRoom.addEventListener('click', function () {
                openCheckoutModal('checkoutPickRoomModal');
            });
        }
        if (summaryAddExp) {
            summaryAddExp.addEventListener('click', function () {
                openCheckoutModal('checkoutPickExperienceModal');
            });
        }

        document.querySelectorAll('[data-checkout-add-room]').forEach(function (btn) {
            btn.addEventListener('click', function () {
                if (btn.disabled) return;
                pushStayToCart();
                var added = IsangeStayCart.addRoom({
                    room_id: parseInt(btn.getAttribute('data-room-id'), 10),
                    slug: btn.getAttribute('data-room-slug') || '',
                    name: btn.getAttribute('data-room-name') || 'Room',
                    image: btn.getAttribute('data-room-image') || '',
                    price: btn.getAttribute('data-room-price') || '',
                    check_in: null,
                    check_out: null,
                    adults: stayAdults ? stayAdults.value : 2,
                    children: stayChildren ? stayChildren.value : 0,
                });
                if (added) {
                    var partial = readStayFields();
                    IsangeStayCart.setRoomsCount(partial.rooms_count);
                }
            });
        });

        document.querySelectorAll('[data-checkout-add-experience]').forEach(function (btn) {
            btn.addEventListener('click', function () {
                if (btn.disabled) return;
                IsangeStayCart.addExperience({
                    id: btn.getAttribute('data-exp-id'),
                    title: btn.getAttribute('data-exp-title') || '',
                    icon: btn.getAttribute('data-exp-icon') || 'fa-star',
                });
            });
        });

        @if ($prefillRoom)
        (function prefillOnce() {
            var key = 'isange_prefill_room_{{ $prefillRoom->id }}';
            if (sessionStorage.getItem(key)) return;
            var cart = IsangeStayCart.get();
            if (!cart.rooms.some(function (r) { return r.room_id === {{ $prefillRoom->id }}; })) {
                pushStayToCart();
                IsangeStayCart.addRoom({
                    room_id: {{ $prefillRoom->id }},
                    slug: @json($prefillRoom->slug),
                    name: @json($prefillRoom->roomName),
                    image: @json($prefillRoom->image ? asset('storage/images/rooms/'.$prefillRoom->image) : ''),
                    price: @json($prefillRoom->price ?? ''),
                    check_in: null,
                    check_out: null,
                    adults: stayAdults ? stayAdults.value : 2,
                    children: stayChildren ? stayChildren.value : 0,
                });
            }
            sessionStorage.setItem(key, '1');
        })();
        @endif
        } catch (err) {
            console.error('Checkout init failed:', err);
            form.dataset.checkoutBound = '0';
        }
    }

    window.__checkoutBoot = initCheckout;

    document.addEventListener('DOMContentLoaded', initCheckout);
    document.addEventListener('isange:stay-cart-ready', initCheckout);
    document.addEventListener('ma:spa-content', initCheckout);
    if (document.readyState !== 'loading') {
        initCheckout();
    }
})();
</script>
@endsection
