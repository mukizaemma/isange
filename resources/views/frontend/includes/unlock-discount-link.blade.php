@php
    $discountUnlocked = (bool) ($discountUnlocked ?? auth()->user()?->hasUnlockedDiscount());
    $discountPercent = $discountPercent ?? null;

    if ($discountPercent === null && isset($room) && $room && $room->hasActiveDiscount()) {
        $discountPercent = $room->effectiveDiscountPercent();
    }

    if ($discountPercent === null && isset($rooms) && $rooms) {
        $discountPercent = collect($rooms)
            ->filter(fn ($r) => $r->hasActiveDiscount())
            ->max(fn ($r) => (float) $r->effectiveDiscountPercent());
    }

    $discountPercent = $discountPercent ?? \App\Support\RoomDiscountPromotion::maximumPercent();
    $discountPercent = $discountPercent !== null && $discountPercent > 0
        ? ((float) $discountPercent == floor((float) $discountPercent)
            ? (string) (int) $discountPercent
            : number_format((float) $discountPercent, 1))
        : null;

    $label = $discountUnlocked
        ? 'Discount unlocked'
        : 'Unlock '.$discountPercent.'% discount';
    $href = $discountUnlocked
        ? ($unlockedHref ?? route('booking.checkout').'#checkout-flow')
        : route('guest.discount');
    $class = trim('isange-unlock-discount '.($class ?? ''));
@endphp

@if ($discountPercent === null)
    {{-- No active room discount: do not advertise an unavailable saving. --}}
@elseif ($discountUnlocked)
    <span class="{{ $class }} isange-unlock-discount--done">
        <i class="fas fa-check-circle" aria-hidden="true"></i>
        {{ $label }}
    </span>
@else
    <a href="{{ $href }}" class="{{ $class }}">
        <i class="fas fa-lock" aria-hidden="true"></i>
        {{ $label }}
    </a>
@endif
