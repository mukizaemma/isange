@props([
    'subtitle' => null,
])

<div class="relative flex min-h-screen flex-col items-center justify-center overflow-hidden bg-gradient-to-br from-neutral-950 via-neutral-900 to-neutral-950 px-4 py-12 sm:py-0">
    <div class="pointer-events-none absolute inset-0 bg-[radial-gradient(ellipse_at_top,_rgba(230,145,56,0.14)_0%,_transparent_55%)]"></div>
    <div class="pointer-events-none absolute -left-24 top-1/4 h-64 w-64 rounded-full bg-[#e69138]/5 blur-3xl"></div>
    <div class="pointer-events-none absolute -right-24 bottom-1/4 h-64 w-64 rounded-full bg-[#e69138]/8 blur-3xl"></div>

    <div class="relative w-full sm:max-w-md">
        <div class="mb-8 text-center">
            {{ $logo }}
            @if ($subtitle)
                <p class="mt-4 text-sm font-semibold uppercase tracking-[0.2em] text-amber-100/90">
                    {{ $subtitle }}
                </p>
            @endif
        </div>

        <div class="rounded-2xl border border-amber-500/25 bg-white/98 px-8 py-8 shadow-2xl shadow-black/50 backdrop-blur-sm sm:px-10">
            {{ $slot }}
        </div>

        <p class="mt-8 text-center text-xs text-neutral-500">
            <a href="{{ url('/') }}" class="font-medium text-[#c2782f] transition hover:text-[#e69138] focus:outline-none focus-visible:underline">
                ← {{ __('Back to hotel website') }}
            </a>
        </p>
    </div>
</div>
