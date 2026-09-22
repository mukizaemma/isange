<section class="facility-grid-section isange-section">
    <div class="container">
        <div class="section-title text-center mb-5">
            <h2>Our Facilities</h2>
            <p class="text-muted mb-0">Eco-friendly spaces designed for comfort, events, and unforgettable stays near Volcanoes National Park</p>
        </div>
        <div class="row g-4 align-items-stretch">
            @foreach ($facilities as $rs)
                @php
                    $facilityPlain = trim(preg_replace(
                        '/\s+/u',
                        ' ',
                        html_entity_decode(preg_replace('/<[^>]+>/', ' ', (string) ($rs->description ?? '')), ENT_QUOTES | ENT_HTML5, 'UTF-8')
                    ));
                    $facilityExcerpt = $facilityPlain !== ''
                        ? \Illuminate\Support\Str::words($facilityPlain, 32, '…')
                        : '';
                @endphp
                <div class="col-lg-4 col-md-6 d-flex">
                    <article class="facility-card" @if (! empty($rs->slug)) id="{{ $rs->slug }}" @endif>
                        <a class="facility-card__media" href="{{ route('facilitySingle', ['slug' => $rs->slug]) }}">
                            @if (! empty($rs->image))
                                <img src="{{ asset('storage/images/facilities/'.$rs->image) }}" alt="{{ $rs->title }}" loading="lazy">
                            @endif
                        </a>
                        <div class="facility-card__body">
                            <h3 class="facility-card__title">
                                <a href="{{ route('facilitySingle', ['slug' => $rs->slug]) }}">{{ $rs->title }}</a>
                            </h3>
                            @if ($facilityExcerpt !== '')
                                <p class="facility-card__excerpt">{{ $facilityExcerpt }}</p>
                            @else
                                <p class="facility-card__excerpt"></p>
                            @endif
                            <a href="{{ route('facilitySingle', ['slug' => $rs->slug]) }}" class="facility-card__more">Learn more</a>
                        </div>
                    </article>
                </div>
            @endforeach
        </div>
    </div>
</section>
