<section class="home-dining-choose rel z-1 py-100 rpy-70">
    <div class="home-dining-choose__inner">
        <div class="text-center mb-55 rmb-40 wow fadeInUp delay-0-1s">
            <span class="home-dining-choose__kicker">Dine with us</span>
            <h2 class="home-dining-choose__title">Restaurant &amp; Bar</h2>
            <p class="home-dining-choose__lead mx-auto">Fresh, local, delicious — meals from our ecological garden and farmers across Musanze.</p>
        </div>

        <div class="home-dining-choose__row">
            <div class="home-dining-choose__col home-dining-choose__col--menu wow fadeInLeft delay-0-2s">
                <article class="home-dining-choose__card home-dining-choose__card--menu h-100">
                    <div class="home-dining-choose__card-inner home-dining-choose__card-inner--menu">
                        <span class="home-dining-choose__badge"><i class="fas fa-utensils me-2" aria-hidden="true"></i>Restaurant menu</span>
                        <h3 class="home-dining-choose__card-title">Chef-crafted plates &amp; drinks</h3>
                        <p class="home-dining-choose__card-text mb-3">Food and beverages at a glance — ten items per column. Open the full menu to order.</p>

                        <script type="application/json" id="home-dining-two-col-data">@json($homeDiningTwoColumns)</script>
                        <div id="home-dining-menu-empty" class="home-dining-menu-empty text-muted small py-3 d-none">Menu coming soon.</div>
                        <div id="home-dining-dual-tables" class="d-none w-100" data-dining-url="{{ route('dining') }}">
                            <div class="row g-3 g-lg-4">
                                <div class="col-12 col-lg-6">
                                    <div class="home-dining-tcol">
                                        <h4 class="home-dining-tcol__title mb-2" id="home-dining-col-title-0"></h4>
                                        <div class="table-responsive home-dining-tcol__wrap border rounded-3 overflow-hidden bg-white">
                                            <table class="table table-sm table-striped home-dining-mini-table align-middle mb-0">
                                                <thead class="table-light">
                                                    <tr>
                                                        <th scope="col">Item</th>
                                                        <th scope="col" class="text-end text-nowrap" style="width:6.5rem;">Price</th>
                                                    </tr>
                                                </thead>
                                                <tbody id="home-dining-tbody-0"></tbody>
                                            </table>
                                        </div>
                                        <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mt-2" id="home-dining-pager-0"></div>
                                    </div>
                                </div>
                                <div class="col-12 col-lg-6">
                                    <div class="home-dining-tcol">
                                        <h4 class="home-dining-tcol__title mb-2" id="home-dining-col-title-1"></h4>
                                        <div class="table-responsive home-dining-tcol__wrap border rounded-3 overflow-hidden bg-white">
                                            <table class="table table-sm table-striped home-dining-mini-table align-middle mb-0">
                                                <thead class="table-light">
                                                    <tr>
                                                        <th scope="col">Item</th>
                                                        <th scope="col" class="text-end text-nowrap" style="width:6.5rem;">Price</th>
                                                    </tr>
                                                </thead>
                                                <tbody id="home-dining-tbody-1"></tbody>
                                            </table>
                                        </div>
                                        <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mt-2" id="home-dining-pager-1"></div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <a href="{{ route('dining') }}" class="theme-btn style-three home-dining-choose__btn mt-2">
                            View full menu <i class="far fa-angle-right ms-2"></i>
                        </a>
                    </div>
                </article>
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

<script>
(function () {
    var PER = 10;
    var elJson = document.getElementById('home-dining-two-col-data');
    var cols = [];
    try {
        cols = elJson && elJson.textContent ? JSON.parse(elJson.textContent) : [];
    } catch (e) {
        cols = [];
    }
    var empty = document.getElementById('home-dining-menu-empty');
    var dual = document.getElementById('home-dining-dual-tables');
    var pageIndex = [0, 0];

    if (!cols.length || !cols.some(function (c) { return c.items && c.items.length; })) {
        if (empty) empty.classList.remove('d-none');
        return;
    }
    if (dual) dual.classList.remove('d-none');

    function renderCol(idx) {
        var col = cols[idx];
        var tbody = document.getElementById('home-dining-tbody-' + idx);
        var pager = document.getElementById('home-dining-pager-' + idx);
        var titleEl = document.getElementById('home-dining-col-title-' + idx);
        var diningUrl = dual ? dual.getAttribute('data-dining-url') : '';

        if (!tbody || !pager || !titleEl) return;

        if (!col || !col.items || !col.items.length) {
            if (idx === 1) {
                titleEl.textContent = 'More on the menu';
                tbody.innerHTML = '';
                var tr = document.createElement('tr');
                var td = document.createElement('td');
                td.colSpan = 2;
                td.className = 'text-muted py-3 px-3 small';
                var a = document.createElement('a');
                a.href = diningUrl || '/dining';
                a.className = 'fw-semibold';
                a.textContent = 'Open the full dining menu';
                td.appendChild(document.createTextNode('See all categories and order online — '));
                td.appendChild(a);
                td.appendChild(document.createTextNode('.'));
                tr.appendChild(td);
                tbody.appendChild(tr);
            } else {
                titleEl.textContent = '';
                tbody.innerHTML = '';
                var tr0 = document.createElement('tr');
                var td0 = document.createElement('td');
                td0.colSpan = 2;
                td0.className = 'text-muted text-center py-4 small';
                td0.textContent = 'No items yet.';
                tr0.appendChild(td0);
                tbody.appendChild(tr0);
            }
            pager.innerHTML = '';
            return;
        }

        titleEl.textContent = col.label || '';

        var items = col.items;
        var totalPages = Math.max(1, Math.ceil(items.length / PER));
        if (pageIndex[idx] >= totalPages) pageIndex[idx] = totalPages - 1;
        var start = pageIndex[idx] * PER;
        var slice = items.slice(start, start + PER);

        tbody.innerHTML = '';
        slice.forEach(function (it) {
            var tr = document.createElement('tr');
            var tdItem = document.createElement('td');
            var strong = document.createElement('div');
            strong.className = 'home-dining-mini-table__title fw-semibold';
            strong.textContent = it.title || '';
            tdItem.appendChild(strong);
            if (it.description) {
                var desc = document.createElement('div');
                desc.className = 'home-dining-mini-table__desc text-muted small mt-1';
                desc.textContent = it.description;
                if (it.descriptionTitle) desc.title = it.descriptionTitle;
                tdItem.appendChild(desc);
            }
            var tdPrice = document.createElement('td');
            tdPrice.className = 'text-end align-top home-dining-mini-table__price';
            var ph = document.createElement('div');
            ph.innerHTML = it.priceHtml || '';
            tdPrice.appendChild(ph);
            tr.appendChild(tdItem);
            tr.appendChild(tdPrice);
            tbody.appendChild(tr);
        });

        pager.innerHTML = '';
        pager.className = 'd-flex flex-wrap align-items-center justify-content-between gap-2 mt-2';
        if (items.length <= PER) return;

        var info = document.createElement('span');
        info.className = 'text-muted small';
        info.textContent = 'Page ' + (pageIndex[idx] + 1) + ' of ' + totalPages;

        var prev = document.createElement('button');
        prev.type = 'button';
        prev.className = 'btn btn-sm btn-outline-secondary';
        prev.textContent = 'Prev';
        prev.disabled = pageIndex[idx] <= 0;
        prev.addEventListener('click', function () {
            if (pageIndex[idx] > 0) {
                pageIndex[idx]--;
                renderCol(idx);
            }
        });

        var next = document.createElement('button');
        next.type = 'button';
        next.className = 'btn btn-sm btn-outline-secondary';
        next.textContent = 'Next';
        next.disabled = pageIndex[idx] >= totalPages - 1;
        next.addEventListener('click', function () {
            if (pageIndex[idx] < totalPages - 1) {
                pageIndex[idx]++;
                renderCol(idx);
            }
        });

        pager.appendChild(prev);
        pager.appendChild(info);
        pager.appendChild(next);
    }

    renderCol(0);
    renderCol(1);
})();
</script>
