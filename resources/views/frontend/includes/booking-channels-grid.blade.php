{{-- Booking channels — footer & book page (uses $setting) --}}
@php
    use App\Support\BookingEngine;

    $bookingEngineUrl = BookingEngine::url($setting ?? null);
    $waDigits = preg_replace('/\D+/', '', $setting->phone ?? '');
    $compact = $compact ?? false;
    $context = $context ?? 'default';
    $isFooter = $context === 'footer';
    $twoColumns = $twoColumns ?? true;
    $colClass = $isFooter ? 'col-6' : ($twoColumns ? 'col-md-6' : 'col-12');
@endphp
<div class="ma-book-channels{{ $isFooter ? ' ma-book-channels--footer' : '' }}">
    @unless ($compact)
        <h4 class="section-title-sm font-weight-bold mb-3">Book your stay</h4>
        <p class="ma-book-channels__intro mb-3{{ $isFooter ? '' : ' small text-muted' }}">Choose how you would like to book — each option below opens the right next step.</p>
    @endunless

    <div class="row g-3 ma-book-channels__cols">
        <div class="{{ $colClass }}">
            <div class="ma-book-channels__col h-100">
                <p class="ma-book-channels__group-title fw-semibold mb-3">Book on discounted prices</p>
                <div class="d-grid gap-2">
                    @if ($waDigits !== '' && strlen($waDigits) >= 8)
                        <a class="theme-btn style-three w-100 text-center d-block py-2" href="{{ route('booking.checkout', ['mode' => 'pay_at_hotel', 'channel' => 'whatsapp']) }}">
                            <i class="fab fa-whatsapp me-1"></i> WhatsApp
                        </a>
                    @endif
                    @if (! empty(trim((string) ($setting->email ?? ''))))
                        <a class="theme-btn style-three w-100 text-center d-block py-2" href="{{ route('booking.checkout', ['mode' => 'pay_at_hotel', 'channel' => 'email']) }}">
                            <i class="far fa-envelope me-1"></i> Email
                        </a>
                    @endif
                    <a
                        class="theme-btn w-100 text-center d-block py-2{{ $bookingEngineUrl ? '' : ' disabled pe-none opacity-50' }}"
                        href="{{ $bookingEngineUrl ?: '#' }}"
                        @if ($bookingEngineUrl) target="_blank" rel="noopener noreferrer" @endif
                    >
                        Book and Pay Now
                    </a>
                </div>
            </div>
        </div>

        <div class="{{ $colClass }}">
            <div class="ma-book-channels__col h-100">
                <p class="ma-book-channels__group-title fw-semibold mb-3">Book through our trusted agents</p>
                <div class="d-grid gap-2">
                    <a class="theme-btn style-three w-100 text-center d-block py-2" href="{{ route('room.booking', ['channel' => 'booking_com']) }}">Booking.com</a>
                    <a class="theme-btn style-three w-100 text-center d-block py-2" href="{{ route('room.booking', ['channel' => 'expedia']) }}">Expedia</a>
                    <a class="theme-btn style-three w-100 text-center d-block py-2" href="{{ route('room.booking', ['channel' => 'emerging_travel']) }}">Emerging Travel Group</a>
                </div>
            </div>
        </div>
    </div>

    @if (! $compact && (! empty(trim((string) ($setting->url_tripadvisor ?? ''))) || ! empty(trim((string) ($setting->url_google_business ?? '')))))
        <div class="mt-4 pt-3 border-top border-secondary border-opacity-25">
            <h4 class="section-title-sm font-weight-bold mb-3">Reviews</h4>
            <div class="row g-2">
                @if (! empty(trim((string) ($setting->url_tripadvisor ?? ''))))
                    <div class="col-sm-6">
                        <a class="btn btn-outline-light w-100" href="{{ $setting->url_tripadvisor }}" target="_blank" rel="noopener noreferrer">TripAdvisor</a>
                    </div>
                @endif
                @if (! empty(trim((string) ($setting->url_google_business ?? ''))))
                    <div class="col-sm-6">
                        <a class="btn btn-outline-light w-100" href="{{ $setting->url_google_business }}" target="_blank" rel="noopener noreferrer">Google</a>
                    </div>
                @endif
            </div>
        </div>
    @endif
</div>
