{{-- Choose pay-online (booking engine) vs pay-at-hotel (on-site checkout) --}}
@php
    $bookingEngineUrl = $bookingEngineUrl ?? \App\Support\BookingEngine::url($setting ?? null);
@endphp

<div id="booking-mode-choice" class="ma-booking-mode-choice">
    <div class="ma-booking-mode-choice__card">
        <h2 class="ma-booking-mode-choice__title">How would you like to book?</h2>
        <div class="row g-3 ma-booking-mode-choice__actions">
            <div class="col-md-6">
                <a
                    href="{{ $bookingEngineUrl ?: '#' }}"
                    class="ma-booking-mode-choice__option theme-btn w-100 d-flex align-items-center justify-content-center text-center py-3{{ $bookingEngineUrl ? '' : ' disabled pe-none opacity-50' }}"
                    @if ($bookingEngineUrl) target="_blank" rel="noopener noreferrer" @endif
                >
                    <strong>Book and pay now</strong>
                </a>
            </div>
            <div class="col-md-6">
                <button
                    type="button"
                    class="ma-booking-mode-choice__option theme-btn style-three w-100 d-flex align-items-center justify-content-center text-center py-3"
                    id="booking-mode-pay-at-hotel"
                >
                    <strong>Book and pay at the hotel</strong>
                </button>
            </div>
        </div>
    </div>
</div>
