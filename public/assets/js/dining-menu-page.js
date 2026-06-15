(function () {
    'use strict';

    function menuCurrency() {
        return window.IsangeDiningOrder
            ? window.IsangeDiningOrder.getMenuCurrency()
            : 'usd';
    }

    function buildCard(it) {
        if (window.IsangeDiningOrder && window.IsangeDiningOrder.buildDishCard) {
            return window.IsangeDiningOrder.buildDishCard(it, menuCurrency());
        }
        return null;
    }

    var elJson = document.getElementById('dining-menu-data');
    if (!elJson) return;

    var columns = [];
    try {
        columns = elJson.textContent ? JSON.parse(elJson.textContent) : [];
    } catch (e) {
        columns = [];
    }

    var root = document.getElementById('dining-menu-items-root');
    var tabsRoot = document.getElementById('dining-menu-tabs');
    var searchEl = document.getElementById('dining-menu-search');
    var emptyEl = document.getElementById('dining-menu-empty');
    var loadedEl = document.getElementById('dining-menu-loaded');
    var bannerEl = document.getElementById('dining-menu-category-banner');
    var activeCategory = -1;
    var searchQuery = '';

    function allItemsFlat() {
        var flat = [];
        columns.forEach(function (col) {
            (col.items || []).forEach(function (it) {
                flat.push(it);
            });
        });
        return flat;
    }

    function itemsForView() {
        var q = searchQuery.trim().toLowerCase();
        var pool = activeCategory === -1
            ? allItemsFlat()
            : ((columns[activeCategory] && columns[activeCategory].items) ? columns[activeCategory].items : []);

        if (!q) return pool;

        return pool.filter(function (it) {
            var title = (it.title || '').toLowerCase();
            var desc = (it.description || '').toLowerCase();
            return title.indexOf(q) >= 0 || desc.indexOf(q) >= 0;
        });
    }

    function updateCategoryBanner(col) {
        if (!bannerEl) return;
        if (activeCategory === -1 || searchQuery.trim() || !col || !col.coverUrl) {
            bannerEl.classList.add('d-none');
            bannerEl.setAttribute('aria-hidden', 'true');
            return;
        }
        var img = bannerEl.querySelector('.dining-menu-category-banner__img');
        var title = bannerEl.querySelector('.dining-menu-category-banner__title');
        if (img) {
            img.src = col.coverUrl;
            img.alt = col.label || 'Menu category';
        }
        if (title) title.textContent = col.label || '';
        bannerEl.classList.remove('d-none');
        bannerEl.setAttribute('aria-hidden', 'false');
    }

    function setActiveTab(idx) {
        activeCategory = idx;
        if (tabsRoot) {
            tabsRoot.querySelectorAll('.dining-menu-tab-btn').forEach(function (btn) {
                var tabIdx = btn.getAttribute('data-category-index');
                var isAll = tabIdx === 'all';
                var isActive = isAll ? idx === -1 : parseInt(tabIdx, 10) === idx;
                btn.classList.toggle('active', isActive);
                btn.setAttribute('aria-selected', isActive ? 'true' : 'false');
            });
        }
        renderItems();
    }

    function renderItems() {
        if (!root) return;

        var col = activeCategory >= 0 ? columns[activeCategory] : null;
        updateCategoryBanner(col);

        var items = itemsForView();
        root.innerHTML = '';
        root.setAttribute('role', 'tabpanel');
        root.id = activeCategory === -1 ? 'dining-menu-panel-all' : 'dining-menu-panel-' + activeCategory;
        root.setAttribute('aria-labelledby', activeCategory === -1 ? 'dining-menu-tab-all' : 'dining-menu-tab-' + activeCategory);

        if (!items.length) {
            var empty = document.createElement('p');
            empty.className = 'text-muted text-center py-4 mb-0 small dining-menu-empty-msg';
            empty.textContent = searchQuery.trim()
                ? 'No dishes match your search.'
                : 'No dishes in this section yet.';
            root.appendChild(empty);
            return;
        }

        items.forEach(function (it) {
            var card = buildCard(it);
            if (card) root.appendChild(card);
        });
    }

    function buildCategoryTabs() {
        if (!tabsRoot) return;
        tabsRoot.innerHTML = '';

        var allBtn = document.createElement('button');
        allBtn.type = 'button';
        allBtn.className = 'nav-link dining-menu-tab-btn active';
        allBtn.setAttribute('role', 'tab');
        allBtn.setAttribute('data-category-index', 'all');
        allBtn.id = 'dining-menu-tab-all';
        allBtn.setAttribute('aria-controls', 'dining-menu-panel-all');
        allBtn.setAttribute('aria-selected', 'true');
        var total = allItemsFlat().length;
        allBtn.textContent = 'All' + (total ? ' (' + total + ')' : '');
        allBtn.addEventListener('click', function () { setActiveTab(-1); });
        tabsRoot.appendChild(allBtn);

        columns.forEach(function (col, i) {
            var btn = document.createElement('button');
            btn.type = 'button';
            btn.className = 'nav-link dining-menu-tab-btn';
            btn.setAttribute('role', 'tab');
            btn.setAttribute('data-category-index', String(i));
            btn.setAttribute('aria-selected', 'false');
            btn.setAttribute('aria-controls', 'dining-menu-panel-' + i);
            btn.id = 'dining-menu-tab-' + i;
            var count = (col.items || []).length;
            btn.textContent = (col.label || 'Menu') + (count ? ' (' + count + ')' : '');
            btn.addEventListener('click', function () { setActiveTab(i); });
            tabsRoot.appendChild(btn);
        });
    }

    function buildLayout() {
        buildCategoryTabs();
        renderItems();
    }

    if (searchEl) {
        searchEl.addEventListener('input', function () {
            searchQuery = searchEl.value || '';
            renderItems();
        });
    }

    window.__diningMenuPage = {
        onCurrencyChange: function () {
            renderItems();
        }
    };

    window.__diningRenderColumns = function () {
        renderItems();
        if (window.IsangeDiningOrder) {
            window.IsangeDiningOrder.renderTodaysMenu();
        }
    };

    function init() {
        var hasItems = columns.some(function (c) { return c.items && c.items.length; });
        if (!columns.length || !hasItems) {
            if (emptyEl) emptyEl.classList.remove('d-none');
            return;
        }
        if (loadedEl) loadedEl.classList.remove('d-none');
        buildLayout();
    }

    function bootMenuPage() {
        if (!window.IsangeDiningOrder) return;
        init();
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', bootMenuPage);
    } else {
        bootMenuPage();
    }
})();
