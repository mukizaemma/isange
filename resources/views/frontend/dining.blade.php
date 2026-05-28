@extends('layouts.frontbase')

@section('content')

@include('frontend.includes.page-header', ['pageKey' => 'dining'])

<section class="dining-page py-100 rpy-70 bg-white rel z-1">
    <div class="dining-page__shell">
        <div class="dining-page__inner">
            @if (! empty($setting->dining_intro))
                <div class="dining-intro text-center mb-45 rmb-35 wow fadeInUp">
                    <div class="dining-intro__inner mx-auto">
                        {!! $setting->dining_intro !!}
                    </div>
                </div>
            @endif

            @include('frontend.includes.dining-todays-menu-card', [
                'items' => $todaysMenu ?? [],
                'mode' => 'order',
                'showViewFullLink' => false,
            ])

            <h2 class="h4 mb-3 wow fadeInUp">Full menu</h2>

            <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4 wow fadeInUp">
                <p class="text-muted small mb-0">Add dishes to your order, review the summary, then send via WhatsApp or email.</p>
                <div class="dining-currency-picker d-flex align-items-center gap-2">
                    <span class="small fw-semibold">Prices in</span>
                    <div class="btn-group btn-group-sm" role="group" aria-label="Menu currency">
                        <button type="button" class="btn btn-outline-secondary active" data-dining-currency="usd" id="dining-cur-usd">USD ($)</button>
                        <button type="button" class="btn btn-outline-secondary" data-dining-currency="rwf" id="dining-cur-rwf">RWF</button>
                    </div>
                </div>
            </div>

            <script type="application/json" id="dining-menu-data">@json($diningMenuColumns)</script>
            <div id="dining-menu-columns-app" class="dining-menu-columns-app wow fadeInUp">
                <p class="text-center text-muted py-5 mb-0 d-none" id="dining-menu-empty">Menu coming soon.</p>
                <div id="dining-menu-loaded" class="d-none">
                    <div class="dining-menu-columns-grid" id="dining-menu-columns-root" aria-live="polite"></div>
                </div>
            </div>
        </div>
    </div>
</section>

@php
    $waDigits = preg_replace('/\D+/', '', $setting->phone ?? '');
@endphp
<div id="dining-order-dock" class="dining-order-dock d-none" aria-live="polite">
    <div class="dining-order-dock__inner dining-order-dock__inner--wide py-3 px-3 px-md-4">
        <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3">
            <div>
                <strong class="dining-order-dock__label d-block mb-0">Your order</strong>
                <span id="dining-order-count" class="dining-order-dock__sub small">0 items</span>
                <span id="dining-order-prep-estimate" class="dining-order-dock__sub small d-block text-warning"></span>
            </div>
            <div class="d-flex flex-wrap gap-2 align-items-end">
                <div>
                    <label class="form-label small mb-1" for="dining-global-time">Time needed</label>
                    <input type="time" class="form-control form-control-sm" id="dining-global-time" style="max-width:9rem">
                </div>
                <div>
                    <label class="form-label small mb-1" for="dining-global-party">Party size</label>
                    <input type="number" class="form-control form-control-sm" id="dining-global-party" min="1" value="2" placeholder="Guests" style="max-width:7rem">
                </div>
            </div>
        </div>
        <div class="row g-3 align-items-start">
            <div class="col-lg-8">
                <div class="dining-order-summary-card rounded-3 border overflow-hidden bg-white text-dark shadow-sm">
                    <div class="dining-order-summary-card__head px-3 py-2 border-bottom">
                        <span class="dining-order-summary-card__title fw-semibold">Order summary</span>
                        <span class="text-muted small ms-2">Review before sending</span>
                    </div>
                    <div class="p-3">
                        <div class="table-responsive dining-order-summary-table-wrap">
                            <table class="table table-sm table-striped align-middle mb-0 dining-order-summary-table">
                                <thead class="table-light">
                                    <tr>
                                        <th scope="col">Item</th>
                                        <th scope="col" class="text-end text-nowrap" style="width:4rem;">Qty</th>
                                        <th scope="col" class="text-end text-nowrap" style="width:7rem;">Total</th>
                                    </tr>
                                </thead>
                                <tbody id="dining-order-table-body"></tbody>
                                <tfoot class="table-group-divider" id="dining-order-table-foot"></tfoot>
                            </table>
                        </div>
                        <h3 class="h6 fw-semibold mt-4 mb-2">Your details</h3>
                        <div class="row g-2">
                            <div class="col-md-6">
                                <label class="form-label small mb-1" for="dining-guest-name">Name</label>
                                <input type="text" class="form-control form-control-sm" id="dining-guest-name" maxlength="255" autocomplete="name">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small mb-1" for="dining-guest-phone">Phone / WhatsApp</label>
                                <input type="tel" class="form-control form-control-sm" id="dining-guest-phone" maxlength="64" autocomplete="tel">
                            </div>
                            <div class="col-12">
                                <label class="form-label small mb-1" for="dining-guest-email">Email</label>
                                <input type="email" class="form-control form-control-sm" id="dining-guest-email" maxlength="255" autocomplete="email">
                            </div>
                        </div>
                        <label class="form-label small fw-semibold mt-3 mb-1" for="dining-order-additional">Special requests (optional)</label>
                        <textarea class="form-control form-control-sm dining-order-summary-card__textarea" id="dining-order-additional" rows="2" placeholder="Allergies, room number, delivery preference, occasion…"></textarea>
                    </div>
                </div>
            </div>
            <div class="col-lg-4">
                <p class="dining-order-dock__sub small mb-3 mb-lg-2">Choose how to send your order to the hotel.</p>
                <div class="d-flex flex-column gap-2">
                    <button type="button" class="theme-btn btn-sm" id="dining-order-whatsapp"><i class="fab fa-whatsapp me-1"></i> Send via WhatsApp</button>
                    <button type="button" class="theme-btn style-three btn-sm" id="dining-order-email"><i class="far fa-envelope me-1"></i> Send via Email</button>
                    <button type="button" class="btn btn-outline-light btn-sm" id="dining-order-clear">Clear order</button>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="dining-add-modal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content dining-modal">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title" id="dining-modal-dish-name">Add to order</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body pt-2">
                <input type="hidden" id="dining-add-id">
                <div class="mb-3">
                    <label class="form-label">Quantity</label>
                    <input type="number" class="form-control" id="dining-add-qty" min="1" value="1">
                </div>
                <div class="mb-3">
                    <label class="form-label">Special request (optional)</label>
                    <textarea class="form-control" id="dining-add-notes" rows="2" placeholder="No onions, extra sauce, allergies…"></textarea>
                </div>
                <button type="button" class="theme-btn w-100 mt-2" id="dining-add-confirm">Add to tray</button>
            </div>
        </div>
    </div>
</div>

<script>
(function () {
    var PER = 10;
    var cfg = {
        wa: @json($waDigits),
        email: @json(trim($setting->email ?? '')),
        hotel: @json($setting->company ?? 'Isange Paradise Eco Resort'),
        displayPhone: @json($setting->phone ?? ''),
        displayEmail: @json(trim($setting->email ?? ''))
    };
    var CURRENCY_KEY = 'dining_currency';
    var cart = [];
    var menuCurrency = 'usd';
    var dock = document.getElementById('dining-order-dock');
    var countEl = document.getElementById('dining-order-count');
    var modalEl = document.getElementById('dining-add-modal');
    var modal = null;
    if (modalEl && typeof bootstrap !== 'undefined' && bootstrap.Modal) {
        modal = new bootstrap.Modal(modalEl);
    }

    var trackUrl = @json(route('track.analytics'));
    var guestDiningUrl = @json(route('guest.dining.store'));

    function maSessionId() {
        try {
            var k = 'ma_sid';
            var s = sessionStorage.getItem(k);
            if (!s) {
                s = 's_' + Math.random().toString(36).slice(2) + '_' + Date.now();
                sessionStorage.setItem(k, s);
            }
            return s;
        } catch (err) {
            return null;
        }
    }

    function csrfToken() {
        var m = document.querySelector('meta[name="csrf-token"]');
        return m ? m.getAttribute('content') : '';
    }

    function postTrack(eventKey, properties) {
        fetch(trackUrl, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrfToken()
            },
            credentials: 'same-origin',
            body: JSON.stringify({
                event_key: eventKey,
                properties: properties || {},
                session_id: maSessionId()
            })
        }).catch(function () {});
    }

    function getMenuCurrency() {
        try {
            var c = localStorage.getItem(CURRENCY_KEY);
            return c === 'rwf' ? 'rwf' : 'usd';
        } catch (e) {
            return 'usd';
        }
    }

    function setMenuCurrency(c) {
        menuCurrency = c === 'rwf' ? 'rwf' : 'usd';
        try { localStorage.setItem(CURRENCY_KEY, menuCurrency); } catch (e) {}
        document.querySelectorAll('[data-dining-currency]').forEach(function (btn) {
            btn.classList.toggle('active', btn.getAttribute('data-dining-currency') === menuCurrency);
        });
        columns.forEach(function (_, i) { renderCol(i); });
        renderTodaysMenu();
        refreshDock();
    }

    window.__diningRenderColumns = function () {
        columns.forEach(function (_, i) { renderCol(i); });
        renderTodaysMenu();
    };

    function postDiningSubmission(channel, plainMessage) {
        var items = cart.map(function (l) {
            return {
                title: l.title,
                qty: l.qty,
                priceUsd: l.priceUsd,
                notes: l.notes || '',
                priceRwf: l.priceRwf || '',
                prepMinutes: l.prepMinutes || null
            };
        });
        var gt = cartGrandTotals();
        var extraEl = document.getElementById('dining-order-additional');
        return fetch(guestDiningUrl, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrfToken()
            },
            credentials: 'same-origin',
            body: JSON.stringify({
                channel: channel,
                message_body: plainMessage,
                items: items,
                currency: menuCurrency,
                guest_name: (document.getElementById('dining-guest-name') || {}).value || '',
                guest_phone: (document.getElementById('dining-guest-phone') || {}).value || '',
                guest_email: (document.getElementById('dining-guest-email') || {}).value || '',
                special_requests: extraEl ? extraEl.value.trim() : '',
                grand_total_usd: gt.sumUsd.toFixed(2),
                grand_total_rwf: gt.sumRwf > 0 ? String(gt.sumRwf) : '',
                session_id: maSessionId()
            })
        }).catch(function () {});
    }

    function renderTodaysMenu() {
        document.querySelectorAll('.dining-todays-menu-root[data-dining-todays-mode="order"]').forEach(function (root) {
            var tbody = root.querySelector('.dining-todays-tbody');
            var jsonEl = root.querySelector('.dining-todays-items-json');
            if (!tbody || !jsonEl) return;
            var items = [];
            try {
                items = JSON.parse(jsonEl.textContent || '[]');
            } catch (e) {
                items = [];
            }
            tbody.innerHTML = '';
            items.forEach(function (it) {
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
                    tdItem.appendChild(desc);
                }
                var tdPrice = document.createElement('td');
                tdPrice.className = 'text-end align-top home-dining-mini-table__price';
                var ph = document.createElement('div');
                ph.innerHTML = menuCurrency === 'rwf' ? (it.priceHtmlRwf || '') : (it.priceHtmlUsd || '');
                tdPrice.appendChild(ph);
                var tdAct = document.createElement('td');
                tdAct.className = 'text-end align-top';
                var b = document.createElement('button');
                b.type = 'button';
                b.className = 'theme-btn style-three btn-sm dining-dish-add';
                b.setAttribute('data-id', String(it.id));
                b.setAttribute('data-title', it.title || '');
                b.setAttribute('data-price', it.priceUsd || '0');
                b.setAttribute('data-price-rwf', it.priceRwfAttr || '');
                b.setAttribute('data-prep-minutes', it.prepMinutes ? String(it.prepMinutes) : '');
                b.innerHTML = '<i class="fas fa-plus me-1"></i> Add';
                tdAct.appendChild(b);
                tr.appendChild(tdItem);
                tr.appendChild(tdPrice);
                tr.appendChild(tdAct);
                tbody.appendChild(tr);
            });
        });
    }

    var elJson = document.getElementById('dining-menu-data');
    var columns = [];
    try {
        columns = elJson && elJson.textContent ? JSON.parse(elJson.textContent) : [];
    } catch (e) {
        columns = [];
    }
    var root = document.getElementById('dining-menu-columns-root');
    var emptyEl = document.getElementById('dining-menu-empty');
    var loadedEl = document.getElementById('dining-menu-loaded');
    var pageIndex = [];

    function buildLayout() {
        if (!root) return;
        root.innerHTML = '';
        pageIndex = columns.map(function () { return 0; });
        columns.forEach(function (col, i) {
            var wrap = document.createElement('div');
            wrap.className = 'home-dining-tcol dining-menu-tcol';

            var h4 = document.createElement('h4');
            h4.className = 'home-dining-tcol__title mb-2';
            h4.id = 'dining-col-title-' + i;

            var trOuter = document.createElement('div');
            trOuter.className = 'table-responsive home-dining-tcol__wrap border rounded-3 overflow-hidden bg-white';

            var table = document.createElement('table');
            table.className = 'table table-sm table-striped home-dining-mini-table dining-menu-page-table align-middle mb-0';

            var thead = document.createElement('thead');
            thead.className = 'table-light';
            var trh = document.createElement('tr');
            var th1 = document.createElement('th');
            th1.scope = 'col';
            th1.textContent = 'Item';
            var th2 = document.createElement('th');
            th2.scope = 'col';
            th2.className = 'text-end text-nowrap';
            th2.style.width = '6.5rem';
            th2.textContent = 'Price';
            var th3 = document.createElement('th');
            th3.scope = 'col';
            th3.className = 'text-end text-nowrap';
            th3.style.width = '5rem';
            var sr = document.createElement('span');
            sr.className = 'visually-hidden';
            sr.textContent = 'Add';
            th3.appendChild(sr);
            trh.appendChild(th1);
            trh.appendChild(th2);
            trh.appendChild(th3);
            thead.appendChild(trh);

            var tbody = document.createElement('tbody');
            tbody.id = 'dining-tbody-' + i;

            table.appendChild(thead);
            table.appendChild(tbody);
            trOuter.appendChild(table);

            var pager = document.createElement('div');
            pager.className = 'd-flex flex-wrap align-items-center justify-content-between gap-2 mt-2';
            pager.id = 'dining-pager-' + i;

            wrap.appendChild(h4);
            wrap.appendChild(trOuter);
            wrap.appendChild(pager);
            root.appendChild(wrap);
        });
    }

    function renderCol(idx) {
        var col = columns[idx];
        var tbody = document.getElementById('dining-tbody-' + idx);
        var pager = document.getElementById('dining-pager-' + idx);
        var titleEl = document.getElementById('dining-col-title-' + idx);
        if (!tbody || !pager || !titleEl || !col) return;

        titleEl.textContent = col.label || '';
        var items = col.items || [];
        var totalPages = Math.max(1, Math.ceil(items.length / PER));
        if (pageIndex[idx] >= totalPages) pageIndex[idx] = totalPages - 1;
        var start = pageIndex[idx] * PER;
        var slice = items.slice(start, start + PER);

        tbody.innerHTML = '';
        if (!slice.length) {
            var tr0 = document.createElement('tr');
            var td0 = document.createElement('td');
            td0.colSpan = 3;
            td0.className = 'text-muted text-center py-4 small';
            td0.textContent = 'No dishes in this section yet.';
            tr0.appendChild(td0);
            tbody.appendChild(tr0);
        } else {
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
                if (it.prepMinutes) {
                    var prep = document.createElement('div');
                    prep.className = 'home-dining-mini-table__prep text-muted small mt-1';
                    prep.innerHTML = '<i class="far fa-clock me-1"></i> ~' + it.prepMinutes + ' min prep';
                    tdItem.appendChild(prep);
                }
                var tdPrice = document.createElement('td');
                tdPrice.className = 'text-end align-top home-dining-mini-table__price';
                var ph = document.createElement('div');
                ph.innerHTML = menuCurrency === 'rwf' ? (it.priceHtmlRwf || '') : (it.priceHtmlUsd || '');
                tdPrice.appendChild(ph);

                var tdAct = document.createElement('td');
                tdAct.className = 'text-end align-top';
                var b = document.createElement('button');
                b.type = 'button';
                b.className = 'theme-btn style-three btn-sm dining-dish-add';
                b.setAttribute('data-id', String(it.id));
                b.setAttribute('data-title', it.title || '');
                b.setAttribute('data-price', it.priceUsd || '0');
                b.setAttribute('data-price-rwf', it.priceRwfAttr || '');
                b.setAttribute('data-prep-minutes', it.prepMinutes ? String(it.prepMinutes) : '');
                b.innerHTML = '<i class="fas fa-plus me-1"></i> Add';
                tdAct.appendChild(b);

                tr.appendChild(tdItem);
                tr.appendChild(tdPrice);
                tr.appendChild(tdAct);
                tbody.appendChild(tr);
            });
        }

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

    var hasItems = columns.some(function (c) { return c.items && c.items.length; });
    if (!columns.length || !hasItems) {
        if (emptyEl) emptyEl.classList.remove('d-none');
    } else {
        if (loadedEl) loadedEl.classList.remove('d-none');
        buildLayout();
        columns.forEach(function (_, i) {
            renderCol(i);
        });
    }

    function save() {
        try { localStorage.setItem('dining_cart', JSON.stringify(cart)); } catch (e) {}
    }
    function load() {
        try {
            var raw = localStorage.getItem('dining_cart');
            if (raw) cart = JSON.parse(raw) || [];
        } catch (e) { cart = []; }
    }
    function moneyUsd(n) {
        var v = Math.round(n * 100) / 100;
        return '$' + v.toFixed(2);
    }

    function moneyRwf(n) {
        return Math.round(n).toLocaleString('en-US') + ' RWF';
    }

    function formatLineTotal(t) {
        if (menuCurrency === 'rwf' && t.lineRwf > 0) {
            return moneyRwf(t.lineRwf);
        }
        return moneyUsd(t.lineUsd);
    }

    function formatUnitPrice(l) {
        var unitRwf = parseInt(String(l.priceRwf || '').replace(/\D/g, ''), 10) || 0;
        if (menuCurrency === 'rwf' && unitRwf > 0) {
            return moneyRwf(unitRwf);
        }
        return moneyUsd(parseFloat(String(l.priceUsd || '0').replace(',', '.')) || 0);
    }

    function maxPrepMinutes() {
        var max = 0;
        cart.forEach(function (l) {
            var p = parseInt(l.prepMinutes, 10) || 0;
            if (p > max) max = p;
        });
        return max;
    }

    function lineTotals(l) {
        var unit = parseFloat(String(l.priceUsd || '0').replace(',', '.')) || 0;
        var qty = parseInt(l.qty, 10) || 0;
        var lineUsd = unit * qty;
        var rwfEa = parseInt(String(l.priceRwf || '').replace(/\D/g, ''), 10) || 0;
        var lineRwf = rwfEa ? rwfEa * qty : 0;
        return { unit: unit, qty: qty, lineUsd: lineUsd, lineRwf: lineRwf };
    }

    function cartGrandTotals() {
        var sumUsd = 0;
        var sumRwf = 0;
        cart.forEach(function (l) {
            var t = lineTotals(l);
            sumUsd += t.lineUsd;
            sumRwf += t.lineRwf;
        });
        return { sumUsd: sumUsd, sumRwf: sumRwf };
    }

    function renderOrderSummary() {
        var tbody = document.getElementById('dining-order-table-body');
        var tfoot = document.getElementById('dining-order-table-foot');
        if (!tbody || !tfoot) return;

        tbody.innerHTML = '';
        if (!cart.length) {
            var trE = document.createElement('tr');
            var tdE = document.createElement('td');
            tdE.colSpan = 3;
            tdE.className = 'text-muted text-center py-3 small';
            tdE.textContent = 'No items in your order yet.';
            trE.appendChild(tdE);
            tbody.appendChild(trE);
            tfoot.innerHTML = '';
        } else {
            cart.forEach(function (l) {
                var t = lineTotals(l);
                var tr = document.createElement('tr');
                var tdItem = document.createElement('td');
                var title = document.createElement('div');
                title.className = 'fw-semibold';
                title.textContent = l.title || '';
                tdItem.appendChild(title);
                var unitLine = document.createElement('div');
                unitLine.className = 'text-muted small';
                unitLine.textContent = formatUnitPrice(l) + ' each';
                if (l.prepMinutes) {
                    var prepLine = document.createElement('div');
                    prepLine.className = 'text-muted small';
                    prepLine.textContent = '~' + l.prepMinutes + ' min preparation';
                    tdItem.appendChild(prepLine);
                }
                tdItem.appendChild(unitLine);
                if (l.notes) {
                    var note = document.createElement('div');
                    note.className = 'text-muted small fst-italic mt-1';
                    note.textContent = 'Note: ' + l.notes;
                    tdItem.appendChild(note);
                }
                var tdQty = document.createElement('td');
                tdQty.className = 'text-end';
                tdQty.textContent = String(t.qty);
                var tdTot = document.createElement('td');
                tdTot.className = 'text-end fw-semibold text-nowrap';
                tdTot.textContent = formatLineTotal(t);
                tr.appendChild(tdItem);
                tr.appendChild(tdQty);
                tr.appendChild(tdTot);
                tbody.appendChild(tr);
            });

            var gt = cartGrandTotals();
            tfoot.innerHTML = '';
            var trF = document.createElement('tr');
            var tdL = document.createElement('th');
            tdL.scope = 'row';
            tdL.colSpan = 2;
            tdL.className = 'text-end border-0';
            tdL.textContent = 'Grand total';
            var tdG = document.createElement('td');
            tdG.className = 'text-end border-0 text-nowrap';
            var strong = document.createElement('strong');
            strong.className = 'text-body';
            strong.textContent = menuCurrency === 'rwf' && gt.sumRwf > 0 ? moneyRwf(gt.sumRwf) : moneyUsd(gt.sumUsd);
            tdG.appendChild(strong);
            trF.appendChild(tdL);
            trF.appendChild(tdG);
            tfoot.appendChild(trF);
        }

    }

    function refreshDock() {
        var n = cart.reduce(function (a, l) { return a + (l.qty || 0); }, 0);
        var prepEl = document.getElementById('dining-order-prep-estimate');
        var maxPrep = maxPrepMinutes();
        if (n > 0) {
            dock.classList.remove('d-none');
            countEl.textContent = n + ' item' + (n === 1 ? '' : 's');
            if (prepEl) {
                prepEl.textContent = maxPrep
                    ? ('Estimated kitchen time: ~' + maxPrep + ' min (longest dish)')
                    : '';
            }
        } else {
            dock.classList.add('d-none');
            countEl.textContent = '0 items';
            if (prepEl) prepEl.textContent = '';
        }
        renderOrderSummary();
        save();
    }

    function buildMessage() {
        var time = document.getElementById('dining-global-time') ? document.getElementById('dining-global-time').value : '';
        var party = document.getElementById('dining-global-party') ? document.getElementById('dining-global-party').value : '';
        var extraEl = document.getElementById('dining-order-additional');
        var extra = extraEl ? extraEl.value.trim() : '';
        var guestName = (document.getElementById('dining-guest-name') || {}).value || '';
        var guestPhone = (document.getElementById('dining-guest-phone') || {}).value || '';
        var guestEmail = (document.getElementById('dining-guest-email') || {}).value || '';
        var gt = cartGrandTotals();
        var sep = '----------------------------------------';
        var lines = [];
        lines.push('*' + cfg.hotel + ' — Bar & Restaurant order*');
        lines.push('Currency: ' + (menuCurrency === 'rwf' ? 'RWF' : 'USD'));
        if (guestName) lines.push('Guest: ' + guestName);
        if (guestPhone) lines.push('Phone: ' + guestPhone);
        if (guestEmail) lines.push('Email: ' + guestEmail);
        lines.push('');
        lines.push('ORDER LINES');
        lines.push(sep);
        lines.push('Item | Qty | Unit (USD) | Line total (USD)');
        lines.push(sep);
        cart.forEach(function (l, i) {
            var t = lineTotals(l);
            var row = (i + 1) + '. ' + (l.title || '') + ' | qty ' + t.qty + ' | ' + formatLineTotal(t);
            lines.push(row);
            if (l.prepMinutes) lines.push('   Prep: ~' + l.prepMinutes + ' min');
            if (l.notes) lines.push('   Special request: ' + l.notes);
        });
        lines.push(sep);
        lines.push('GRAND TOTAL: ' + (menuCurrency === 'rwf' && gt.sumRwf > 0 ? moneyRwf(gt.sumRwf) : moneyUsd(gt.sumUsd)));
        var maxPrep = maxPrepMinutes();
        if (maxPrep) lines.push('Estimated preparation (longest dish): ~' + maxPrep + ' min');
        lines.push('');
        if (extra) {
            lines.push('ADDITIONAL REQUESTS');
            lines.push(sep);
            lines.push(extra);
            lines.push('');
        }
        lines.push('SERVICE DETAILS');
        lines.push(sep);
        lines.push('Time required: ' + (time || '—'));
        lines.push('Party size: ' + (party || '—'));
        lines.push('');
        lines.push('— Sent from the hotel website dining page.');
        return lines.join('\n');
    }

    document.addEventListener('click', function (e) {
        var btn = e.target.closest('.dining-dish-add');
        if (!btn) return;
        document.getElementById('dining-add-id').value = btn.getAttribute('data-id');
        document.getElementById('dining-modal-dish-name').textContent = btn.getAttribute('data-title');
        document.getElementById('dining-add-qty').value = '1';
        document.getElementById('dining-add-notes').value = '';
        if (modal) modal.show();
    });

    document.getElementById('dining-add-confirm').addEventListener('click', function () {
        var id = document.getElementById('dining-add-id').value;
        var btn = document.querySelector('.dining-dish-add[data-id="' + id + '"]');
        if (!btn) return;
        var title = btn.getAttribute('data-title');
        var priceUsd = btn.getAttribute('data-price');
        var priceRwf = btn.getAttribute('data-price-rwf') || '';
        var prepMinutes = btn.getAttribute('data-prep-minutes') || '';
        var qty = parseInt(document.getElementById('dining-add-qty').value, 10) || 1;
        var notes = document.getElementById('dining-add-notes').value.trim();
        cart.push({ id: id, title: title, priceUsd: priceUsd, priceRwf: priceRwf, prepMinutes: prepMinutes, qty: qty, notes: notes });
        if (modal) modal.hide();
        refreshDock();
        postTrack('dining_cart_item_added', { qty: qty });
    });
    document.getElementById('dining-order-clear').addEventListener('click', function () {
        cart = [];
        refreshDock();
    });
    document.querySelectorAll('[data-dining-currency]').forEach(function (btn) {
        btn.addEventListener('click', function () {
            setMenuCurrency(btn.getAttribute('data-dining-currency'));
        });
    });

    function validateBeforeSubmit(channel) {
        if (!cart.length) {
            alert('Your order is empty. Add dishes from the menu first.');
            return false;
        }
        var phone = (document.getElementById('dining-guest-phone') || {}).value || '';
        var email = (document.getElementById('dining-guest-email') || {}).value || '';
        if (channel === 'whatsapp') {
            if (!cfg.wa || cfg.wa.length < 8) {
                alert('WhatsApp ordering is unavailable. Please use email or call the hotel.');
                return false;
            }
            if (phone.replace(/\D/g, '').length < 8) {
                alert('Enter your WhatsApp phone number.');
                document.getElementById('dining-guest-phone') && document.getElementById('dining-guest-phone').focus();
                return false;
            }
        }
        if (channel === 'email') {
            if (!cfg.email) {
                alert('Email ordering is unavailable. Please use WhatsApp or call the hotel.');
                return false;
            }
            if (!email || email.indexOf('@') < 1) {
                alert('Enter a valid email address.');
                document.getElementById('dining-guest-email') && document.getElementById('dining-guest-email').focus();
                return false;
            }
        }
        return true;
    }

    document.getElementById('dining-order-whatsapp').addEventListener('click', function () {
        if (!validateBeforeSubmit('whatsapp')) return;
        var plain = buildMessage();
        postDiningSubmission('whatsapp', plain).finally(function () {
            window.open('https://wa.me/' + cfg.wa + '?text=' + encodeURIComponent(plain), '_blank');
        });
    });

    document.getElementById('dining-order-email').addEventListener('click', function () {
        if (!validateBeforeSubmit('email')) return;
        var plain = buildMessage();
        var subject = encodeURIComponent(cfg.hotel + ' — Restaurant order');
        var body = encodeURIComponent(plain);
        postDiningSubmission('email', plain).finally(function () {
            window.location.href = 'mailto:' + encodeURIComponent(cfg.email) + '?subject=' + subject + '&body=' + body;
        });
    });
    menuCurrency = getMenuCurrency();
    setMenuCurrency(menuCurrency);
    load();
    renderTodaysMenu();
    refreshDock();
})();
</script>
@endsection
