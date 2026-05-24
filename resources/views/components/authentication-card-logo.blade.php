<a href="{{ url('/') }}" class="auth-brand-logo inline-block rounded-lg focus:outline-none focus-visible:ring-2 focus-visible:ring-[#e85e26] focus-visible:ring-offset-2 focus-visible:ring-offset-white transition">
    <img
        src="{{ $brandLogo ?? asset('assets/images/isange-logo.png') }}"
        alt="{{ $brandName ?? config('app.name') }}"
        class="mx-auto h-20 w-auto max-w-[280px] object-contain drop-shadow-sm sm:h-24"
        width="280"
        height="96"
    />
</a>
