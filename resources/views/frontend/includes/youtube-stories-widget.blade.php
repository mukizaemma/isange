{{-- YouTube Stories / Shorts widget — shown when embed code or channel URL is configured --}}
@php
    $embed = trim((string) ($setting->youtube_stories_embed ?? ''));
    $youtubeUrl = trim((string) ($setting->youtube ?? ''));
    $hasWidget = $embed !== '' || $youtubeUrl !== '';
    $variant = $variant ?? 'default';
@endphp
{{-- @if ($hasWidget)
<section class="isange-youtube-widget isange-youtube-widget--{{ $variant }} rel z-1">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-10 col-xl-8">
                <div class="isange-youtube-widget__inner wow fadeInUp">
                    <div class="text-center mb-4">
                        <span class="isange-section__eyebrow">Watch our stories</span>
                        <h2 class="h4 mb-2">Follow Isange on YouTube</h2>
                        <p class="text-muted small mb-0">Stories, updates, and glimpses of life at the resort and in our community.</p>
                    </div>
                    @if ($embed !== '')
                        <div class="isange-youtube-widget__embed">
                            {!! $embed !!}
                        </div>
                    @elseif ($youtubeUrl !== '')
                        <div class="isange-youtube-widget__cta text-center">
                            <a href="{{ $youtubeUrl }}" class="theme-btn style-three" target="_blank" rel="noopener noreferrer">
                                <i class="fab fa-youtube me-2"></i> Watch on YouTube
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</section>
@endif --}}
