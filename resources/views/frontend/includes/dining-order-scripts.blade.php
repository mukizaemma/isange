@php
    $waDigits = preg_replace('/\D+/', '', $setting->phone ?? '');
@endphp
<script>
window.__diningOrderConfig = {
    wa: @json($waDigits),
    email: @json(trim($setting->email ?? '')),
    hotel: @json($setting->company ?? 'Isange Paradise Eco Resort'),
    displayPhone: @json($setting->phone ?? ''),
    displayEmail: @json(trim($setting->email ?? '')),
    trackUrl: @json(route('track.analytics')),
    guestDiningUrl: @json(route('guest.dining.store'))
};
</script>
<script src="{{ asset('assets/js/dining-order.js') }}" defer></script>
