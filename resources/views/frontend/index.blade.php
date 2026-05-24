@extends('layouts.frontbase')

@section('content')

@php
    use App\Support\PageHeaderResolver;

    $welcomeHeader = ($pageHeaders ?? collect())['about'] ?? null;
    $welcomeImage = PageHeaderResolver::resolve('about', $setting, $about, $welcomeHeader)['imageUrl']
        ?? (! empty($about?->aboutImage)
            ? asset('storage/images/gallery/' . ltrim($about->aboutImage, '/'))
            : null);

    $welcomeParagraphs = [];
    $welcomeHtml = $about->welcome ?? '';
    if ($welcomeHtml !== '') {
        if (preg_match_all('/<p\b[^>]*>.*?<\/p>/is', $welcomeHtml, $welcomeMatches)) {
            $welcomeParagraphs = $welcomeMatches[0];
        } else {
            $plain = trim(preg_replace('/\s+/', ' ', strip_tags($welcomeHtml)));
            if ($plain !== '') {
                foreach (preg_split('/(?:\r\n|\r|\n){2,}/', $welcomeHtml) as $chunk) {
                    $chunk = trim(strip_tags($chunk));
                    if ($chunk !== '') {
                        $welcomeParagraphs[] = '<p>' . e($chunk) . '</p>';
                    }
                }
                if ($welcomeParagraphs === [] && $plain !== '') {
                    $welcomeParagraphs[] = '<p>' . e($plain) . '</p>';
                }
            }
        }
    }
    $welcomePreview = array_slice($welcomeParagraphs, 0, 2);
    $welcomeHasMore = count($welcomeParagraphs) > 2;

    $home = \App\Support\PageContent::get('home', $pageHeaders ?? collect());
    $hs = $home['sections'];
    $whyCards = $hs['why_cards'] ?? [];
    $eventTypes = $hs['events_types'] ?? [];
@endphp

{{-- 1. HERO (full-width slides — header & caption from admin) --}}
<section class="isange-hero isange-hero--full rel z-1">
    @if ($slides->isNotEmpty())
        <div class="slider-two-active isange-hero-slider">
            @foreach ($slides as $slide)
                <div
                    class="slider-item-two isange-hero-slide parallax-bg"
                    style="background-image: url('{{ asset('storage/images/slides') . $slide->image }}');"
                >
                    <div class="isange-hero__overlay" aria-hidden="true"></div>
                    <div class="isange-hero__content-wrap">
                        <div class="isange-hero__content wow fadeInUp delay-0-2s">
                            @if (! empty($slide->heading))
                                <h1 class="isange-hero__title">{{ $slide->heading }}</h1>
                            @endif
                            @if (! empty($slide->subheading))
                                <p class="isange-hero__caption">{{ $slide->subheading }}</p>
                            @endif
                            <div class="isange-hero__actions">
                                <a href="{{ route('room.booking') }}" class="theme-btn">Book Your Stay <i class="far fa-angle-right"></i></a>
                                <a href="https://www.future4kids.at/" class="theme-btn style-three" target="_blank" rel="noopener noreferrer">Future 4 Kids <i class="fas fa-external-link-alt"></i></a>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="isange-hero-slide isange-hero-slide--fallback">
            <div class="isange-hero__overlay" aria-hidden="true"></div>
            <div class="isange-hero__content-wrap">
                <div class="isange-hero__content">
                    <h1 class="isange-hero__title">Isange Paradise Eco Resort</h1>
                    <p class="isange-hero__caption">Add slides in the admin panel to customize this hero.</p>
                    <div class="isange-hero__actions">
                        <a href="{{ route('room.booking') }}" class="theme-btn">Book Your Stay <i class="far fa-angle-right"></i></a>
                        <a href="https://www.future4kids.at/" class="theme-btn style-three" target="_blank" rel="noopener noreferrer">Future 4 Kids <i class="fas fa-external-link-alt"></i></a>
                    </div>
                </div>
            </div>
        </div>
    @endif
</section>

{{-- 2. ABOUT --}}
<section class="isange-section isange-section--cream rel z-1">
    <div class="container">
        <div class="row align-items-center g-4 g-lg-5">
            @if ($welcomeImage)
            <div class="col-lg-5 wow fadeInLeft delay-0-2s">
                <figure class="isange-about-media mb-0">
                    <img src="{{ $welcomeImage }}" alt="Isange Paradise Eco Resort in Musanze" loading="lazy" width="640" height="480">
                </figure>
            </div>
            @endif
            <div class="col-lg-{{ $welcomeImage ? '7' : '12' }} wow fadeInUp delay-0-2s">
                {{-- <span class="isange-section__eyebrow">{{ $hs['about_eyebrow'] ?? 'About us' }}</span> --}}
                <h2>{{ $hs['about_title'] ?? 'Welcome to Isange Paradise' }}</h2>
                @if (count($welcomePreview) > 0)
                    <div class="welcome-prose isange-about-excerpt mb-3">
                        @foreach ($welcomePreview as $paragraph)
                            {!! $paragraph !!}
                        @endforeach
                    </div>
                    @if ($welcomeHasMore)
                        <a href="{{ route('aboutUs') }}" class="theme-btn style-three">View more <i class="far fa-angle-right"></i></a>
                    @endif
                @else
                    <p class="text-muted mb-3">Add your welcome text in the admin About section.</p>
                    <a href="{{ route('aboutUs') }}" class="theme-btn style-three">About us <i class="far fa-angle-right"></i></a>
                @endif
            </div>
        </div>
    </div>
</section>

{{-- 3. ACCOMMODATION --}}
<section class="room-area-three rooms-on-white isange-section rel z-1 bgc-white">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-xl-8 col-lg-10 text-center mb-50 wow fadeInUp delay-0-2s">
                {{-- <span class="isange-section__eyebrow">{{ $hs['accommodation_eyebrow'] ?? 'Accommodation' }}</span> --}}
                <h2>{{ $hs['accommodation_title'] ?? 'Stay in Comfort, Surrounded by Nature' }}</h2>
                <p class="mb-0">{{ $hs['accommodation_intro'] ?? 'Each room is thoughtfully designed to offer comfort, privacy, and beautiful garden views.' }}</p>
            </div>
        </div>
        <div class="row g-4 justify-content-center">
            @forelse ($rooms->take(6) as $room)
                <div class="col-md-6 col-lg-4 wow fadeInUp delay-0-2s">
                    <article class="room-two-item home-room-card h-100 d-flex flex-column">
                        <div class="image home-room-card__image">
                            <img class="home-room-card__img" src="{{ asset('storage/images/rooms/' . $room->image) }}" alt="{{ $room->roomName }}" loading="lazy" width="800" height="500">
                        </div>
                        <div class="content flex-grow-1 d-flex flex-column">
                            <h3 class="mb-15"><a href="{{ route('singleRoom', ['slug' => $room->slug]) }}">{{ $room->roomName }}</a></h3>
                            @if (! empty(trim(strip_tags($room->description ?? ''))))
                                <p class="text-muted mb-3 flex-grow-1">{!! \Illuminate\Support\Str::limit(strip_tags($room->description), 140) !!}</p>
                            @endif
                            <div class="d-flex flex-wrap gap-2 mt-auto">
                                <a href="{{ route('singleRoom', ['slug' => $room->slug]) }}" class="theme-btn style-three home-room-card__btn flex-grow-1 d-inline-flex justify-content-center align-items-center">
                                    View Details <i class="far fa-angle-right"></i>
                                </a>
                                <a href="{{ route('room.booking') }}" class="theme-btn home-room-card__btn flex-grow-1 d-inline-flex justify-content-center align-items-center">
                                    Book <i class="far fa-angle-right"></i>
                                </a>
                            </div>
                        </div>
                    </article>
                </div>
            @empty
                <div class="col-12 text-center text-muted py-4">
                    <p>Accommodation listings coming soon. <a href="{{ route('contact') }}">Contact us</a> to book.</p>
                </div>
            @endforelse
        </div>
        @if ($rooms->count() > 6)
            <div class="text-center mt-50 wow fadeInUp delay-0-2s">
                <a href="{{ route('rooms') }}" class="theme-btn style-three">View all rooms <i class="far fa-angle-right"></i></a>
            </div>
        @endif
        <div class="text-center mt-40 wow fadeInUp">
            <p class="small text-muted mb-0">{{ $hs['accommodation_footnote'] ?? 'Amenities include private bathroom, hot shower, balcony or terrace, garden access, and comfortable bedding.' }}</p>
        </div>
    </div>
</section>

{{-- 4. WHY STAY --}}
<section class="isange-section isange-section--cream rel z-1">
    <div class="container">
        <div class="row justify-content-center mb-50">
            <div class="col-lg-8 text-center wow fadeInUp delay-0-2s">
                <span class="isange-section__eyebrow">{{ $hs['why_eyebrow'] ?? 'Why stay with us' }}</span>
                <h2>{{ $hs['why_title'] ?? 'Why Guests Love Isange Paradise' }}</h2>
            </div>
        </div>
        <div class="row g-4">
            @foreach ($whyCards as $i => $card)
            <div class="col-md-6 col-lg-3 wow fadeInUp delay-0-{{ min(2 + $i, 5) }}s">
                <div class="isange-why-card h-100">
                    <div class="isange-why-card__icon"><i class="fas {{ $card['icon'] ?? 'fa-star' }}" aria-hidden="true"></i></div>
                    <h3>{{ $card['title'] ?? '' }}</h3>
                    <p class="mb-0">{{ $card['text'] ?? '' }}</p>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>

{{-- 5. EXPERIENCES --}}
<section class="isange-section rel z-1 bgc-white">
    <div class="container">
        <div class="row g-4 align-items-start">
            <div class="col-lg-5 wow fadeInUp delay-0-2s">
                <span class="isange-section__eyebrow">{{ $hs['experiences_eyebrow'] ?? 'Experiences' }}</span>
                <h2>{{ $hs['experiences_title'] ?? 'Explore Northern Rwanda' }}</h2>
                <p>{{ $hs['experiences_intro'] ?? 'From iconic gorilla trekking to cultural village visits — we can help arrange your full adventure itinerary.' }}</p>
                <a href="{{ route('experiences') }}" class="theme-btn mt-3">All experiences <i class="far fa-angle-right"></i></a>
            </div>
            <div class="col-lg-7 wow fadeInUp delay-0-3s">
                <ul class="isange-experience-list">
                    <li><a href="{{ route('experiences') }}#gorilla"><i class="fas fa-paw" aria-hidden="true"></i> Gorilla Trekking</a></li>
                    <li><a href="{{ route('experiences') }}#golden-monkey"><i class="fas fa-tree" aria-hidden="true"></i> Golden Monkey Trekking</a></li>
                    <li><a href="{{ route('experiences') }}#volcano"><i class="fas fa-mountain" aria-hidden="true"></i> Volcano Hiking</a></li>
                    <li><a href="{{ route('experiences') }}#caves"><i class="fas fa-dungeon" aria-hidden="true"></i> Musanze Caves tours</a></li>
                    <li><a href="{{ route('experiences') }}#lakes"><i class="fas fa-water" aria-hidden="true"></i> Twin Lakes excursions</a></li>
                    <li><a href="{{ route('experiences') }}#culture"><i class="fas fa-people-arrows" aria-hidden="true"></i> Cultural village experiences</a></li>
                    <li><a href="{{ route('experiences') }}#birds"><i class="fas fa-dove" aria-hidden="true"></i> Bird watching</a></li>
                    <li><a href="{{ route('experiences') }}#cycling"><i class="fas fa-bicycle" aria-hidden="true"></i> Cycling tours</a></li>
                    <li><a href="{{ route('experiences') }}#community"><i class="fas fa-hands-helping" aria-hidden="true"></i> Community visits</a></li>
                </ul>
            </div>
        </div>
    </div>
</section>

{{-- 6. RESTAURANT --}}
@include('frontend.includes.home-dining-choose-row')

{{-- 7. EVENTS & GARDEN --}}
<section class="isange-section rel z-1 bgc-white">
    <div class="container">
        <div class="row justify-content-center text-center mb-40 wow fadeInUp">
            <div class="col-lg-8">
                {{-- <span class="isange-section__eyebrow">{{ $hs['events_eyebrow'] ?? 'Events & garden' }}</span> --}}
                <h2>{{ $hs['events_title'] ?? 'Celebrate in Nature' }}</h2>
                <p class="mb-0">{{ $hs['events_intro'] ?? '' }}</p>
                @if (! empty($hs['events_capacity']))
                <span class="isange-events-capacity">{{ $hs['events_capacity'] }}</span>
                @endif
            </div>
        </div>
        <div class="row g-3 justify-content-center">
            @foreach ($eventTypes as $eventType)
            <div class="col-6 col-md-4 col-lg wow fadeInUp delay-0-2s">
                <div class="isange-why-card py-4">
                    <h3 class="h5 mb-0">{{ $eventType }}</h3>
                </div>
            </div>
            @endforeach
        </div>
        <div class="text-center mt-40">
            <a href="{{ route('facilities') }}#garden" class="theme-btn style-three">Explore our gardens <i class="far fa-angle-right"></i></a>
        </div>
    </div>
</section>

@include('frontend.includes.youtube-stories-widget', ['variant' => 'white'])

{{-- 9. FINAL CTA --}}
<section class="isange-cta-band rel z-1 wow fadeInUp">
    <div class="container">
        <h2>{{ $hs['cta_title'] ?? 'Book Your Eco Stay' }}</h2>
        <p>{{ $hs['cta_text'] ?? '' }}</p>
        <a href="{{ route('room.booking') }}" class="theme-btn">Book Your Stay <i class="far fa-angle-right"></i></a>
        <a href="{{ route('contact') }}" class="theme-btn style-three ms-2 mt-2 mt-md-0">Contact us <i class="far fa-angle-right"></i></a>
    </div>
</section>

@endsection
