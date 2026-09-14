@php
    use App\Support\Currency;

    $room = $room ?? null;
    $showOnRequest = $showOnRequest ?? true;
    $discountEligible = (bool) auth()->user()?->hasUnlockedDiscount();
    $promoOn = $room && $room->hasActiveDiscount() && \App\Support\RoomDiscountPromotion::hasActivePromotion();
@endphp

@if ($room && $room->listPriceUsd() !== null)
    @if ($promoOn)
        @php
            $listUsd = $room->listPriceUsd();
            $saleUsd = $room->salePriceUsd();
            $saleRwf = $room->salePriceRwf();
            $badge = $room->discountBadgeLabel();
            $tooltip = $room->discountTooltip();
            $listFmt = number_format($listUsd, $listUsd == floor($listUsd) ? 0 : 2);
        @endphp
        <div class="room-price room-price--sale room-price--inline mb-2" title="{{ $tooltip }}">
            <span class="room-price__now">{!! Currency::formatUsdWithLocal($saleUsd, $saleRwf) !!}</span>
            <span class="room-price__was">${{ $listFmt }}</span>
            <span class="price-suffix">/ night</span>
            @if ($badge)
                <span class="room-price__badge">{{ $badge }}</span>
            @endif
        </div>
    @else
        <div class="room-price room-price--list mb-2">
            {!! Currency::formatUsdWithLocal($room->bookingPriceUsd(false), $room->bookingPriceRwf(false)) !!}
            <span class="price-suffix text-muted"> / night</span>
        </div>
    @endif
@elseif ($showOnRequest)
    <div class="price mb-2 small text-muted">Price on request</div>
@endif
