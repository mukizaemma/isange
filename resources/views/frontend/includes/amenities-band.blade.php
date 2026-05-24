@php
    $amenitiesBg = null;
    if (! empty($setting->flexible_stay_bg_image ?? null)) {
        $amenitiesBg = asset('storage/images/pages/' . ltrim($setting->flexible_stay_bg_image, '/'));
    } elseif (! empty($setting->facilities_hero_image ?? null)) {
        $amenitiesBg = asset('storage/images/pages/' . ltrim($setting->facilities_hero_image, '/'));
    }

    $items = [
        [
            'emoji' => '🌿',
            'title' => 'Eco-Friendly Resort',
            'text' => 'Solar energy, organic gardens, and sustainable operations near Volcanoes National Park.',
            'href' => route('aboutUs'),
        ],
        [
            'emoji' => '🍽️',
            'title' => 'Restaurant & Bar',
            'text' => 'Fresh meals from our garden and local farmers — breakfast, lunch, and dinner.',
            'href' => route('dining'),
        ],
        [
            'emoji' => '🦍',
            'title' => 'Gorilla Trekking Access',
            'text' => '15 minutes drive from/to Volcanoes National Park office for unforgettable mountain gorilla encounters.',
            'href' => route('experiences') . '#gorilla',
        ],
        [
            'emoji' => '🌺',
            'title' => 'Tropical Gardens',
            'text' => 'Peaceful outdoor spaces for relaxation, events, and garden dining.',
            'href' => route('facilities') . '#garden',
        ],
        [
            'emoji' => '🛏️',
            'title' => 'Comfortable Rooms',
            'text' => 'Private bathrooms, Wi-Fi, balconies, and garden views for every traveler.',
            'href' => route('rooms'),
        ],
        [
            'emoji' => '🎪',
            'title' => 'Events & Celebrations',
            'text' => 'Weddings, retreats, and conferences for up to 150 guests in nature.',
            'href' => route('facilities'),
        ],
        [
            'emoji' => '🛍️',
            'title' => 'Future 4 Kids Shop',
            'text' => 'Handmade Rwandan crafts and fashion — every purchase empowers local artisans.',
            'href' => route('future4kids') . '#shop',
        ],
        [
            'emoji' => '💚',
            'title' => 'Social Impact',
            'text' => '100% of profits support education, health, and community programs.',
            'href' => route('future4kids'),
        ],
        [
            'emoji' => '📶',
            'title' => 'Free Wi-Fi',
            'text' => 'Stay connected throughout the resort during your Musanze adventure.',
            'href' => route('facilities'),
        ],
    ];
@endphp

<section class="amenities-band parallax-bg rel z-1" @if($amenitiesBg) style="background-image: url('{{ $amenitiesBg }}');" @endif aria-labelledby="amenities-band-heading">
    <div class="container container-1130 rel z-2">
        <div class="row justify-content-center">
            <div class="col-xl-10 col-lg-11">
                <div class="section-title text-center mb-45 wow fadeInUp delay-0-2s">
                    <h2 id="amenities-band-heading">Resort Facilities</h2>
                    <p class="amenities-band-lead mt-20 mb-0">
                        Isange Paradise Eco Resort blends comfort, nature, and purpose — explore what awaits you in Musanze.
                    </p>
                </div>
            </div>
        </div>

        <div class="row g-4 justify-content-center amenities-band-grid">
            @foreach ($items as $item)
                <div class="col-xl-4 col-lg-4 col-md-6">
                    <a href="{{ $item['href'] }}" class="amenities-band-card wow fadeInUp delay-0-2s text-decoration-none d-block h-100">
                        <div class="amenities-band-card-head">
                            <span class="amenities-band-emoji" aria-hidden="true">{{ $item['emoji'] }}</span>
                            <h3 class="amenities-band-card-title">{{ $item['title'] }}</h3>
                        </div>
                        <p class="amenities-band-card-text mb-0">{{ $item['text'] }}</p>
                    </a>
                </div>
            @endforeach
        </div>
    </div>
</section>
