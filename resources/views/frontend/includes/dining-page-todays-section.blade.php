@php
    use App\Support\PageHeaderResolver;

    $todaysMenu = $todaysMenu ?? [];
    $diningHeader = ($pageHeaders ?? collect())['dining'] ?? null;
    $diningCover = PageHeaderResolver::resolve('dining', $setting ?? null, $about ?? null, $diningHeader)['imageUrl']
        ?? asset('assets/img/resto.jpg');
@endphp

<section class="dining-page-todays wow fadeInUp" aria-labelledby="dining-todays-heading">
  <div class="dining-page-section-head">
    <span class="dining-cat-kicker">Chef&rsquo;s selection</span>
    <h2 class="dining-cat-title h4 mb-1" id="dining-todays-heading">Today&rsquo;s picks</h2>
    <p class="dining-page-section-head__sub small text-muted mb-0">Updated daily from our garden kitchen — add to your tray below.</p>
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
          <span class="home-dining-choose__badge home-dining-choose__badge--light"><i class="fas fa-sun me-2" aria-hidden="true"></i>Fresh today</span>
          <p class="dining-page-todays__visual-text mb-0">Seasonal plates &amp; drinks prepared in our open kitchen.</p>
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
          'useRowList' => true,
      ])
    </div>
  </div>
</section>
