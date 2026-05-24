{{-- Social profiles: Facebook, Instagram, YouTube, TikTok --}}
@php
    $style = $style ?? 'one';
    $class = $style === 'two' ? 'social-style-two' : 'social-style-one';
    $links = [
        ['url' => $setting->facebook ?? '', 'icon' => 'fab fa-facebook-f', 'label' => 'Facebook'],
        ['url' => $setting->instagram ?? '', 'icon' => 'fab fa-instagram', 'label' => 'Instagram'],
        ['url' => $setting->youtube ?? '', 'icon' => 'fab fa-youtube', 'label' => 'YouTube'],
        ['url' => $setting->tiktok ?? '', 'icon' => 'fab fa-tiktok', 'label' => 'TikTok'],
    ];
@endphp
<div class="{{ $class }} {{ $wrapperClass ?? '' }}">
    @foreach ($links as $link)
        @if (! empty(trim((string) $link['url'])))
            <a href="{{ $link['url'] }}" target="_blank" rel="noopener noreferrer" aria-label="{{ $link['label'] }}"><i class="{{ $link['icon'] }}"></i></a>
        @endif
    @endforeach
</div>
