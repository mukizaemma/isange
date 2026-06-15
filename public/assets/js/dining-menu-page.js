(function () {
    'use strict';

    function menuCurrency() {
        return window.IsangeDiningOrder
            ? window.IsangeDiningOrder.getMenuCurrency()
            : 'usd';
    }

    function buildRow(it) {
        if (window.IsangeDiningOrder && window.IsangeDiningOrder.buildDishRow) {
            return window.IsangeDiningOrder.buildDishRow(it, menuCurrency());
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
    var emptyEl = document.getElementById('dining-menu-empty');
    var loadedEl = document.getElementById('dining-menu-loaded');
    var bannerEl = document.getElementById('dining-menu-category-banner');
    var activeCategory = 0;

    function updateCategoryBanner(col) {
        if (!bannerEl) return;
        var img = bannerEl.querySelector('.dining-menu-category-banner__img');
        var title = bannerEl.querySelector('.dining-menu-category-banner__title');
        if (!col || !col.coverUrl) {
            bannerEl.classList.add('d-none');
            bannerEl.setAttribute('aria-hidden', 'true');
            return;
        }
        if (img) {
            img.src = col.coverUrl;
            img.alt = col.label || 'Menu category';
        }
        if (title) title.textContent = col.label || '';
        bannerEl.classList.remove('d-none');
        bannerEl.setAttribute('aria-hidden', 'false');
    }

    function renderCategory(idx) {
        var col = columns[idx];
        if (!root || !col) return;

        updateCategoryBanner(col);

        var items = col.items || [];
        root.innerHTML = '';
        root.id = 'dining-menu-panel-' + idx;
        root.setAttribute('role', 'tabpanel');
        root.setAttribute('aria-labelledby', 'dining-menu-tab-' + idx);

        if (!items.length) {
            var empty = document.createElement('p');
            empty.className = 'text-muted text-center py-4 mb-0 small';
            empty.textContent = 'No dishes in this section yet.';
            root.appendChild(empty);
            return;
        }

        items.forEach(function (it) {
            var row = buildRow(it);
            if (row) root.appendChild(row);
        });
    }

    function buildCategoryTabs() {
        if (!tabsRoot) return;
        tabsRoot.innerHTML = '';
        columns.forEach(function (col, i) {
            var btn = document.createElement('button');
            btn.type = 'button';
            btn.className = 'nav-link dining-menu-tab-btn' + (i === activeCategory ? ' active' : '');
            btn.setAttribute('role', 'tab');
            btn.setAttribute('aria-selected', i === activeCategory ? 'true' : 'false');
            btn.setAttribute('aria-controls', 'dining-menu-panel-' + i);
            btn.id = 'dining-menu-tab-' + i;
            var count = (col.items || []).length;
            btn.textContent = (col.label || 'Menu') + (count ? ' (' + count + ')' : '');
            btn.addEventListener('click', function () {
                activeCategory = i;
                columns.forEach(function (_, j) {
                    var tab = document.getElementById('dining-menu-tab-' + j);
                    if (tab) {
                        tab.classList.toggle('active', j === i);
                        tab.setAttribute('aria-selected', j === i ? 'true' : 'false');
                    }
                });
                renderCategory(i);
            });
            tabsRoot.appendChild(btn);
        });
    }

    function buildLayout() {
        buildCategoryTabs();
        renderCategory(activeCategory);
    }

    window.__diningMenuPage = {
        onCurrencyChange: function () {
            renderCategory(activeCategory);
        }
    };

    window.__diningRenderColumns = function () {
        renderCategory(activeCategory);
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
