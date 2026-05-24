<a href="{{ url('/') }}" class="auth-brand-logo inline-block rounded-lg focus:outline-none focus-visible:ring-2 focus-visible:ring-[#e85e26] focus-visible:ring-offset-2">
    <img
        src="{{ $brandLogo ?? asset('assets/images/isange-logo.png') }}"
        alt="{{ $brandName ?? config('app.name') }}"
        class="auth-brand-logo__img"
        width="280"
        height="96"
    />
</a>
