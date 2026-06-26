@props([
    'class' => '',
])

@php
    $turnstileSiteKey = \App\Support\SpamProtection::siteKey();
@endphp

<div {{ $attributes->merge(['class' => 'human-form-protection '.$class]) }}>
    <div
        class="human-form-protection__honeypot"
        aria-hidden="true"
        style="position:absolute;left:-9999px;width:1px;height:1px;overflow:hidden;"
        tabindex="-1"
    >
        <label for="_hp_website">Website</label>
        <input type="text" name="_hp_website" value="" autocomplete="off" tabindex="-1">
    </div>
    <input type="hidden" name="_form_ts" value="{{ time() }}">
    @if ($turnstileSiteKey)
        <div class="human-form-protection__turnstile mb-3">
            <div class="cf-turnstile" data-sitekey="{{ $turnstileSiteKey }}"></div>
        </div>
        @once
            @push('scripts')
                <script src="https://challenges.cloudflare.com/turnstile/v0/api.js" async defer></script>
            @endpush
        @endonce
    @endif
</div>
