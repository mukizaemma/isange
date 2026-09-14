@php
    $percent = \App\Support\RoomDiscountPromotion::formattedMaximumPercent();
    $period = \App\Support\RoomDiscountPromotion::periodLabel();
    $unlocked = (bool) auth()->user()?->hasUnlockedDiscount();
    $bookUrl = $unlocked ? route('booking.checkout').'#checkout-flow' : route('guest.discount');
    $skipAuto = request()->routeIs('guest.discount', 'guest.discount.verify', 'booking.checkout', 'room.booking');
@endphp
@if ($percent)
<div class="modal fade isange-promo-popup" id="directPromoModal" tabindex="-1" aria-labelledby="directPromoModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content isange-promo-popup__content">
            <button type="button" class="btn-close isange-promo-popup__close" data-bs-dismiss="modal" aria-label="Close"></button>
            <div class="isange-promo-popup__ribbon">Book direct · skip OTA fees</div>
            <div class="isange-promo-popup__hero">
                <p class="isange-promo-popup__kicker">Special rate · website only</p>
                <h2 id="directPromoModalLabel">Save up to {{ $percent }}% when you book with us</h2>
                @if ($period)
                    <p class="isange-promo-popup__dates">
                        <i class="far fa-calendar-alt" aria-hidden="true"></i>
                        For stays {{ $period }}
                    </p>
                @endif
            </div>
            <div class="isange-promo-popup__body">
                <ul class="isange-promo-popup__points">
                    <li>Lower than typical OTA rates</li>
                    <li>Unlock once, then book at the sale price</li>
                </ul>
                <a class="theme-btn isange-promo-popup__cta" href="{{ $bookUrl }}">
                    Book now <i class="fas fa-arrow-right" aria-hidden="true"></i>
                </a>
                <button type="button" class="isange-promo-popup__dismiss" data-bs-dismiss="modal">Maybe later</button>
            </div>
        </div>
    </div>
</div>

<button type="button" class="isange-promo-badge" id="directPromoBadge" hidden
    data-bs-toggle="modal" data-bs-target="#directPromoModal"
    aria-label="Open direct booking discount offer">
    <span class="isange-promo-badge__pct">{{ $percent }}% off</span>
    <span class="isange-promo-badge__txt">Book direct</span>
</button>

<script>
document.addEventListener('DOMContentLoaded', function () {
    var modalEl = document.getElementById('directPromoModal');
    var badge = document.getElementById('directPromoBadge');
    if (!modalEl) return;

    var storageKey = @json('isange_direct_promo_'.\App\Support\RoomDiscountPromotion::formattedMaximumPercent().'_'.(\App\Support\RoomDiscountPromotion::periodLabel() ?? 'open'));
    var skipAuto = @json($skipAuto);

    function boot() {
        if (typeof bootstrap === 'undefined') return;
        var modal = bootstrap.Modal.getOrCreateInstance(modalEl);
        var dismissed = false;
        try { dismissed = window.sessionStorage.getItem(storageKey) === '1'; } catch (e) {}

        function showBadge() {
            if (badge) badge.hidden = false;
        }

        modalEl.addEventListener('hidden.bs.modal', function () {
            try { window.sessionStorage.setItem(storageKey, '1'); } catch (e) {}
            if (!skipAuto) {
                showBadge();
            }
        });

        if (skipAuto || dismissed) {
            if (!skipAuto) {
                showBadge();
            }
            return;
        }

        setTimeout(function () { modal.show(); }, 700);
    }

    if (typeof bootstrap !== 'undefined') {
        boot();
    } else {
        window.addEventListener('load', boot);
    }
});
</script>
@endif
