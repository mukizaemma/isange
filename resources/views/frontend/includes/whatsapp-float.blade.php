@php
    $waDigits = preg_replace('/\D+/', '', (string) ($setting->phone ?? ''));
    $waMessage = rawurlencode('Hello, I would like to enquire about Isange Paradise Eco Resort.');
@endphp
@if (strlen($waDigits) >= 8)
    <a
        href="https://wa.me/{{ $waDigits }}?text={{ $waMessage }}"
        class="ma-whatsapp-float"
        target="_blank"
        rel="noopener noreferrer"
        aria-label="Chat with us on WhatsApp"
        title="WhatsApp us"
    >
        <i class="fab fa-whatsapp" aria-hidden="true"></i>
    </a>
@endif
