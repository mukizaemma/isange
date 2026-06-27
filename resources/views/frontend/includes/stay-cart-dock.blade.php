<div id="stay-cart-dock" class="stay-cart-dock d-none" aria-live="polite" data-checkout-url="{{ route('booking.checkout') }}">
    <div class="stay-cart-dock__wrap">
        <div class="stay-cart-dock__pill" role="region" aria-label="Booking cart">
            <button type="button" class="stay-cart-dock__toggle" id="stay-cart-toggle" aria-expanded="false" aria-controls="stayCartSummaryModal" title="View cart summary">
                <i class="fas fa-chevron-up" aria-hidden="true"></i>
            </button>

            <button type="button" class="stay-cart-dock__amount" id="stay-cart-open-summary" aria-label="Open stay summary">
                <span class="stay-cart-dock__total" id="stay-cart-total">$0.00</span>
                <span class="stay-cart-dock__grand">Grand Total</span>
            </button>

            <div class="stay-cart-dock__icons" aria-hidden="false">
                <span class="stay-cart-dock__icon-pill" id="stay-cart-rooms-icon" title="Rooms selected">
                    <i class="fas fa-bed" aria-hidden="true"></i>
                    <span class="stay-cart-dock__badge" id="stay-cart-room-count">0</span>
                </span>
                <span class="stay-cart-dock__icon-pill" id="stay-cart-exp-icon" title="Activities selected">
                    <i class="fas fa-hiking" aria-hidden="true"></i>
                    <span class="stay-cart-dock__badge" id="stay-cart-exp-count">0</span>
                </span>
            </div>

            <a href="{{ route('booking.checkout') }}" class="stay-cart-dock__continue" id="stay-cart-continue">
                Continue <i class="fas fa-chevron-right ms-1" aria-hidden="true"></i>
            </a>
        </div>
    </div>
</div>

{{-- Stay summary modal --}}
<div class="modal fade stay-cart-modal" id="stayCartSummaryModal" tabindex="-1" aria-labelledby="stayCartSummaryModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable stay-cart-modal__dialog">
        <div class="modal-content stay-cart-modal__content">
            <div class="modal-header stay-cart-modal__header border-0 pb-0">
                <h5 class="modal-title stay-cart-modal__title" id="stayCartSummaryModalLabel">Your Stay Summary</h5>
                <div class="d-flex align-items-center gap-3">
                    <button type="button" class="btn btn-link stay-cart-modal__clear p-0" id="stay-cart-clear-all">Clear All</button>
                    <button type="button" class="btn-close stay-cart-modal__close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
            </div>
            <div class="modal-body stay-cart-modal__body pt-2">
                <div id="stay-cart-meta" class="stay-cart-modal__meta d-none"></div>
                <div id="stay-cart-summary-lines" class="stay-cart-modal__lines"></div>
                <p class="stay-cart-modal__empty text-muted small mb-0 d-none" id="stay-cart-summary-empty">Your cart is empty.</p>
            </div>
            <div class="modal-footer stay-cart-modal__footer border-0 pt-0">
                <div class="stay-cart-modal__footer-total w-100 d-flex justify-content-between align-items-center">
                    <span class="text-muted small">Grand Total</span>
                    <strong class="stay-cart-modal__footer-price" id="stay-cart-modal-total">$0.00</strong>
                </div>
                <a href="{{ route('booking.checkout') }}" class="stay-cart-dock__continue stay-cart-dock__continue--modal w-100 mt-3" id="stay-cart-continue-modal">
                    Continue <i class="fas fa-chevron-right ms-1" aria-hidden="true"></i>
                </a>
            </div>
        </div>
    </div>
</div>

<script>
(function () {
    var dockBooted = false;

    function purgeOrphanModalLayers() {
        if (document.querySelectorAll('.modal.show').length > 0) {
            return;
        }
        document.querySelectorAll('.modal-backdrop').forEach(function (el) {
            el.remove();
        });
        document.body.classList.remove('modal-open');
        document.body.style.removeProperty('overflow');
        document.body.style.removeProperty('padding-right');
    }

    function initStayCartDock() {
        if (!window.IsangeStayCart) {
            return false;
        }
        if (dockBooted) {
            if (typeof window.__stayCartDockRender === 'function') {
                window.__stayCartDockRender();
            }
            return true;
        }

        var dock = document.getElementById('stay-cart-dock');
        var toggle = document.getElementById('stay-cart-toggle');
        var openSummary = document.getElementById('stay-cart-open-summary');
        var summaryModalEl = document.getElementById('stayCartSummaryModal');
        var linesEl = document.getElementById('stay-cart-summary-lines');
        var metaEl = document.getElementById('stay-cart-meta');
        var emptyEl = document.getElementById('stay-cart-summary-empty');
        var totalEl = document.getElementById('stay-cart-total');
        var modalTotalEl = document.getElementById('stay-cart-modal-total');
        var roomCountEl = document.getElementById('stay-cart-room-count');
        var expCountEl = document.getElementById('stay-cart-exp-count');
        var roomsIcon = document.getElementById('stay-cart-rooms-icon');
        var expIcon = document.getElementById('stay-cart-exp-icon');
        var clearAllBtn = document.getElementById('stay-cart-clear-all');

        if (!dock) {
            return false;
        }

        dockBooted = true;

        var summaryModal = summaryModalEl && window.bootstrap
            ? bootstrap.Modal.getOrCreateInstance(summaryModalEl)
            : null;

        function formatMoney(n) {
            return '$' + (Number(n) || 0).toFixed(2);
        }

        function formatDateShort(iso) {
            if (!iso) return '';
            try {
                return new Date(iso + 'T12:00:00').toLocaleDateString(undefined, { weekday: 'short', day: 'numeric', month: 'short' });
            } catch (e) {
                return iso;
            }
        }

        function escapeHtml(str) {
            var d = document.createElement('div');
            d.textContent = str || '';
            return d.innerHTML;
        }

        function openModal() {
            if (summaryModal) {
                summaryModal.show();
            }
        }

        function bindRemoveHandlers(root) {
            if (!root) return;
            root.querySelectorAll('[data-remove-room]').forEach(function (btn) {
                btn.addEventListener('click', function () {
                    IsangeStayCart.removeRoom(parseInt(btn.getAttribute('data-remove-room'), 10));
                });
            });
            root.querySelectorAll('[data-remove-exp]').forEach(function (btn) {
                btn.addEventListener('click', function () {
                    IsangeStayCart.removeExperience(btn.getAttribute('data-remove-exp'));
                });
            });
        }

        function render() {
            var cart = IsangeStayCart.get();
            var roomCount = cart.rooms.length;
            var expCount = cart.experiences.length;
            var count = roomCount + expCount;
            var total = IsangeStayCart.estimateTotalUsd();

            if (count === 0) {
                dock.classList.add('d-none');
                if (summaryModal) {
                    summaryModal.hide();
                }
                return;
            }

            dock.classList.remove('d-none');
            if (totalEl) totalEl.textContent = formatMoney(total);
            if (modalTotalEl) modalTotalEl.textContent = formatMoney(total);

            if (roomCountEl) roomCountEl.textContent = String(roomCount);
            if (expCountEl) expCountEl.textContent = String(expCount);
            if (roomsIcon) roomsIcon.classList.toggle('is-empty', roomCount === 0);
            if (expIcon) expIcon.classList.toggle('is-empty', expCount === 0);

            if (metaEl) {
                if (roomCount > 0 && cart.rooms[0].check_in && cart.rooms[0].check_out) {
                    var nights = cart.rooms[0].nights || 1;
                    metaEl.classList.remove('d-none');
                    metaEl.innerHTML =
                        '<p class="stay-cart-modal__dates mb-1">' +
                        '<strong>' + escapeHtml(formatDateShort(cart.rooms[0].check_in)) + '</strong>' +
                        ' <span class="text-muted">→</span> ' +
                        '<strong>' + escapeHtml(formatDateShort(cart.rooms[0].check_out)) + '</strong>' +
                        ' <span class="stay-cart-modal__nights-badge">' + nights + ' Night' + (nights !== 1 ? 's' : '') + '</span>' +
                        '</p>' +
                        '<p class="stay-cart-modal__counts small text-muted mb-3">' +
                        roomCount + ' Room' + (roomCount !== 1 ? 's' : '') +
                        ' · ' + expCount + ' Activit' + (expCount === 1 ? 'y' : 'ies') +
                        '</p>';
                } else {
                    metaEl.classList.remove('d-none');
                    metaEl.innerHTML =
                        '<p class="stay-cart-modal__counts small text-muted mb-3">' +
                        (roomCount ? roomCount + ' Room' + (roomCount !== 1 ? 's' : '') : '') +
                        (roomCount && expCount ? ' · ' : '') +
                        (expCount ? expCount + ' Activit' + (expCount === 1 ? 'y' : 'ies') : '') +
                        '</p>';
                }
            }

            if (linesEl) {
                linesEl.innerHTML = '';
                cart.rooms.forEach(function (room, idx) {
                    var nights = room.nights || 1;
                    var lineTotal = (parseFloat(String(room.price || '').replace(/[^0-9.]/g, '')) || 0) * nights;
                    var card = document.createElement('article');
                    card.className = 'stay-cart-modal__card';
                    card.innerHTML =
                        '<div class="stay-cart-modal__card-top">' +
                        '<span class="stay-cart-modal__card-type"><i class="fas fa-bed me-1"></i> Room</span>' +
                        '<button type="button" class="stay-cart-modal__remove" data-remove-room="' + idx + '" aria-label="Remove room"><i class="fas fa-trash-alt"></i></button>' +
                        '</div>' +
                        '<h6 class="stay-cart-modal__card-title">' + escapeHtml(room.name || 'Room') + '</h6>' +
                        (room.check_in && room.check_out
                            ? '<p class="stay-cart-modal__card-meta small mb-1">' +
                              escapeHtml(room.check_in) + ' → ' + escapeHtml(room.check_out) +
                              '</p>'
                            : '<p class="stay-cart-modal__card-meta small mb-1 text-warning">Dates — add on confirm booking page</p>') +
                        '<p class="stay-cart-modal__card-meta small mb-0">' +
                        (room.adults || 1) + ' Adult' + ((room.adults || 1) !== 1 ? 's' : '') +
                        ', ' + (room.children || 0) + ' Child' + ((room.children || 0) !== 1 ? 'ren' : '') +
                        '</p>' +
                        (lineTotal > 0
                            ? '<p class="stay-cart-modal__card-price mb-0">' + formatMoney(lineTotal) + '</p>'
                            : '<p class="stay-cart-modal__card-price mb-0 text-muted small">Rate on request</p>');
                    linesEl.appendChild(card);
                });

                cart.experiences.forEach(function (exp) {
                    var card = document.createElement('article');
                    card.className = 'stay-cart-modal__card stay-cart-modal__card--exp';
                    card.innerHTML =
                        '<div class="stay-cart-modal__card-top">' +
                        '<span class="stay-cart-modal__card-type"><i class="fas ' + escapeHtml(exp.icon || 'fa-star') + ' me-1"></i> Activity</span>' +
                        '<button type="button" class="stay-cart-modal__remove" data-remove-exp="' + escapeHtml(exp.id) + '" aria-label="Remove activity"><i class="fas fa-trash-alt"></i></button>' +
                        '</div>' +
                        '<h6 class="stay-cart-modal__card-title">' + escapeHtml(exp.title || 'Experience') + '</h6>' +
                        '<p class="stay-cart-modal__card-meta small mb-0 text-muted">Experience interest — we will help arrange</p>';
                    linesEl.appendChild(card);
                });

                bindRemoveHandlers(linesEl);
            }

            if (emptyEl) {
                emptyEl.classList.toggle('d-none', count > 0);
            }
        }

        if (toggle) {
            toggle.addEventListener('click', openModal);
        }
        if (openSummary) {
            openSummary.addEventListener('click', openModal);
        }

        if (clearAllBtn) {
            clearAllBtn.addEventListener('click', function () {
                if (confirm('Remove all rooms and activities from your cart?')) {
                    IsangeStayCart.clear();
                }
            });
        }

        if (summaryModalEl) {
            summaryModalEl.addEventListener('hidden.bs.modal', function () {
                if (toggle) toggle.setAttribute('aria-expanded', 'false');
                purgeOrphanModalLayers();
            });
            summaryModalEl.addEventListener('shown.bs.modal', function () {
                if (toggle) toggle.setAttribute('aria-expanded', 'true');
            });
        }

        document.body.addEventListener('click', function (e) {
            var addExp = e.target.closest('[data-add-experience]');
            if (addExp) {
                e.preventDefault();
                var added = IsangeStayCart.addExperience({
                    id: addExp.getAttribute('data-add-experience'),
                    title: addExp.getAttribute('data-exp-title') || '',
                    icon: addExp.getAttribute('data-exp-icon') || 'fa-star',
                });
                if (added) {
                    addExp.classList.add('is-added');
                    addExp.setAttribute('aria-pressed', 'true');
                    var label = addExp.querySelector('[data-add-label]');
                    if (label) label.textContent = 'Added to cart';
                }
                return;
            }

            var addRoom = e.target.closest('[data-add-room]');
            if (addRoom) {
                e.preventDefault();
                var roomId = parseInt(addRoom.getAttribute('data-room-id'), 10);
                if (!roomId) {
                    return;
                }
                var added = IsangeStayCart.addRoom({
                    room_id: roomId,
                    slug: addRoom.getAttribute('data-room-slug') || '',
                    name: addRoom.getAttribute('data-room-name') || 'Room',
                    image: addRoom.getAttribute('data-room-image') || '',
                    price: addRoom.getAttribute('data-room-price') || '',
                    check_in: null,
                    check_out: null,
                    adults: 2,
                    children: 0,
                });
                if (added) {
                    addRoom.classList.add('is-added');
                    addRoom.setAttribute('aria-pressed', 'true');
                    var roomLabel = addRoom.querySelector('[data-add-room-label]');
                    if (roomLabel) {
                        roomLabel.textContent = 'Added';
                    } else if (!addRoom.querySelector('i')) {
                        addRoom.textContent = 'Added to cart';
                    }
                }
            }
        });

        function syncAddedButtons() {
            var cart = IsangeStayCart.get();
            var expIds = cart.experiences.map(function (e) { return e.id; });
            document.querySelectorAll('[data-add-experience]').forEach(function (btn) {
                var id = btn.getAttribute('data-add-experience');
                var added = expIds.indexOf(id) >= 0;
                btn.classList.toggle('is-added', added);
                btn.setAttribute('aria-pressed', added ? 'true' : 'false');
                var label = btn.querySelector('[data-add-label]');
                if (label && !btn.classList.contains('isange-experience-list__add')) {
                    label.textContent = added ? 'Added to cart' : 'Add to itinerary';
                }
            });
            document.querySelectorAll('[data-add-room]').forEach(function (btn) {
                var rid = parseInt(btn.getAttribute('data-room-id'), 10);
                var added = cart.rooms.some(function (r) { return r.room_id === rid; });
                btn.classList.toggle('is-added', added);
                btn.setAttribute('aria-pressed', added ? 'true' : 'false');
            });
        }

        window.__stayCartDockRender = render;

        IsangeStayCart.onChange(function () {
            render();
            syncAddedButtons();
        });
        render();
        syncAddedButtons();

        document.addEventListener('click', function (e) {
            var dockContinue = e.target.closest('#stay-cart-continue, #stay-cart-continue-modal');
            if (dockContinue) {
                if (document.getElementById('stay-checkout-form') && window.IsangeCheckout && typeof window.IsangeCheckout.next === 'function') {
                    e.preventDefault();
                    window.IsangeCheckout.next();
                    return;
                }
                if (!IsangeStayCart.hasItems()) {
                    e.preventDefault();
                    alert('Add a room or experience to continue.');
                }
            }
        });

        return true;
    }

    function bootStayCartDock() {
        if (initStayCartDock()) {
            return;
        }
        var attempts = 0;
        var timer = setInterval(function () {
            attempts += 1;
            if (initStayCartDock() || attempts > 40) {
                clearInterval(timer);
            }
        }, 100);
    }

    document.addEventListener('DOMContentLoaded', bootStayCartDock);
    document.addEventListener('isange:stay-cart-ready', bootStayCartDock);
    document.addEventListener('ma:spa-content', bootStayCartDock);

    if (document.readyState !== 'loading') {
        bootStayCartDock();
    }

    document.addEventListener('hidden.bs.modal', purgeOrphanModalLayers);
    window.addEventListener('pageshow', purgeOrphanModalLayers);
    setInterval(purgeOrphanModalLayers, 15000);
})();
</script>
