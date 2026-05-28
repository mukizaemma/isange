{{-- Partner logos row — above main footer; equal height, evenly spaced --}}
@if (($partners ?? collect())->isNotEmpty())
<section class="ma-partners-strip" aria-labelledby="ma-partners-strip-heading">
    <div class="container">
        <div class="ma-partners-strip__head text-center wow fadeInUp">
            {{-- <p class="ma-partners-strip__eyebrow mb-2">In partnership with</p> --}}
            <h2 id="ma-partners-strip-heading" class="ma-partners-strip__title">In partnership with</h2>
        </div>
        <ul class="ma-partners-strip__row list-unstyled mb-0 wow fadeInUp delay-0-2s"
            style="--partner-count: {{ $partners->count() }};">
            @foreach ($partners as $partner)
                @php
                    $logoUrl = asset('storage/images/partners/' . ltrim($partner->image, '/'));
                    $website = trim((string) ($partner->website ?? ''));
                    $alt = $partner->title ?: 'Partner logo';
                @endphp
                <li class="ma-partners-strip__item">
                    @if ($website !== '')
                        <a href="{{ $website }}" class="ma-partners-strip__link" target="_blank" rel="noopener noreferrer" title="{{ $alt }}">
                            <img src="{{ $logoUrl }}" alt="{{ $alt }}" class="ma-partners-strip__logo" loading="lazy" decoding="async">
                        </a>
                    @else
                        <span class="ma-partners-strip__link ma-partners-strip__link--static">
                            <img src="{{ $logoUrl }}" alt="{{ $alt }}" class="ma-partners-strip__logo" loading="lazy" decoding="async">
                        </span>
                    @endif
                </li>
            @endforeach
        </ul>
    </div>
</section>
@endif
