/**
 * Unified stay cart: rooms + experiences (sessionStorage, mirrored to localStorage).
 */
(function (global) {
    'use strict';

    var STORAGE_KEY = 'isange_stay_cart';
    var LEGACY_KEY = 'isange_stay_cart';
    var listeners = [];

    function storage() {
        try {
            return window.sessionStorage;
        } catch (e) {
            return null;
        }
    }

    function defaultStay() {
        return {
            check_in: null,
            check_out: null,
            adults: 2,
            children: 0,
            rooms_count: 1,
        };
    }

    function emptyCart() {
        return { rooms: [], experiences: [], stay: defaultStay(), updated_at: null };
    }

    function normalizeStay(cart) {
        if (!cart.stay || typeof cart.stay !== 'object') {
            cart.stay = defaultStay();
        }
        cart.stay.adults = Math.max(1, Math.min(20, parseInt(cart.stay.adults, 10) || 2));
        cart.stay.children = Math.max(0, Math.min(20, parseInt(cart.stay.children, 10) || 0));
        cart.stay.rooms_count = Math.max(1, Math.min(10, parseInt(cart.stay.rooms_count, 10) || 1));
        return cart.stay;
    }

    function applyStayToRooms(cart) {
        var stay = normalizeStay(cart);
        cart.rooms.forEach(function (room) {
            if (stay.check_in) {
                room.check_in = stay.check_in;
            }
            if (stay.check_out) {
                room.check_out = stay.check_out;
            }
            room.adults = stay.adults;
            room.children = stay.children;
            room.nights = nightsBetween(room.check_in, room.check_out);
        });
    }

    function migrateLegacyCart() {
        var store = storage();
        if (!store || store.getItem(STORAGE_KEY)) {
            return;
        }
        try {
            var legacy = localStorage.getItem(LEGACY_KEY);
            if (legacy) {
                store.setItem(STORAGE_KEY, legacy);
            }
        } catch (e) {}
    }

    function load() {
        migrateLegacyCart();
        try {
            var store = storage();
            if (!store) {
                return emptyCart();
            }
            var raw = store.getItem(STORAGE_KEY);
            if (!raw) {
                return emptyCart();
            }
            var data = JSON.parse(raw);
            if (!data || typeof data !== 'object') {
                return emptyCart();
            }
            data.rooms = Array.isArray(data.rooms) ? data.rooms : [];
            data.experiences = Array.isArray(data.experiences) ? data.experiences : [];
            normalizeStay(data);
            return data;
        } catch (e) {
            return emptyCart();
        }
    }

    function save(cart) {
        cart.updated_at = new Date().toISOString();
        var json = JSON.stringify(cart);
        try {
            var store = storage();
            if (store) {
                store.setItem(STORAGE_KEY, json);
            }
            localStorage.setItem(STORAGE_KEY, json);
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

    function isPlaceholderRoom(room) {
        return !room || !room.room_id;
    }

    function stripPlaceholderRooms(cart) {
        cart.rooms = cart.rooms.filter(function (room) {
            return !isPlaceholderRoom(room);
        });
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

        hasSelectedRoom: function () {
            return load().rooms.some(function (room) {
                return room && room.room_id;
            });
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
            stripPlaceholderRooms(cart);
            var stay = normalizeStay(cart);
            if (!room.check_in && stay.check_in) {
                room.check_in = stay.check_in;
            }
            if (!room.check_out && stay.check_out) {
                room.check_out = stay.check_out;
            }
            if (!room.adults) {
                room.adults = stay.adults;
            }
            if (room.children === undefined || room.children === null) {
                room.children = stay.children;
            }
            room.nights = nightsBetween(room.check_in, room.check_out);
            cart.rooms.push(room);
            if (cart.rooms.length > stay.rooms_count) {
                stay.rooms_count = cart.rooms.length;
            }
            save(cart);
            return true;
        },

        removeRoom: function (index) {
            var cart = load();
            cart.rooms.splice(index, 1);
            save(cart);
        },

        updateRoom: function (index, updates) {
            var cart = load();
            if (!cart.rooms[index] || !updates) {
                return false;
            }
            var room = cart.rooms[index];
            if (updates.check_in !== undefined) {
                room.check_in = updates.check_in || null;
            }
            if (updates.check_out !== undefined) {
                room.check_out = updates.check_out || null;
            }
            if (updates.adults !== undefined) {
                room.adults = Math.max(1, Math.min(20, parseInt(updates.adults, 10) || 1));
            }
            if (updates.children !== undefined) {
                room.children = Math.max(0, Math.min(20, parseInt(updates.children, 10) || 0));
            }
            room.nights = nightsBetween(room.check_in, room.check_out);
            save(cart);
            return true;
        },

        repriceRooms: function (prices) {
            if (!prices || typeof prices !== 'object') {
                return false;
            }
            var cart = load();
            var changed = false;
            cart.rooms.forEach(function (room) {
                var current = prices[String(room.room_id)];
                if (!current) {
                    return;
                }
                room.price = current.price;
                room.list_price = current.list_price;
                room.discount_applied = !!current.discount_applied;
                changed = true;
            });
            if (changed) {
                save(cart);
            }
            return changed;
        },

        getStay: function () {
            return normalizeStay(load());
        },

        setStay: function (partial) {
            var cart = load();
            var stay = normalizeStay(cart);
            if (partial.check_in !== undefined) {
                stay.check_in = partial.check_in || null;
            }
            if (partial.check_out !== undefined) {
                stay.check_out = partial.check_out || null;
            }
            if (partial.adults !== undefined) {
                stay.adults = Math.max(1, Math.min(20, parseInt(partial.adults, 10) || 2));
            }
            if (partial.children !== undefined) {
                stay.children = Math.max(0, Math.min(20, parseInt(partial.children, 10) || 0));
            }
            if (partial.rooms_count !== undefined) {
                stay.rooms_count = Math.max(1, Math.min(10, parseInt(partial.rooms_count, 10) || 1));
            }
            applyStayToRooms(cart);
            save(cart);
        },

        ensureStayRequest: function (stay) {
            if (!stay || !stay.check_in || !stay.check_out || stay.check_out <= stay.check_in) {
                return false;
            }
            var cart = load();
            if (cart.rooms.some(function (room) { return room.room_id; }) || cart.experiences.length > 0) {
                applyStayToRooms(cart);
                save(cart);
                return true;
            }
            stripPlaceholderRooms(cart);
            cart.rooms.push({
                room_id: null,
                slug: '',
                name: 'Room to be confirmed',
                image: '',
                price: '',
                check_in: stay.check_in,
                check_out: stay.check_out,
                adults: Math.max(1, parseInt(stay.adults, 10) || 2),
                children: Math.max(0, parseInt(stay.children, 10) || 0),
                nights: nightsBetween(stay.check_in, stay.check_out),
            });
            normalizeStay(cart);
            cart.stay.check_in = stay.check_in;
            cart.stay.check_out = stay.check_out;
            cart.stay.adults = Math.max(1, parseInt(stay.adults, 10) || 2);
            cart.stay.children = Math.max(0, parseInt(stay.children, 10) || 0);
            cart.stay.rooms_count = Math.max(1, parseInt(stay.rooms_count, 10) || 1);
            save(cart);
            return true;
        },

        setRoomsCount: function (count) {
            var cart = load();
            var stay = normalizeStay(cart);
            stay.rooms_count = Math.max(1, Math.min(10, parseInt(count, 10) || 1));
            if (cart.rooms.length > 0) {
                var template = cart.rooms[0];
                while (cart.rooms.length < stay.rooms_count) {
                    cart.rooms.push({
                        room_id: template.room_id,
                        slug: template.slug,
                        name: template.name,
                        image: template.image,
                        price: template.price,
                        check_in: stay.check_in,
                        check_out: stay.check_out,
                        adults: stay.adults,
                        children: stay.children,
                        nights: nightsBetween(stay.check_in, stay.check_out),
                    });
                }
                while (cart.rooms.length > stay.rooms_count) {
                    cart.rooms.pop();
                }
                applyStayToRooms(cart);
            }
            save(cart);
        },

        roomsNeedDates: function () {
            var cart = load();
            if (cart.rooms.length === 0) {
                return false;
            }
            var stay = normalizeStay(cart);
            if (!stay.check_in || !stay.check_out) {
                return true;
            }
            return cart.rooms.some(function (room) {
                return !room.check_in || !room.check_out;
            });
        },

        toJson: function () {
            return JSON.stringify(load());
        },
    };

    global.IsangeStayCart = api;

    function signalReady() {
        notify();
        document.dispatchEvent(new CustomEvent('isange:stay-cart-ready'));
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', signalReady);
    } else {
        signalReady();
    }

    window.addEventListener('storage', function (e) {
        if (e.key === STORAGE_KEY) {
            notify();
        }
    });
})(typeof window !== 'undefined' ? window : this);
