@php
    use App\Support\Currency;

    $room = $room ?? null;
    $showOnRequest = $showOnRequest ?? true;
@endphp

@if ($room && $room->price !== null && (float) $room->price > 0)
    <div class="price mb-2">
        {!! Currency::formatUsdWithLocal($room->price, $room->price_rwf) !!}
        <span class="price-suffix text-muted"> / night</span>
    </div>
@elseif ($showOnRequest)
    <div class="price mb-2 small text-muted">Price on request</div>
@endif
