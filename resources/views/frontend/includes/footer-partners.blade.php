{{-- Partner logos — links open in new tab when website is set --}}
@if (($partners ?? collect())->isNotEmpty())
<section class="footer-partners-band pt-60 pb-20 rel z-1">
    <div class="container">
        <div class="text-center mb-4 wow fadeInUp">
            <h4 class="footer-title mb-2">Our partners</h4>
            <p class="small mb-0 ma-footer-gold__muted">Trusted organisations we work with</p>
        </div>
        <div class="row g-4 justify-content-center align-items-center footer-partners-grid wow fadeInUp delay-0-2s">
            @foreach ($partners as $partner)
                @php
                    $logoUrl = ! empty($partner->image)
                        ? asset('storage/images/partners/' . ltrim($partner->image, '/'))
                        : null;
                    $website = trim((string) ($partner->website ?? ''));
                @endphp
                <div class="col-6 col-md-4 col-lg-3">
                    @if ($website !== '')
                        <a href="{{ $website }}" class="footer-partner-card d-block text-center" target="_blank" rel="noopener noreferrer" title="{{ $partner->title }}">
                            @if ($logoUrl)
                                <img src="{{ $logoUrl }}" alt="{{ $partner->title }}" class="footer-partner-card__logo" loading="lazy">
                            @else
                                <span class="footer-partner-card__name">{{ $partner->title }}</span>
                            @endif
                        </a>
                    @else
                        <div class="footer-partner-card d-block text-center footer-partner-card--static">
                            @if ($logoUrl)
                                <img src="{{ $logoUrl }}" alt="{{ $partner->title }}" class="footer-partner-card__logo" loading="lazy">
                            @else
                                <span class="footer-partner-card__name">{{ $partner->title }}</span>
                            @endif
                        </div>
                    @endif
                </div>
            @endforeach
        </div>
    </div>
</section>
@endif
