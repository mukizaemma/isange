@php
    $items = $items ?? [];
    $mode = $mode ?? 'preview';
    $showViewFullLink = $showViewFullLink ?? false;
    $hideCurrencyBar = $hideCurrencyBar ?? false;
    $compactHeader = $compactHeader ?? false;
    $useHomeCard = $useHomeCard ?? false;
    $useRowList = $useRowList ?? false;
    $cardClass = $mode === 'preview'
        ? 'home-dining-choose__card home-dining-choose__card--menu h-100'
        : ($useHomeCard
            ? 'home-dining-choose__card home-dining-choose__card--menu home-dining-choose__card--order h-100'
            : 'dining-todays-card dining-todays-card--page h-100 mb-0');
    $innerClass = ($mode === 'preview' || $useHomeCard)
        ? 'home-dining-choose__card-inner home-dining-choose__card-inner--menu'
        : 'dining-todays-card__inner';
@endphp

<article class="{{ $cardClass }} dining-todays-menu-root" data-dining-todays-mode="{{ $mode }}">
    <div class="{{ $innerClass }}">
        @if (! $compactHeader)
        <span class="home-dining-choose__badge"><i class="fas fa-sun me-2" aria-hidden="true"></i>Today&rsquo;s menu</span>
        <h3 class="home-dining-choose__card-title">{{ $mode === 'preview' ? "Chef-crafted plates & drinks" : "Today's menu" }}</h3>
        @endif

        @if ($mode === 'order' && ! $hideCurrencyBar)
            <div class="d-flex flex-wrap align-items-center justify-content-end gap-2 mb-3 dining-todays-currency-bar">
                <span class="small fw-semibold">Prices in</span>
                <div class="btn-group btn-group-sm" role="group" aria-label="Today's menu currency">
                    <button type="button" class="btn btn-outline-secondary active" data-dining-currency="usd">USD ($)</button>
                    <button type="button" class="btn btn-outline-secondary" data-dining-currency="rwf">RWF</button>
                </div>
            </div>
        @endif

        @if (count($items) === 0)
            <p class="text-muted small py-3 mb-0">Today's menu is being prepared. Browse the full menu below to order.</p>
        @elseif ($useRowList && $mode === 'order')
            <div class="dining-todays-items dining-todays-items--rows"></div>
            <script type="application/json" class="dining-todays-items-json">@json($items)</script>
        @else
            <div class="table-responsive home-dining-tcol__wrap border rounded-3 overflow-hidden bg-white mb-3{{ $mode === 'preview' ? ' home-dining-tcol__wrap--preview' : '' }}">
                <table class="table table-sm table-striped home-dining-mini-table dining-todays-table align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th scope="col">Item</th>
                            <th scope="col" class="text-end text-nowrap" style="width:6.5rem;">Price</th>
                            @if ($mode === 'order')
                                <th scope="col" class="text-end text-nowrap" style="width:5rem;"><span class="visually-hidden">Add</span></th>
                            @endif
                        </tr>
                    </thead>
                    <tbody class="dining-todays-tbody"></tbody>
                </table>
            </div>
            <script type="application/json" class="dining-todays-items-json">@json($items)</script>
        @endif

        @if ($showViewFullLink)
            <a href="{{ route('dining') }}" class="theme-btn style-three home-dining-choose__btn mt-1">
                View full menu <i class="far fa-angle-right ms-2"></i>
            </a>
        @endif
    </div>
</article>
