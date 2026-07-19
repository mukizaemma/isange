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
        <div class="room-price room-price--sale mb-2" title="{{ $tooltip }}">
            <div class="room-price__compare">
                <span class="room-price__label">Was</span>
                <span class="room-price__was">${{ $listFmt }}</span>
                <span class="price-suffix text-muted">/ night</span>
            </div>
            <div class="room-price__compare room-price__compare--now">
                <span class="room-price__label room-price__label--now">Now</span>
                <span class="room-price__now">
                    {!! Currency::formatUsdWithLocal($saleUsd, $saleRwf) !!}
                    @if ($badge)
                        <span class="room-price__badge">{{ $badge }}</span>
                    @endif
                </span>
                <span class="price-suffix">/ night</span>
            </div>
        </div>
    @else
        <div class="room-price room-price--list mb-2">
            {!! Currency::formatUsdWithLocal($room->bookingPriceUsd(false), $room->bookingPriceRwf(false)) !!}
            <span class="price-suffix text-muted"> / night</span>
        </div>
        @if ($room->hasActiveDiscount())
            <div class="mb-2">
                @include('frontend.includes.unlock-discount-link', [
                    'discountUnlocked' => $discountEligible,
                    'room' => $room,
                    'class' => 'isange-unlock-discount--room',
                ])
            </div>
        @endif
    @endif
@elseif ($showOnRequest)
    <div class="price mb-2 small text-muted">Price on request</div>
@endif
