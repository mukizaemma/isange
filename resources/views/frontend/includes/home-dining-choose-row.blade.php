@php
    use App\Support\PageHeaderResolver;

    $homeTodaysMenu = $homeTodaysMenu ?? [];
    $diningHeader = ($pageHeaders ?? collect())['dining'] ?? null;
    $diningCover = PageHeaderResolver::resolve('dining', $setting ?? null, $about ?? null, $diningHeader)['imageUrl']
        ?? asset('assets/img/resto.jpg');
@endphp

<section class="home-dining-choose rel z-1 py-100 rpy-70">
    <div class="home-dining-choose__inner">
        <div class="text-center mb-55 rmb-40 wow fadeInUp delay-0-1s">
            <h2 class="home-dining-choose__title">Dine with us</h2>
            <p class="home-dining-choose__lead mx-auto">Fresh, local, delicious — meals from our ecological garden and farmers across Musanze.</p>
        </div>

        <div class="home-dining-choose__row">
            <div class="home-dining-choose__col home-dining-choose__col--visual wow fadeInLeft delay-0-2s">
                <figure class="home-dining-choose__visual">
                    <img
                        class="home-dining-choose__visual-img"
                        src="{{ $diningCover }}"
                        alt="Restaurant and garden dining at Isange Paradise"
                        loading="lazy"
                        width="960"
                        height="720"
                    >
                    <figcaption class="home-dining-choose__visual-caption">
                        <span class="home-dining-choose__badge home-dining-choose__badge--light"><i class="fas fa-gem me-2" aria-hidden="true"></i>Why choose us</span>
                        <h3 class="home-dining-choose__visual-title">Garden dining experience</h3>
                        <ul class="home-dining-choose__why-list home-dining-choose__why-list--overlay">
                            <li><i class="fas fa-seedling" aria-hidden="true"></i><span>Produce from our ecological garden</span></li>
                            <li><i class="fas fa-utensils" aria-hidden="true"></i><span>Rwandan specialties &amp; international favorites</span></li>
                            <li><i class="fas fa-leaf" aria-hidden="true"></i><span>Vegetarian options &amp; locally sourced ingredients</span></li>
                        </ul>
                        <a href="{{ route('rooms') }}" class="theme-btn home-dining-choose__btn home-dining-choose__btn--solid home-dining-choose__btn--overlay">
                            Book now <i class="far fa-angle-right ms-2"></i>
                        </a>
                    </figcaption>
                </figure>
            </div>

            <div class="home-dining-choose__col home-dining-choose__col--menu wow fadeInRight delay-0-2s">
                <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-2">
                    <p class="home-dining-choose__order-hint small text-muted mb-0">Add dishes, then send your order via WhatsApp or email. Pay at the hotel.</p>
                    <div class="dining-currency-picker d-flex align-items-center gap-2 flex-shrink-0">
                        <span class="small fw-semibold">Prices in</span>
                        <div class="btn-group btn-group-sm" role="group" aria-label="Menu currency">
                            <button type="button" class="btn btn-outline-secondary active" data-dining-currency="usd">USD ($)</button>
                            <button type="button" class="btn btn-outline-secondary" data-dining-currency="rwf">RWF</button>
                        </div>
                    </div>
                </div>
                @include('frontend.includes.dining-todays-menu-card', [
                    'items' => $homeTodaysMenu,
                    'mode' => 'order',
                    'useHomeCard' => true,
                    'useRowList' => true,
                    'showViewFullLink' => true,
                ])
            </div>
        </div>
    </div>
</section>

@include('frontend.includes.dining-order-ui')
@push('scripts')
    @include('frontend.includes.dining-order-scripts')
@endpush
