@props([
    'subtitle' => null,
])

@php
    $displaySubtitle = $subtitle ?? (($brandName ?? config('app.name')).' · Admin');
    $hero = $authHeroImage ?? null;
@endphp

<div class="auth-shell">
    <aside
        class="auth-shell__hero"
        @if ($hero)
            style="background-image: url('{{ $hero }}');"
        @endif
    >
        <div class="auth-shell__hero-overlay" aria-hidden="true"></div>
        <div class="auth-shell__hero-content">
            <p class="auth-shell__eyebrow">Staff portal</p>
            <h1 class="auth-shell__hero-title">{{ $brandName ?? config('app.name') }}</h1>
            <p class="auth-shell__hero-text">
                Manage reservations, content, and guest experiences for your eco-resort near Volcanoes National Park.
            </p>
        </div>
    </aside>

    <main class="auth-shell__panel">
        @if ($hero)
            <div
                class="auth-shell__hero-mobile"
                style="background-image: url('{{ $hero }}');"
            >
                <div class="auth-shell__hero-overlay" aria-hidden="true"></div>
                <p class="auth-shell__hero-mobile-title">{{ $brandName ?? config('app.name') }}</p>
            </div>
        @endif

        <div class="auth-shell__panel-inner">
            <div class="auth-shell__brand">
                {{ $logo }}
                <p class="auth-shell__subtitle">{{ $displaySubtitle }}</p>
            </div>

            <div class="auth-shell__card">
                {{ $slot }}
            </div>

            <p class="auth-shell__back-wrap">
                <a href="{{ url('/') }}" class="auth-shell__back">
                    ← {{ __('Back to hotel website') }}
                </a>
            </p>
        </div>
    </main>
</div>
