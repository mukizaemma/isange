@extends('layouts.frontbase')

@section('content')

@include('frontend.includes.page-header', ['pageKey' => 'booking', 'title' => 'Confirm booking'])

@php
    $termsUrl = route('terms');
    $defaultPayment = old('payment_method', 'pay_now');
@endphp

<section class="ma-stay-checkout py-80 rpy-60 rel z-1">
    <div class="container">
        <div class="ma-stay-checkout__top d-flex flex-wrap justify-content-between align-items-center gap-3 mb-3">
            <a href="{{ route('home') }}" class="text-muted small"><i class="fas fa-arrow-left me-1"></i> Back to home</a>
        </div>

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
                        <span class="ma-checkout-wizard__label">Payment</span>
                    </button>
                </li>
                <li class="ma-checkout-wizard__item" data-wizard-step="4">
                    <button type="button" class="ma-checkout-wizard__btn" data-goto-step="4">
                        <span class="ma-checkout-wizard__num">4</span>
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

            <div class="ma-stay-checkout__main">

                {{-- Step 1: Your stay --}}
                <div class="ma-checkout-step is-active" data-checkout-step="1" id="checkout-step-1">
                <div class="ma-checkout-card mb-0" id="checkout-stay-dates-card">
                    <div class="ma-checkout-card__head">
                        <span class="ma-checkout-card__icon" aria-hidden="true"><i class="fas fa-calendar-check"></i></span>
                        <div>
                            <h2 class="ma-checkout-card__title">Your stay</h2>
                            <p class="ma-checkout-card__lead">Set dates and guests — your room carries over from the site.</p>
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

                        <div class="ma-checkout-stay-row mt-3" id="checkout-room-picker-empty">
                            @if ($rooms->isNotEmpty())
                                <label class="form-label fw-semibold mb-1" for="checkout-quick-room">Room type</label>
                                <div class="d-flex flex-wrap gap-2">
                                    <select class="form-select ma-checkout-input flex-grow-1" id="checkout-quick-room">
                                        <option value="">Choose a room type…</option>
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
                                    <button type="button" class="theme-btn ma-checkout-add-room-btn" id="checkout-quick-add-room">Add room</button>
                                </div>
                                <p class="small text-muted mt-2 mb-0">Choose a room once — then set your dates above.</p>
                            @else
                                <p class="small text-muted mb-0">Browse <a href="{{ route('rooms') }}">accommodation</a> to add a room.</p>
                            @endif
                        </div>

                        <div class="ma-checkout-selected-room mt-3 d-none" id="checkout-room-picker-selected" aria-live="polite">
                            <span class="ma-checkout-selected-room__label">Selected room</span>
                            <div class="ma-checkout-selected-room__card">
                                <span class="ma-checkout-selected-room__icon" aria-hidden="true"><i class="fas fa-bed"></i></span>
                                <span class="ma-checkout-selected-room__info">
                                    <strong id="checkout-selected-room-name">—</strong>
                                    <span class="small text-muted d-block" id="checkout-selected-room-meta"></span>
                                </span>
                                <button type="button" class="btn btn-link btn-sm text-danger ma-checkout-selected-room__change" id="checkout-change-room">Change room</button>
                            </div>
                            <p class="small text-muted mt-2 mb-0">Your room is saved — just set check-in, check-out, and guests above.</p>
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
                                <label class="form-label" for="guest_first_name">First name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control ma-checkout-input" id="guest_first_name" name="guest_first_name" value="{{ old('guest_first_name') }}" required maxlength="120">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label" for="guest_last_name">Last name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control ma-checkout-input" id="guest_last_name" name="guest_last_name" value="{{ old('guest_last_name') }}" required maxlength="120">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label" for="guest_phone">Mobile (WhatsApp) <span class="text-danger">*</span></label>
                                <input type="tel" class="form-control ma-checkout-input" id="guest_phone" name="guest_phone" value="{{ old('guest_phone') }}" required maxlength="64" placeholder="+250 7XX XXX XXX" autocomplete="tel">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label" for="guest_email">Email <span class="text-danger">*</span></label>
                                <input type="email" class="form-control ma-checkout-input" id="guest_email" name="guest_email" value="{{ old('guest_email') }}" required maxlength="255" autocomplete="email">
                            </div>
                            <div class="col-12">
                                <label class="form-label" for="guest_country">Country / region <span class="text-danger">*</span></label>
                                <input type="text" class="form-control ma-checkout-input" id="guest_country" name="guest_country" value="{{ old('guest_country') }}" required maxlength="120">
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

                {{-- Step 3: Payment --}}
                <div class="ma-checkout-step" data-checkout-step="3" id="checkout-step-3">
                <div class="ma-checkout-card mb-0">
                    <div class="ma-checkout-card__head">
                        <span class="ma-checkout-card__icon" aria-hidden="true"><i class="fas fa-credit-card"></i></span>
                        <div>
                            <h2 class="ma-checkout-card__title">Payment</h2>
                            <p class="ma-checkout-card__lead">Choose how you would like to complete your booking.</p>
                        </div>
                    </div>
                    <div class="ma-checkout-card__body">
                        <div class="ma-pay-panels" id="ma-pay-panels">
                        <div class="ma-pay-choices row g-3" role="radiogroup" aria-label="Payment method">
                            <div class="col-md-6">
                                <label class="ma-pay-choice">
                                    <input type="radio" name="payment_method" value="pay_now" class="ma-pay-choice__input" @checked($defaultPayment === 'pay_now') required>
                                    <span class="ma-pay-choice__surface">
                                        <span class="ma-pay-choice__indicator" aria-hidden="true"></span>
                                        <span class="ma-pay-choice__text">
                                            <strong>Pay direct</strong>
                                            <small>Secure card payment — instant confirmation</small>
                                        </span>
                                    </span>
                                </label>
                            </div>
                            <div class="col-md-6">
                                <label class="ma-pay-choice {{ (!$hotelWhatsappReady && !$hotelEmailReady) ? 'ma-pay-choice--disabled' : '' }}">
                                    <input type="radio" name="payment_method" value="pay_at_hotel" class="ma-pay-choice__input" @checked($defaultPayment === 'pay_at_hotel') @disabled(!$hotelWhatsappReady && !$hotelEmailReady)>
                                    <span class="ma-pay-choice__surface">
                                        <span class="ma-pay-choice__indicator" aria-hidden="true"></span>
                                        <span class="ma-pay-choice__text">
                                            <strong>Pay at hotel</strong>
                                            <small>Reserve now, pay on arrival via WhatsApp or email</small>
                                        </span>
                                    </span>
                                </label>
                            </div>
                        </div>

                        <div id="pay-now-details" class="ma-stay-pay-panel ma-stay-pay-panel--card ma-stay-pay-panel--pay-now mt-4" @if($defaultPayment !== 'pay_now') hidden @endif>
                            <h3 class="ma-stay-pay-panel__title"><i class="fas fa-lock text-success me-2"></i>Card details</h3>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label" for="card_holder_name">Cardholder name</label>
                                    <input type="text" class="form-control ma-checkout-input" id="card_holder_name" name="card_holder_name" value="{{ old('card_holder_name') }}" maxlength="120" autocomplete="cc-name" placeholder="As shown on card">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label" for="card_number">Card number</label>
                                    <input type="text" class="form-control ma-checkout-input" id="card_number" name="card_number" value="{{ old('card_number') }}" maxlength="24" inputmode="numeric" autocomplete="cc-number" placeholder="0000 0000 0000 0000">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label" for="card_expiry">Expiry (MM/YY)</label>
                                    <input type="text" class="form-control ma-checkout-input" id="card_expiry" name="card_expiry" value="{{ old('card_expiry') }}" maxlength="7" autocomplete="cc-exp" placeholder="MM/YY">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label" for="card_cvc">CVC</label>
                                    <input type="text" class="form-control ma-checkout-input" id="card_cvc" name="card_cvc" value="{{ old('card_cvc') }}" maxlength="4" inputmode="numeric" autocomplete="cc-csc" placeholder="123">
                                </div>
                            </div>
                        </div>

                        <div id="pay-at-hotel-channels" class="ma-stay-pay-panel ma-stay-pay-panel--hotel ma-stay-pay-panel--pay-hotel mt-4" @if($defaultPayment !== 'pay_at_hotel') hidden @endif>
                            <h3 class="ma-stay-pay-panel__title">Send your reservation via</h3>
                            <div class="ma-channel-choices d-flex flex-wrap gap-3" role="radiogroup" aria-label="Reservation channel">
                                @if ($hotelWhatsappReady)
                                    <label class="ma-channel-choice">
                                        <input type="radio" name="pay_at_hotel_channel" value="whatsapp" class="ma-channel-choice__input" @checked(old('pay_at_hotel_channel', $hotelWhatsappReady ? 'whatsapp' : null) === 'whatsapp')>
                                        <span class="ma-channel-choice__surface">
                                            <span class="ma-channel-choice__indicator" aria-hidden="true"></span>
                                            <i class="fab fa-whatsapp"></i>
                                            <span>WhatsApp</span>
                                        </span>
                                    </label>
                                @endif
                                @if ($hotelEmailReady)
                                    <label class="ma-channel-choice">
                                        <input type="radio" name="pay_at_hotel_channel" value="email" class="ma-channel-choice__input" @checked(old('pay_at_hotel_channel') === 'email')>
                                        <span class="ma-channel-choice__surface">
                                            <span class="ma-channel-choice__indicator" aria-hidden="true"></span>
                                            <i class="fas fa-envelope"></i>
                                            <span>Email</span>
                                        </span>
                                    </label>
                                @endif
                            </div>
                            <p class="small text-muted mt-3 mb-0">We will contact you using the mobile number and email above to confirm your pay-at-hotel reservation.</p>
                        </div>
                        </div>{{-- .ma-pay-panels --}}
                    </div>
                </div>
                </div>{{-- step 3 --}}

                <div class="ma-checkout-step" data-checkout-step="4" id="checkout-step-4">
                    <div class="ma-checkout-card mb-3">
                        <div class="ma-checkout-card__head">
                            <span class="ma-checkout-card__icon" aria-hidden="true"><i class="fas fa-clipboard-check"></i></span>
                            <p class="ma-checkout-card__title mb-0">Review &amp; confirm</p>
                        </div>
                        <div class="ma-checkout-card__body">
                            <div id="checkout-review-summary" class="ma-checkout-review mb-4"></div>
                            <label class="ma-stay-terms-check mb-0">
                                <input type="checkbox" name="terms_accepted" value="1" id="terms_accepted" @checked(old('terms_accepted')) required>
                                <span class="ma-stay-terms-check__box" aria-hidden="true"></span>
                                <span class="ma-stay-terms-check__label">
                                    I have read and agree to the <a href="{{ $termsUrl }}" target="_blank" rel="noopener">Hotel Policy and Terms &amp; Conditions</a>.
                                </span>
                            </label>
                        </div>
                    </div>
                    <button type="submit" class="theme-btn btn-lg w-100 ma-stay-checkout__submit" id="stay-checkout-submit">
                        Confirm booking
                    </button>
                </div>

                <div class="ma-checkout-step-nav" id="checkout-step-nav">
                    <button type="button" class="btn btn-outline-secondary ma-checkout-step-nav__back" id="checkout-step-back" disabled>
                        <i class="fas fa-arrow-left me-1"></i> Back
                    </button>
                    <div class="ma-checkout-step-nav__meta">
                        <span class="ma-checkout-step-nav__total" id="checkout-nav-total">$0.00</span>
                        <span class="ma-checkout-step-nav__step-label" id="checkout-nav-step">Step 1 of 4</span>
                    </div>
                    <button type="button" class="theme-btn ma-checkout-step-nav__next" id="checkout-step-next">
                        Continue <i class="fas fa-arrow-right ms-1"></i>
                    </button>
                </div>

            </div>

            <aside class="ma-stay-checkout__aside ma-stay-checkout__aside--desktop">
                <div class="ma-stay-summary ma-checkout-summary card border-0 shadow-sm sticky-top">
                    <div class="card-header ma-checkout-summary__head border-0">
                        <h2 class="h5 mb-0">Your stay summary</h2>
                    </div>
                    <div class="card-body p-0">
                        <div id="checkout-summary-meta" class="ma-checkout-summary__meta px-3 pt-3 d-none"></div>
                        <div id="checkout-summary-lines" class="ma-stay-summary__lines"></div>
                        <p class="small text-muted px-3 py-3 mb-0 d-none" id="checkout-summary-empty">
                            Your cart is empty.
                            <a href="{{ route('rooms') }}">Browse rooms</a> or add one above.
                        </p>
                    </div>
                    <div class="card-footer ma-checkout-summary__foot border-0">
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="text-muted small" id="checkout-pay-label">Estimated total</span>
                            <strong class="ma-checkout-summary__total" id="checkout-summary-total">$0.00</strong>
                        </div>
                    </div>
                </div>
            </aside>
        </form>
    </div>
</section>

@endsection

@section('scripts')
<script>
(function () {
    var checkoutBooted = false;

    function initCheckout() {
        if (!window.IsangeStayCart) {
            return;
        }
        if (checkoutBooted) {
            if (window.__checkoutRender) {
                window.__checkoutRender();
            }
            return;
        }
        checkoutBooted = true;

        @if (old('cart_json'))
        try {
            var restoredCart = {!! json_encode(old('cart_json')) !!};
            if (typeof restoredCart === 'string' && restoredCart.length) {
                sessionStorage.setItem('isange_stay_cart', restoredCart);
            }
        } catch (e) {}
        @endif

        @if ($prefillRoom)
        (function prefillOnce() {
            var key = 'isange_prefill_room_{{ $prefillRoom->id }}';
            if (sessionStorage.getItem(key)) return;
            var sel = document.getElementById('checkout-quick-room');
            if (sel) {
                sel.value = '{{ $prefillRoom->id }}';
                document.getElementById('checkout-quick-add-room')?.click();
            }
            sessionStorage.setItem(key, '1');
        })();
        @endif

        var form = document.getElementById('stay-checkout-form');
        var cartInput = document.getElementById('stay-checkout-cart-json');
        var linesEl = document.getElementById('checkout-summary-lines');
        var summaryMeta = document.getElementById('checkout-summary-meta');
        var emptyEl = document.getElementById('checkout-summary-empty');
        var roomPickerEmpty = document.getElementById('checkout-room-picker-empty');
        var roomPickerSelected = document.getElementById('checkout-room-picker-selected');
        var selectedRoomName = document.getElementById('checkout-selected-room-name');
        var selectedRoomMeta = document.getElementById('checkout-selected-room-meta');
        var changeRoomBtn = document.getElementById('checkout-change-room');
        var totalEl = document.getElementById('checkout-summary-total');
        var payLabel = document.getElementById('checkout-pay-label');
        var payAtHotel = document.getElementById('pay-at-hotel-channels');
        var payNowDetails = document.getElementById('pay-now-details');
        var submitBtn = document.getElementById('stay-checkout-submit');
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
        var currentStep = 1;
        var maxStepReached = 1;

        function goToStep(step, opts) {
            step = Math.max(1, Math.min(4, step));
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
                if (step >= 4) {
                    stepNextBtn.classList.add('d-none');
                } else {
                    stepNextBtn.classList.remove('d-none');
                    stepNextBtn.innerHTML = 'Continue <i class="fas fa-arrow-right ms-1"></i>';
                }
            }
            if (navStepEl) {
                navStepEl.textContent = 'Step ' + step + ' of 4';
            }
            if (step === 4) {
                renderReviewSummary();
            }
            var activePanel = document.getElementById('checkout-step-' + step);
            if (activePanel && !(opts && opts.silent)) {
                activePanel.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }
        }

        function validateStep(step) {
            if (step === 1) {
                pushStayToCart();
                if (!IsangeStayCart.hasItems()) {
                    alert('Please add at least one room or experience to your cart.');
                    return false;
                }
                if (IsangeStayCart.get().rooms.length > 0) {
                    var stay = readStayFields();
                    if (!stay.check_in || !stay.check_out || stay.check_out <= stay.check_in) {
                        alert('Please choose valid check-in and check-out dates.');
                        return false;
                    }
                }
                return true;
            }
            if (step === 2) {
                var required = ['guest_first_name', 'guest_last_name', 'guest_phone', 'guest_email', 'guest_country'];
                for (var i = 0; i < required.length; i++) {
                    var el = form.querySelector('[name="' + required[i] + '"]');
                    if (!el || !String(el.value || '').trim()) {
                        alert('Please complete all guest details.');
                        if (el) el.focus();
                        return false;
                    }
                }
                return true;
            }
            if (step === 3) {
                var payMethod = form.querySelector('input[name="payment_method"]:checked');
                if (!payMethod) {
                    alert('Please select a payment method.');
                    return false;
                }
                if (payMethod.value === 'pay_at_hotel') {
                    if (!form.querySelector('input[name="pay_at_hotel_channel"]:checked')) {
                        alert('Choose WhatsApp or email for pay-at-hotel.');
                        return false;
                    }
                }
                if (payMethod.value === 'pay_now') {
                    var holder = (form.querySelector('[name="card_holder_name"]') || {}).value || '';
                    var cardNum = (form.querySelector('[name="card_number"]') || {}).value || '';
                    if (!holder.trim() || cardNum.replace(/\D/g, '').length < 12) {
                        alert('Enter cardholder name and a valid card number.');
                        return false;
                    }
                }
                return true;
            }
            return true;
        }

        function renderReviewSummary() {
            if (!reviewEl) return;
            var stay = readStayFields();
            var cart = IsangeStayCart.get();
            var payMethod = form.querySelector('input[name="payment_method"]:checked');
            var payLabelText = payMethod && payMethod.value === 'pay_now' ? 'Pay direct (card)' : 'Pay at hotel';
            if (payMethod && payMethod.value === 'pay_at_hotel') {
                var ch = form.querySelector('input[name="pay_at_hotel_channel"]:checked');
                if (ch) payLabelText += ' · ' + (ch.value === 'whatsapp' ? 'WhatsApp' : 'Email');
            }
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

        function syncPaymentPanels() {
            var payMethod = form.querySelector('input[name="payment_method"]:checked');
            var isPayNow = payMethod && payMethod.value === 'pay_now';
            var isPayAtHotel = payMethod && payMethod.value === 'pay_at_hotel';

            document.querySelectorAll('.ma-pay-choice').forEach(function (label) {
                var input = label.querySelector('.ma-pay-choice__input');
                label.classList.toggle('ma-pay-choice--selected', input && input.checked);
            });

            if (payNowDetails) {
                payNowDetails.hidden = !isPayNow;
                payNowDetails.classList.toggle('d-none', !isPayNow);
                payNowDetails.setAttribute('aria-hidden', isPayNow ? 'false' : 'true');
            }
            if (payAtHotel) {
                payAtHotel.hidden = !isPayAtHotel;
                payAtHotel.classList.toggle('d-none', !isPayAtHotel);
                payAtHotel.setAttribute('aria-hidden', isPayAtHotel ? 'false' : 'true');
            }

            if (payLabel) {
                payLabel.textContent = isPayNow ? 'Pay now' : 'Estimated total';
            }
            if (submitBtn) {
                submitBtn.textContent = isPayNow ? 'Confirm & pay now' : 'Confirm booking';
            }

            document.querySelectorAll('#pay-now-details input').forEach(function (inp) {
                inp.required = isPayNow;
                inp.disabled = !isPayNow;
            });
            document.querySelectorAll('#pay-at-hotel-channels input[name="pay_at_hotel_channel"]').forEach(function (inp) {
                inp.required = isPayAtHotel;
                inp.disabled = !isPayAtHotel;
            });

            document.querySelectorAll('.ma-channel-choice').forEach(function (label) {
                var input = label.querySelector('.ma-channel-choice__input');
                label.classList.toggle('ma-channel-choice--selected', input && input.checked && isPayAtHotel);
            });
        }

        function updateRoomPicker(cart) {
            var hasRooms = cart.rooms.length > 0;
            if (roomPickerEmpty) {
                roomPickerEmpty.classList.toggle('d-none', hasRooms);
            }
            if (roomPickerSelected) {
                roomPickerSelected.classList.toggle('d-none', !hasRooms);
            }
            if (hasRooms) {
                var primary = cart.rooms[0];
                if (selectedRoomName) {
                    selectedRoomName.textContent = primary.name || 'Room';
                }
                if (selectedRoomMeta) {
                    var count = cart.rooms.length;
                    selectedRoomMeta.textContent = count > 1
                        ? count + ' rooms of this type'
                        : '1 room';
                }
                if (stayRoomsCount && parseInt(stayRoomsCount.value, 10) !== cart.rooms.length) {
                    stayRoomsCount.value = cart.rooms.length;
                }
                var sel = document.getElementById('checkout-quick-room');
                if (sel && primary.room_id) {
                    sel.value = String(primary.room_id);
                }
            }
        }

        function renderSummary() {
            var cart = IsangeStayCart.get();
            var stay = IsangeStayCart.getStay();
            fillStayFields(stay);

            if (cartInput) {
                cartInput.value = IsangeStayCart.toJson();
            }

            updateRoomPicker(cart);

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

        function onPaymentMethodChange() {
            syncPaymentPanels();
        }

        form.querySelectorAll('input[name="payment_method"]').forEach(function (radio) {
            radio.addEventListener('change', onPaymentMethodChange);
            radio.addEventListener('click', onPaymentMethodChange);
        });
        form.querySelectorAll('input[name="pay_at_hotel_channel"]').forEach(function (radio) {
            radio.addEventListener('change', syncPaymentPanels);
        });
        document.querySelectorAll('.ma-pay-choice').forEach(function (label) {
            label.addEventListener('click', function () {
                setTimeout(onPaymentMethodChange, 0);
            });
        });

        IsangeStayCart.onChange(renderSummary);
        renderSummary();
        @if ($errors->any())
        goToStep(4, { silent: true });
        @else
        goToStep(1, { silent: true });
        @endif

        if (stepBackBtn) {
            stepBackBtn.addEventListener('click', function () {
                if (currentStep > 1) {
                    goToStep(currentStep - 1);
                }
            });
        }
        if (stepNextBtn) {
            stepNextBtn.addEventListener('click', function () {
                if (validateStep(currentStep)) {
                    goToStep(currentStep + 1);
                }
            });
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
            if (currentStep < 4) {
                e.preventDefault();
                if (validateStep(currentStep)) {
                    goToStep(currentStep + 1);
                }
                return;
            }
            pushStayToCart();

            if (!IsangeStayCart.hasItems()) {
                e.preventDefault();
                alert('Please add at least one room or experience to your cart.');
                return;
            }

            if (IsangeStayCart.get().rooms.length > 0) {
                var stay = readStayFields();
                if (!stay.check_in || !stay.check_out || stay.check_out <= stay.check_in) {
                    e.preventDefault();
                    alert('Please choose valid check-in and check-out dates.');
                    document.getElementById('checkout-stay-dates-card')?.scrollIntoView({ behavior: 'smooth', block: 'start' });
                    return;
                }
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
            if (payMethod && payMethod.value === 'pay_now') {
                var holder = (form.querySelector('[name="card_holder_name"]') || {}).value || '';
                var cardNum = (form.querySelector('[name="card_number"]') || {}).value || '';
                if (!holder.trim() || cardNum.replace(/\D/g, '').length < 12) {
                    e.preventDefault();
                    alert('Enter cardholder name and a valid card number for pay direct.');
                    return;
                }
            }
            if (!form.querySelector('#terms_accepted:checked')) {
                e.preventDefault();
                alert('Please accept the hotel policy and terms to continue.');
                return;
            }
        });

        if (changeRoomBtn) {
            changeRoomBtn.addEventListener('click', function () {
                var cart = IsangeStayCart.get();
                while (cart.rooms.length > 0) {
                    IsangeStayCart.removeRoom(0);
                    cart = IsangeStayCart.get();
                }
                if (stayRoomsCount) stayRoomsCount.value = 1;
            });
        }

        var quickAdd = document.getElementById('checkout-quick-add-room');
        if (quickAdd) {
            quickAdd.addEventListener('click', function () {
                var sel = document.getElementById('checkout-quick-room');
                var opt = sel && sel.options[sel.selectedIndex];
                if (!opt || !opt.value) {
                    alert('Choose a room type first.');
                    return;
                }
                pushStayToCart();
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
                var partial = readStayFields();
                IsangeStayCart.setRoomsCount(partial.rooms_count);
            });
        }
    }

    document.addEventListener('DOMContentLoaded', initCheckout);
    document.addEventListener('isange:stay-cart-ready', initCheckout);
    document.addEventListener('ma:spa-content', initCheckout);
})();
</script>
@endsection
