@php
    $homeTodaysMenu = $homeTodaysMenu ?? [];
@endphp

<section class="home-dining-choose rel z-1 py-100 rpy-70">
    <div class="home-dining-choose__inner">
        <div class="text-center mb-55 rmb-40 wow fadeInUp delay-0-1s">
            <h2 class="home-dining-choose__title">Dine with us</h2>
            <p class="home-dining-choose__lead mx-auto">Fresh, local, delicious — meals from our ecological garden and farmers across Musanze.</p>
        </div>

        <div class="home-dining-choose__row">
            <div class="home-dining-choose__col home-dining-choose__col--menu wow fadeInLeft delay-0-2s">
                @include('frontend.includes.dining-todays-menu-card', [
                    'items' => $homeTodaysMenu,
                    'mode' => 'preview',
                    'showViewFullLink' => true,
                ])
            </div>
            <div class="home-dining-choose__col home-dining-choose__col--why wow fadeInRight delay-0-2s">
                <article class="home-dining-choose__card home-dining-choose__card--why h-100">
                    <div class="home-dining-choose__card-inner home-dining-choose__card-inner--why">
                        <span class="home-dining-choose__badge home-dining-choose__badge--accent"><i class="fas fa-gem me-2" aria-hidden="true"></i>Why choose us</span>
                        <h3 class="home-dining-choose__card-title">Garden dining experience</h3>
                        <ul class="home-dining-choose__why-list">
                            <li><i class="fas fa-seedling" aria-hidden="true"></i><span>Produce from our ecological garden</span></li>
                            <li><i class="fas fa-utensils" aria-hidden="true"></i><span>Rwandan specialties &amp; international favorites</span></li>
                            <li><i class="fas fa-leaf" aria-hidden="true"></i><span>Vegetarian options &amp; locally sourced ingredients</span></li>
                        </ul>
                        <a href="{{ route('rooms') }}" class="theme-btn home-dining-choose__btn home-dining-choose__btn--solid">
                            Book now <i class="far fa-angle-right ms-2"></i>
                        </a>
                    </div>
                </article>
            </div>
        </div>
    </div>
</section>

@if (count($homeTodaysMenu) > 0)
<script>
(function () {
    function getCurrency() {
        try {
            return localStorage.getItem('dining_currency') === 'rwf' ? 'rwf' : 'usd';
        } catch (e) {
            return 'usd';
        }
    }
    function renderTodaysPreview(root) {
        var tbody = root.querySelector('.dining-todays-tbody');
        var jsonEl = root.querySelector('.dining-todays-items-json');
        if (!tbody || !jsonEl) return;
        var items = [];
        try {
            items = JSON.parse(jsonEl.textContent || '[]');
        } catch (e) {
            items = [];
        }
        var cur = getCurrency();
        tbody.innerHTML = '';
        items.forEach(function (it) {
            var tr = document.createElement('tr');
            var tdItem = document.createElement('td');
            var title = document.createElement('div');
            title.className = 'fw-semibold';
            title.textContent = it.title || '';
            tdItem.appendChild(title);
            if (it.description) {
                var d = document.createElement('div');
                d.className = 'text-muted small mt-1';
                d.textContent = it.description;
                tdItem.appendChild(d);
            }
            var tdPrice = document.createElement('td');
            tdPrice.className = 'text-end';
            tdPrice.innerHTML = cur === 'rwf' ? (it.priceHtmlRwf || it.priceHtml || '') : (it.priceHtmlUsd || it.priceHtml || '');
            tr.appendChild(tdItem);
            tr.appendChild(tdPrice);
            tbody.appendChild(tr);
        });
    }
    document.querySelectorAll('.dining-todays-menu-root[data-dining-todays-mode="preview"]').forEach(renderTodaysPreview);
})();
</script>
@endif
