{{-- Clears localStorage cart once after a successful booking (not on every page load). --}}
@if (! empty($booking?->public_id))
<script>
(function () {
    var flag = 'isange_stay_cart_done_{{ $booking->public_id }}';
    if (sessionStorage.getItem(flag)) {
        return;
    }
    sessionStorage.setItem(flag, '1');
    try {
        sessionStorage.removeItem('isange_stay_cart');
        localStorage.removeItem('isange_stay_cart');
    } catch (e) {}
    if (window.IsangeStayCart && typeof window.IsangeStayCart.clear === 'function') {
        window.IsangeStayCart.clear();
    } else if (typeof window.__stayCartDockRender === 'function') {
        window.__stayCartDockRender();
    }
})();
</script>
@endif
