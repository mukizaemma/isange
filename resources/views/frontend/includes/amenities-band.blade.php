@php
    $globalContent = \App\Support\PageContent::get('global', $pageHeaders ?? collect());
    $gs = $globalContent['sections'];

    if (request()->routeIs('dining')) {
        return;
    }

    $amenitiesBg = null;
    if (! empty($setting->flexible_stay_bg_image ?? null)) {
        $amenitiesBg = asset('storage/images/pages/' . ltrim($setting->flexible_stay_bg_image, '/'));
    } elseif (! empty($setting->facilities_hero_image ?? null)) {
        $amenitiesBg = asset('storage/images/pages/' . ltrim($setting->facilities_hero_image, '/'));
    }

    $items = $gs['amenities_items'] ?? [];
    $resolveHref = function ($href) {
        if ($href === '' || $href === null) {
            return route('home');
        }
        if (str_starts_with($href, 'http://') || str_starts_with($href, 'https://')) {
            return $href;
        }

        return url($href);
    };
@endphp

<section class="amenities-band parallax-bg rel z-1" @if($amenitiesBg) style="background-image: url('{{ $amenitiesBg }}');" @endif aria-labelledby="amenities-band-heading">
    <div class="container container-1130 rel z-2">
        <div class="row justify-content-center">
            <div class="col-xl-10 col-lg-11">
                <div class="section-title text-center mb-45 wow fadeInUp delay-0-2s">
                    <h2 id="amenities-band-heading">{{ $gs['amenities_title'] ?? 'Resort Facilities' }}</h2>
                    <p class="amenities-band-lead mt-20 mb-0">
                        {{ $gs['amenities_lead'] ?? 'Isange Paradise Eco Resort blends comfort, nature, and purpose — explore what awaits you in Musanze.' }}
                    </p>
                </div>
            </div>
        </div>

        <div class="row g-4 justify-content-center amenities-band-grid">
            @foreach ($items as $item)
                <div class="col-xl-4 col-lg-4 col-md-6">
                    <a href="{{ $resolveHref($item['href'] ?? '/') }}" class="amenities-band-card wow fadeInUp delay-0-2s text-decoration-none d-block h-100">
                        <div class="amenities-band-card-head">
                            <span class="amenities-band-emoji" aria-hidden="true">{{ $item['emoji'] ?? '' }}</span>
                            <h3 class="amenities-band-card-title">{{ $item['title'] ?? '' }}</h3>
                        </div>
                        <p class="amenities-band-card-text mb-0">{{ $item['text'] ?? '' }}</p>
                    </a>
                </div>
            @endforeach
        </div>
    </div>
</section>
