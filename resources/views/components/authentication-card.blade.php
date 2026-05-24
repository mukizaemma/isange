@props([
    'subtitle' => null,
])

@php
    $displaySubtitle = $subtitle ?? (($brandName ?? config('app.name')).' · Admin');
    $hero = $authHeroImage ?? null;
@endphp

<div class="auth-shell relative flex min-h-screen flex-col lg:flex-row">
    {{-- Hero panel --}}
    <div
        class="auth-shell__hero relative hidden min-h-[220px] flex-1 overflow-hidden lg:flex lg:min-h-screen"
        @if ($hero)
            style="background-image: url('{{ $hero }}');"
        @endif
    >
        <div class="auth-shell__hero-overlay absolute inset-0"></div>
        <div class="relative z-10 flex h-full w-full flex-col justify-end p-10 xl:p-14">
            <p class="auth-shell__eyebrow mb-3 text-sm font-semibold uppercase tracking-[0.22em] text-white/90">
                Staff portal
            </p>
            <h1 class="auth-shell__hero-title max-w-md text-3xl font-semibold leading-tight text-white xl:text-4xl">
                {{ $brandName ?? config('app.name') }}
            </h1>
            <p class="auth-shell__hero-text mt-4 max-w-sm text-base leading-relaxed text-white/88">
                Manage reservations, content, and guest experiences for your eco-resort near Volcanoes National Park.
            </p>
        </div>
    </div>

    {{-- Form panel --}}
    <div class="auth-shell__panel flex w-full flex-col justify-center px-5 py-10 sm:px-8 lg:w-[min(100%,520px)] lg:flex-none lg:px-12 xl:px-16">
        @if ($hero)
            <div
                class="auth-shell__hero-mobile relative mb-8 h-36 overflow-hidden rounded-2xl lg:hidden"
                style="background-image: url('{{ $hero }}');"
            >
                <div class="auth-shell__hero-overlay absolute inset-0 rounded-2xl"></div>
                <div class="relative z-10 flex h-full items-end p-5">
                    <p class="text-sm font-semibold text-white">{{ $brandName ?? config('app.name') }}</p>
                </div>
            </div>
        @endif
        <div class="mx-auto w-full max-w-md">
            <div class="mb-8 text-center lg:mb-10">
                {{ $logo }}
                <p class="auth-shell__subtitle mt-4 text-sm font-semibold uppercase tracking-[0.18em] text-[#106b38]">
                    {{ $displaySubtitle }}
                </p>
            </div>

            <div class="auth-shell__card rounded-2xl border border-[#106b38]/12 bg-white px-7 py-8 shadow-xl shadow-[#106b38]/8 sm:px-9">
                {{ $slot }}
            </div>

            <p class="mt-8 text-center text-sm text-neutral-500">
                <a href="{{ url('/') }}" class="auth-shell__back font-medium text-[#106b38] transition hover:text-[#e85e26] focus:outline-none focus-visible:underline">
                    ← {{ __('Back to hotel website') }}
                </a>
            </p>
        </div>
    </div>
</div>
