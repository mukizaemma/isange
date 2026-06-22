@php
    use App\Support\PageHeaderResolver;

    $homeTodaysMenu = $homeTodaysMenu ?? [];
    $diningHeader = ($pageHeaders ?? collect())['dining'] ?? null;
    $diningCover = PageHeaderResolver::resolve('dining', $setting ?? null, $about ?? null, $diningHeader)['imageUrl']
        ?? asset('assets/img/resto.jpg');
@endphp

<section class="home-dining-choose home-dining-choose--hero rel z-1" aria-labelledby="home-dining-heading">
    <div class="home-dining-choose__bg" aria-hidden="true">
        <img
            class="home-dining-choose__bg-img"
            src="{{ $diningCover }}"
            alt=""
            loading="lazy"
            width="1920"
            height="1080"
        >
    </div>
    <div class="home-dining-choose__scrim" aria-hidden="true"></div>

    <div class="home-dining-choose__inner home-dining-choose__inner--hero">
        <div class="text-center mb-40 rmb-30 wow fadeInUp delay-0-1s">
            <h2 class="home-dining-choose__title" id="home-dining-heading">Dine with us</h2>
            <p class="home-dining-choose__lead mx-auto mb-0">Fresh, local, delicious — meals from our ecological garden and farmers across Musanze.</p>
        </div>

        <div class="d-flex flex-wrap align-items-center justify-content-end gap-3 mb-3 wow fadeInUp delay-0-15s">
            <div class="dining-currency-picker dining-currency-picker--on-dark d-flex align-items-center gap-2 flex-shrink-0">
                <span class="small fw-semibold">Prices in</span>
                <div class="btn-group btn-group-sm" role="group" aria-label="Menu currency">
                    <button type="button" class="btn btn-outline-secondary active" data-dining-currency="usd">USD ($)</button>
                    <button type="button" class="btn btn-outline-secondary" data-dining-currency="rwf">RWF</button>
                </div>
            </div>
        </div>

        <div class="wow fadeInUp delay-0-2s">
            @include('frontend.includes.dining-todays-menu-card', [
                'items' => $homeTodaysMenu,
                'mode' => 'order',
                'heroOverlay' => true,
                'useCardGrid' => true,
                'showViewFullLink' => true,
                'hideCurrencyBar' => true,
                'compactHeader' => true,
            ])
        </div>
    </div>
</section>

@include('frontend.includes.dining-order-ui')
@push('scripts')
    @include('frontend.includes.dining-order-scripts')
@endpush
