/**
 * Unified stay cart: rooms + experiences (localStorage).
 */
(function (global) {
    'use strict';

    var STORAGE_KEY = 'isange_stay_cart';
    var listeners = [];

    function emptyCart() {
        return { rooms: [], experiences: [], updated_at: null };
    }

    function load() {
        try {
            var raw = localStorage.getItem(STORAGE_KEY);
            if (!raw) {
                return emptyCart();
            }
            var data = JSON.parse(raw);
            if (!data || typeof data !== 'object') {
                return emptyCart();
            }
            data.rooms = Array.isArray(data.rooms) ? data.rooms : [];
            data.experiences = Array.isArray(data.experiences) ? data.experiences : [];
            return data;
        } catch (e) {
            return emptyCart();
        }
    }

    function save(cart) {
        cart.updated_at = new Date().toISOString();
        try {
            localStorage.setItem(STORAGE_KEY, JSON.stringify(cart));
        } catch (e) {}
        notify();
    }

    function notify() {
        listeners.forEach(function (fn) {
            try {
                fn(load());
            } catch (e) {}
        });
        document.dispatchEvent(new CustomEvent('isange:stay-cart-changed', { detail: load() }));
    }

    function nightsBetween(checkIn, checkOut) {
        if (!checkIn || !checkOut) {
            return 1;
        }
        var a = new Date(checkIn + 'T12:00:00');
        var b = new Date(checkOut + 'T12:00:00');
        var diff = Math.round((b - a) / 86400000);
        return diff > 0 ? diff : 1;
    }

    function parsePrice(val) {
        var n = parseFloat(String(val || '').replace(/[^0-9.]/g, ''));
        return isNaN(n) ? 0 : n;
    }

    var api = {
        onChange: function (fn) {
            if (typeof fn === 'function') {
                listeners.push(fn);
            }
        },

        get: load,

        clear: function () {
            save(emptyCart());
        },

        count: function () {
            var c = load();
            return c.rooms.length + c.experiences.length;
        },

        hasItems: function () {
            return api.count() > 0;
        },

        estimateTotalUsd: function () {
            var total = 0;
            load().rooms.forEach(function (room) {
                var price = parsePrice(room.price);
                var nights = room.nights || nightsBetween(room.check_in, room.check_out);
                total += price * nights;
            });
            return Math.round(total * 100) / 100;
        },

        addExperience: function (exp) {
            if (!exp || !exp.id) {
                return false;
            }
            var cart = load();
            if (cart.experiences.some(function (e) {
                return e.id === exp.id;
            })) {
                return false;
            }
            cart.experiences.push({
                id: exp.id,
                title: exp.title || exp.id,
                icon: exp.icon || 'fa-star',
            });
            save(cart);
            return true;
        },

        removeExperience: function (id) {
            var cart = load();
            cart.experiences = cart.experiences.filter(function (e) {
                return e.id !== id;
            });
            save(cart);
        },

        addRoom: function (room) {
            if (!room || !room.room_id) {
                return false;
            }
            var cart = load();
            room.nights = nightsBetween(room.check_in, room.check_out);
            cart.rooms.push(room);
            save(cart);
            return true;
        },

        removeRoom: function (index) {
            var cart = load();
            cart.rooms.splice(index, 1);
            save(cart);
        },

        toJson: function () {
            return JSON.stringify(load());
        },
    };

    global.IsangeStayCart = api;

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', notify);
    } else {
        notify();
    }
})(typeof window !== 'undefined' ? window : this);
