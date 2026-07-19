@php
    use App\Support\Currency;

    $room = $room ?? null;
    $showOnRequest = $showOnRequest ?? true;
    $discountEligible = (bool) auth()->user()?->hasUnlockedDiscount();
@endphp

@if ($room && $room->listPriceUsd() !== null)
    @if ($discountEligible && $room->hasActiveDiscount())
        @php
            $listUsd = $room->listPriceUsd();
            $saleUsd = $room->salePriceUsd();
            $saleRwf = $room->salePriceRwf();
            $badge = $room->discountBadgeLabel();
            $tooltip = $room->discountTooltip();
            $listFmt = number_format($listUsd, $listUsd == floor($listUsd) ? 0 : 2);
        @endphp
        <div class="room-price room-price--sale mb-2">
            <div class="room-price__was" aria-hidden="true">$ {{ $listFmt }}</div>
            <div class="room-price__now">
                {!! Currency::formatUsdWithLocal($saleUsd, $saleRwf) !!}
                <button type="button"
                    class="room-price__info"
                    title="{{ $tooltip }}"
                    aria-label="{{ $tooltip }}">
                    <span aria-hidden="true">i</span>
                </button>
                @if ($badge)
                    <span class="room-price__badge">{{ $badge }}</span>
                @endif
            </div>
            <span class="price-suffix text-muted"> / night</span>
        </div>
    @else
        <div class="price mb-2">
            {!! Currency::formatUsdWithLocal($room->bookingPriceUsd(false), $room->bookingPriceRwf(false)) !!}
            <span class="price-suffix text-muted"> / night</span>
        </div>
        @if ($room->hasActiveDiscount())
            <a class="small fw-bold text-success d-inline-block mb-2" href="{{ route('guest.discount') }}">
                <i class="fas fa-lock me-1" aria-hidden="true"></i> Unlock {{ $room->discountBadgeLabel() }}
            </a>
        @endif
    @endif
@elseif ($showOnRequest)
    <div class="price mb-2 small text-muted">Price on request</div>
@endif
