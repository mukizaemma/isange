@php
    use App\Support\PageHeaderResolver;

    $todaysMenu = $todaysMenu ?? [];
    $diningHeader = ($pageHeaders ?? collect())['dining'] ?? null;
    $diningCover = PageHeaderResolver::resolve('dining', $setting ?? null, $about ?? null, $diningHeader)['imageUrl']
        ?? asset('assets/img/resto.jpg');
@endphp

<section class="dining-page-todays mb-45 rmb-35 wow fadeInUp" aria-labelledby="dining-todays-heading">
  <div class="d-flex flex-wrap align-items-end justify-content-between gap-3 mb-3">
    <h2 class="h5 mb-0" id="dining-todays-heading">Today&rsquo;s menu</h2>
    <div class="dining-currency-picker d-flex align-items-center gap-2">
      <span class="small fw-semibold">Prices in</span>
      <div class="btn-group btn-group-sm" role="group" aria-label="Today's menu currency">
        <button type="button" class="btn btn-outline-secondary active" data-dining-currency="usd">USD ($)</button>
        <button type="button" class="btn btn-outline-secondary" data-dining-currency="rwf">RWF</button>
      </div>
    </div>
  </div>

  <div class="dining-page-todays__row">
    <div class="dining-page-todays__visual-col">
      <figure class="dining-page-todays__visual">
        <img
          class="dining-page-todays__visual-img"
          src="{{ $diningCover }}"
          alt="Restaurant and bar at Isange Paradise"
          loading="lazy"
          width="960"
          height="720"
        >
        <figcaption class="dining-page-todays__visual-caption">
          <span class="home-dining-choose__badge home-dining-choose__badge--light"><i class="fas fa-sun me-2" aria-hidden="true"></i>Today&rsquo;s picks</span>
          <p class="dining-page-todays__visual-text mb-0">Chef&rsquo;s daily selection — fresh from our garden kitchen.</p>
        </figcaption>
      </figure>
    </div>

    <div class="dining-page-todays__menu-col">
      @include('frontend.includes.dining-todays-menu-card', [
          'items' => $todaysMenu,
          'mode' => 'order',
          'showViewFullLink' => false,
          'hideCurrencyBar' => true,
          'compactHeader' => true,
      ])
    </div>
  </div>
</section>
