@php
    $hotelWhatsappReady = \App\Http\Controllers\StayBookingController::hotelWhatsappReady($setting ?? null);
    $hotelEmailReady = \App\Http\Controllers\StayBookingController::hotelEmailReady($setting ?? null);
@endphp

<div id="stay-cart-dock" class="stay-cart-dock d-none" aria-live="polite" data-checkout-url="{{ route('booking.checkout') }}">
    <div class="stay-cart-dock__bar">
        <button type="button" class="stay-cart-dock__toggle" id="stay-cart-toggle" aria-expanded="false" aria-controls="stay-cart-panel">
            <i class="fas fa-chevron-up" aria-hidden="true"></i>
        </button>
        <div class="stay-cart-dock__summary">
            <span class="stay-cart-dock__total" id="stay-cart-total">$0.00</span>
            <span class="stay-cart-dock__label" id="stay-cart-label">Your stay cart</span>
        </div>
        <a href="{{ route('booking.checkout') }}" class="theme-btn stay-cart-dock__continue" id="stay-cart-continue">Continue <i class="far fa-angle-right"></i></a>
    </div>
    <div id="stay-cart-panel" class="stay-cart-dock__panel collapse">
        <div class="stay-cart-dock__panel-inner">
            <p class="stay-cart-dock__hint small mb-2">Rooms and experiences you have selected for your stay.</p>
            <div id="stay-cart-lines" class="stay-cart-dock__lines"></div>
            <p class="stay-cart-dock__empty small text-muted mb-0 d-none" id="stay-cart-empty">Your cart is empty.</p>
        </div>
    </div>
</div>

@include('frontend.includes.stay-add-room-modal')

<script src="{{ asset('assets/js/stay-booking-cart.js') }}" defer></script>
<script>
(function () {
    function initStayCartDock() {
        if (!window.IsangeStayCart) return;

        var dock = document.getElementById('stay-cart-dock');
        var panel = document.getElementById('stay-cart-panel');
        var toggle = document.getElementById('stay-cart-toggle');
        var linesEl = document.getElementById('stay-cart-lines');
        var totalEl = document.getElementById('stay-cart-total');
        var labelEl = document.getElementById('stay-cart-label');
        var emptyEl = document.getElementById('stay-cart-empty');
        var continueBtn = document.getElementById('stay-cart-continue');

        if (!dock) return;

        function formatMoney(n) {
            return '$' + (Number(n) || 0).toFixed(2);
        }

        function render() {
            var cart = IsangeStayCart.get();
            var count = IsangeStayCart.count();
            var total = IsangeStayCart.estimateTotalUsd();

            if (count === 0) {
                dock.classList.add('d-none');
                return;
            }

            dock.classList.remove('d-none');
            totalEl.textContent = formatMoney(total);
            labelEl.textContent = count === 1 ? '1 item in cart' : count + ' items in cart';

            if (linesEl) {
                linesEl.innerHTML = '';
                cart.rooms.forEach(function (room, idx) {
                    var div = document.createElement('div');
                    div.className = 'stay-cart-line';
                    div.innerHTML =
                        '<div class="stay-cart-line__body">' +
                        '<strong>' + (room.name || 'Room') + '</strong>' +
                        '<span class="small d-block">' + (room.check_in || '') + ' → ' + (room.check_out || '') +
                        ' · ' + (room.adults || 1) + ' adult(s)</span>' +
                        '</div>' +
                        '<button type="button" class="btn btn-sm btn-outline-light stay-cart-line__remove" data-remove-room="' + idx + '" aria-label="Remove room">×</button>';
                    linesEl.appendChild(div);
                });
                cart.experiences.forEach(function (exp) {
                    var div = document.createElement('div');
                    div.className = 'stay-cart-line stay-cart-line--exp';
                    div.innerHTML =
                        '<div class="stay-cart-line__body">' +
                        '<strong><i class="fas ' + (exp.icon || 'fa-star') + ' me-1"></i>' + (exp.title || 'Experience') + '</strong>' +
                        '<span class="small d-block text-muted">Experience interest</span>' +
                        '</div>' +
                        '<button type="button" class="btn btn-sm btn-outline-light stay-cart-line__remove" data-remove-exp="' + exp.id + '" aria-label="Remove">×</button>';
                    linesEl.appendChild(div);
                });
            }

            if (emptyEl) {
                emptyEl.classList.toggle('d-none', count > 0);
            }

            linesEl.querySelectorAll('[data-remove-room]').forEach(function (btn) {
                btn.addEventListener('click', function () {
                    IsangeStayCart.removeRoom(parseInt(btn.getAttribute('data-remove-room'), 10));
                });
            });
            linesEl.querySelectorAll('[data-remove-exp]').forEach(function (btn) {
                btn.addEventListener('click', function () {
                    IsangeStayCart.removeExperience(btn.getAttribute('data-remove-exp'));
                });
            });
        }

        if (toggle && panel) {
            toggle.addEventListener('click', function () {
                var open = panel.classList.toggle('show');
                toggle.setAttribute('aria-expanded', open ? 'true' : 'false');
                toggle.querySelector('i').classList.toggle('fa-chevron-up', open);
                toggle.querySelector('i').classList.toggle('fa-chevron-down', !open);
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
                var modal = document.getElementById('stayAddRoomModal');
                if (modal && window.bootstrap) {
                    modal.querySelector('[name="room_id"]').value = addRoom.getAttribute('data-room-id') || '';
                    modal.querySelector('[name="room_name"]').value = addRoom.getAttribute('data-room-name') || '';
                    modal.querySelector('[name="room_slug"]').value = addRoom.getAttribute('data-room-slug') || '';
                    modal.querySelector('[name="room_price"]').value = addRoom.getAttribute('data-room-price') || '';
                    modal.querySelector('[name="room_image"]').value = addRoom.getAttribute('data-room-image') || '';
                    bootstrap.Modal.getOrCreateInstance(modal).show();
                }
            }
        });

        var roomForm = document.getElementById('stay-add-room-form');
        if (roomForm) {
            roomForm.addEventListener('submit', function (e) {
                e.preventDefault();
                var fd = new FormData(roomForm);
                var checkIn = fd.get('check_in');
                var checkOut = fd.get('check_out');
                if (!checkIn || !checkOut || checkOut <= checkIn) {
                    alert('Please choose valid check-in and check-out dates.');
                    return;
                }
                IsangeStayCart.addRoom({
                    room_id: parseInt(fd.get('room_id'), 10),
                    slug: fd.get('room_slug'),
                    name: fd.get('room_name'),
                    image: fd.get('room_image'),
                    price: fd.get('room_price'),
                    check_in: checkIn,
                    check_out: checkOut,
                    adults: parseInt(fd.get('adults'), 10) || 1,
                    children: parseInt(fd.get('children'), 10) || 0,
                });
                var modal = document.getElementById('stayAddRoomModal');
                if (modal && window.bootstrap) {
                    bootstrap.Modal.getInstance(modal)?.hide();
                }
                roomForm.reset();
            });
        }

        function syncAddedButtons() {
            var ids = IsangeStayCart.get().experiences.map(function (e) { return e.id; });
            document.querySelectorAll('[data-add-experience]').forEach(function (btn) {
                var id = btn.getAttribute('data-add-experience');
                var added = ids.indexOf(id) >= 0;
                btn.classList.toggle('is-added', added);
                btn.setAttribute('aria-pressed', added ? 'true' : 'false');
                var label = btn.querySelector('[data-add-label]');
                if (label) {
                    label.textContent = added ? 'Added to cart' : (btn.classList.contains('isange-experience-list__add') ? '' : 'Add to itinerary');
                }
            });
        }

        IsangeStayCart.onChange(function () {
            render();
            syncAddedButtons();
        });
        render();
        syncAddedButtons();

        document.addEventListener('click', function (e) {
            if (e.target.closest('#stay-cart-continue') && !IsangeStayCart.hasItems()) {
                e.preventDefault();
                alert('Add a room or experience to continue.');
            }
        });
    }

    document.addEventListener('DOMContentLoaded', initStayCartDock);
    document.addEventListener('ma:spa-content', initStayCartDock);
})();
</script>
