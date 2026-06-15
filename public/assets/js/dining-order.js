(function () {
    'use strict';

    var CURRENCY_KEY = 'dining_currency';
    var cart = [];
    var menuCurrency = 'usd';
    var cfg = null;
    var modal = null;
    var bound = false;

    function dockEl() { return document.getElementById('dining-order-dock'); }
    function countEl() { return document.getElementById('dining-order-count'); }

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
        if (!cfg || !cfg.trackUrl) return;
        fetch(cfg.trackUrl, {
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

    function save() {
        try { localStorage.setItem('dining_cart', JSON.stringify(cart)); } catch (e) {}
    }

    function load() {
        try {
            var raw = localStorage.getItem('dining_cart');
            if (raw) cart = JSON.parse(raw) || [];
        } catch (e) {
            cart = [];
        }
    }

    function moneyUsd(n) {
        var v = Math.round(n * 100) / 100;
        return '$' + v.toFixed(2);
    }

    function moneyRwf(n) {
        return Math.round(n).toLocaleString('en-US') + ' RWF';
    }

    function lineTotals(l) {
        var unit = parseFloat(String(l.priceUsd || '0').replace(',', '.')) || 0;
        var qty = parseInt(l.qty, 10) || 0;
        var lineUsd = unit * qty;
        var rwfEa = parseInt(String(l.priceRwf || '').replace(/\D/g, ''), 10) || 0;
        var lineRwf = rwfEa ? rwfEa * qty : 0;
        return { unit: unit, qty: qty, lineUsd: lineUsd, lineRwf: lineRwf };
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
            return;
        }

        cart.forEach(function (l) {
            var t = lineTotals(l);
            var tr = document.createElement('tr');
            var tdItem = document.createElement('td');
            var title = document.createElement('div');
            title.className = 'fw-semibold';
            title.textContent = l.title || '';
            tdItem.appendChild(title);
            if (l.prepMinutes) {
                var prepLine = document.createElement('div');
                prepLine.className = 'text-muted small';
                prepLine.textContent = '~' + l.prepMinutes + ' min preparation';
                tdItem.appendChild(prepLine);
            }
            var unitLine = document.createElement('div');
            unitLine.className = 'text-muted small';
            unitLine.textContent = formatUnitPrice(l) + ' each';
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

    function refreshDock() {
        var dock = dockEl();
        var count = countEl();
        if (!dock || !count) return;

        var n = cart.reduce(function (a, l) { return a + (l.qty || 0); }, 0);
        var prepEl = document.getElementById('dining-order-prep-estimate');
        var maxPrep = maxPrepMinutes();

        if (n > 0) {
            dock.classList.remove('d-none');
            document.body.classList.add('dining-order-active');
            count.textContent = n + ' item' + (n === 1 ? '' : 's');
            if (prepEl) {
                prepEl.textContent = maxPrep
                    ? ('Estimated kitchen time: ~' + maxPrep + ' min (longest dish)')
                    : '';
            }
        } else {
            dock.classList.add('d-none');
            document.body.classList.remove('dining-order-active');
            count.textContent = '0 items';
            if (prepEl) prepEl.textContent = '';
        }

        renderOrderSummary();
        save();
    }

    function buildDishRow(it, cur) {
        cur = cur || menuCurrency;
        var article = document.createElement('article');
        article.className = 'dining-menu-item-row';
        if (it.imageUrl) {
            article.classList.add('dining-menu-item-row--has-img');
            var thumb = document.createElement('div');
            thumb.className = 'dining-menu-item-row__thumb';
            var img = document.createElement('img');
            img.src = it.imageUrl;
            img.alt = it.title || '';
            img.loading = 'lazy';
            thumb.appendChild(img);
            article.appendChild(thumb);
        }

        var main = document.createElement('div');
        main.className = 'dining-menu-item-row__main';

        var title = document.createElement('h4');
        title.className = 'dining-menu-item-row__title';
        title.textContent = it.title || '';
        main.appendChild(title);

        if (it.description) {
            var desc = document.createElement('p');
            desc.className = 'dining-menu-item-row__desc';
            desc.textContent = it.description;
            if (it.descriptionTitle) desc.title = it.descriptionTitle;
            main.appendChild(desc);
        }

        if (it.prepMinutes) {
            var prep = document.createElement('p');
            prep.className = 'dining-menu-item-row__prep';
            prep.innerHTML = '<i class="far fa-clock me-1" aria-hidden="true"></i> ~' + it.prepMinutes + ' min prep';
            main.appendChild(prep);
        }

        var aside = document.createElement('div');
        aside.className = 'dining-menu-item-row__aside';

        var price = document.createElement('div');
        price.className = 'dining-menu-item-row__price';
        price.innerHTML = cur === 'rwf' ? (it.priceHtmlRwf || '') : (it.priceHtmlUsd || '');
        aside.appendChild(price);

        var b = document.createElement('button');
        b.type = 'button';
        b.className = 'theme-btn style-three btn-sm dining-dish-add';
        b.setAttribute('data-id', String(it.id));
        b.setAttribute('data-title', it.title || '');
        b.setAttribute('data-price', it.priceUsd || '0');
        b.setAttribute('data-price-rwf', it.priceRwfAttr || '');
        b.setAttribute('data-prep-minutes', it.prepMinutes ? String(it.prepMinutes) : '');
        b.innerHTML = '<i class="fas fa-plus me-1" aria-hidden="true"></i> Add';
        aside.appendChild(b);

        article.appendChild(main);
        article.appendChild(aside);
        return article;
    }

    function renderTodaysMenu() {
        document.querySelectorAll('.dining-todays-menu-root[data-dining-todays-mode="order"]').forEach(function (root) {
            var listEl = root.querySelector('.dining-todays-items');
            var tbody = root.querySelector('.dining-todays-tbody');
            var jsonEl = root.querySelector('.dining-todays-items-json');
            if (!jsonEl) return;
            var items = [];
            try {
                items = JSON.parse(jsonEl.textContent || '[]');
            } catch (e) {
                items = [];
            }

            if (listEl) {
                listEl.innerHTML = '';
                items.forEach(function (it) {
                    listEl.appendChild(buildDishRow(it, menuCurrency));
                });
                return;
            }

            if (!tbody) return;
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
                b.innerHTML = '<i class="fas fa-plus me-1" aria-hidden="true"></i> Add';
                tdAct.appendChild(b);
                tr.appendChild(tdItem);
                tr.appendChild(tdPrice);
                tr.appendChild(tdAct);
                tbody.appendChild(tr);
            });
        });
    }

    function setMenuCurrency(c) {
        menuCurrency = c === 'rwf' ? 'rwf' : 'usd';
        try { localStorage.setItem(CURRENCY_KEY, menuCurrency); } catch (e) {}
        document.querySelectorAll('[data-dining-currency]').forEach(function (btn) {
            btn.classList.toggle('active', btn.getAttribute('data-dining-currency') === menuCurrency);
        });
        renderTodaysMenu();
        refreshDock();
        if (window.__diningMenuPage && typeof window.__diningMenuPage.onCurrencyChange === 'function') {
            window.__diningMenuPage.onCurrencyChange(menuCurrency);
        }
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
        lines.push('*' + (cfg.hotel || 'Restaurant') + ' — Bar & Restaurant order*');
        lines.push('Currency: ' + (menuCurrency === 'rwf' ? 'RWF' : 'USD'));
        lines.push('Payment: Pay at the hotel (not online).');
        if (guestName) lines.push('Guest: ' + guestName);
        if (guestPhone) lines.push('Phone: ' + guestPhone);
        if (guestEmail) lines.push('Email: ' + guestEmail);
        lines.push('');
        lines.push('ORDER LINES');
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
        lines.push('— Sent from the hotel website.');
        return lines.join('\n');
    }

    function postDiningSubmission(channel, plainMessage) {
        if (!cfg || !cfg.guestDiningUrl) return Promise.resolve();
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
        return fetch(cfg.guestDiningUrl, {
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
                var phoneEl = document.getElementById('dining-guest-phone');
                if (phoneEl) phoneEl.focus();
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
                var emailEl = document.getElementById('dining-guest-email');
                if (emailEl) emailEl.focus();
                return false;
            }
        }
        return true;
    }

    function bindEvents() {
        if (bound) return;
        bound = true;

        document.addEventListener('click', function (e) {
            var btn = e.target.closest('.dining-dish-add');
            if (!btn) return;
            var idEl = document.getElementById('dining-add-id');
            var nameEl = document.getElementById('dining-modal-dish-name');
            var qtyEl = document.getElementById('dining-add-qty');
            var notesEl = document.getElementById('dining-add-notes');
            if (!idEl || !nameEl) return;
            idEl.value = btn.getAttribute('data-id');
            nameEl.textContent = btn.getAttribute('data-title');
            if (qtyEl) qtyEl.value = '1';
            if (notesEl) notesEl.value = '';
            if (modal) modal.show();
        });

        document.addEventListener('click', function (e) {
            var curBtn = e.target.closest('[data-dining-currency]');
            if (!curBtn) return;
            setMenuCurrency(curBtn.getAttribute('data-dining-currency'));
        });

        var confirmBtn = document.getElementById('dining-add-confirm');
        if (confirmBtn) {
            confirmBtn.addEventListener('click', function () {
                var id = (document.getElementById('dining-add-id') || {}).value;
                var addBtn = document.querySelector('.dining-dish-add[data-id="' + id + '"]');
                if (!addBtn) return;
                var qty = parseInt((document.getElementById('dining-add-qty') || {}).value, 10) || 1;
                var notes = ((document.getElementById('dining-add-notes') || {}).value || '').trim();
                cart.push({
                    id: id,
                    title: addBtn.getAttribute('data-title'),
                    priceUsd: addBtn.getAttribute('data-price'),
                    priceRwf: addBtn.getAttribute('data-price-rwf') || '',
                    prepMinutes: addBtn.getAttribute('data-prep-minutes') || '',
                    qty: qty,
                    notes: notes
                });
                if (modal) modal.hide();
                refreshDock();
                postTrack('dining_cart_item_added', { qty: qty });
            });
        }

        var clearBtn = document.getElementById('dining-order-clear');
        if (clearBtn) {
            clearBtn.addEventListener('click', function () {
                cart = [];
                refreshDock();
            });
        }

        var waBtn = document.getElementById('dining-order-whatsapp');
        if (waBtn) {
            waBtn.addEventListener('click', function () {
                if (!validateBeforeSubmit('whatsapp')) return;
                var plain = buildMessage();
                postDiningSubmission('whatsapp', plain).finally(function () {
                    window.open('https://wa.me/' + cfg.wa + '?text=' + encodeURIComponent(plain), '_blank');
                });
            });
        }

        var emailBtn = document.getElementById('dining-order-email');
        if (emailBtn) {
            emailBtn.addEventListener('click', function () {
                if (!validateBeforeSubmit('email')) return;
                var plain = buildMessage();
                var subject = encodeURIComponent((cfg.hotel || 'Restaurant') + ' — Restaurant order');
                var body = encodeURIComponent(plain);
                postDiningSubmission('email', plain).finally(function () {
                    window.location.href = 'mailto:' + encodeURIComponent(cfg.email) + '?subject=' + subject + '&body=' + body;
                });
            });
        }
    }

    function boot() {
        if (!window.__diningOrderConfig || !document.getElementById('dining-order-dock')) {
            return;
        }
        cfg = window.__diningOrderConfig;
        var modalEl = document.getElementById('dining-add-modal');
        if (modalEl && typeof bootstrap !== 'undefined' && bootstrap.Modal) {
            modal = new bootstrap.Modal(modalEl);
        }
        bindEvents();
        load();
        menuCurrency = getMenuCurrency();
        setMenuCurrency(menuCurrency);
    }

    window.IsangeDiningOrder = {
        getCart: function () { return cart; },
        getMenuCurrency: function () { return menuCurrency; },
        setMenuCurrency: setMenuCurrency,
        renderTodaysMenu: renderTodaysMenu,
        refreshDock: refreshDock,
        buildDishRow: buildDishRow
    };

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', boot);
    } else {
        boot();
    }
})();
