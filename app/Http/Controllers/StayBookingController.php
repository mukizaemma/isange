<?php

namespace App\Http\Controllers;

use App\Models\GuestBookingRequest;
use App\Models\Room;
use App\Models\Setting;
use App\Models\SiteAnalyticsEvent;
use App\Support\ExperienceCatalog;
use App\Support\StayBookingMessageBuilder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;

class StayBookingController extends Controller
{
    use Concerns\RendersSpaFragment;

    public function checkout(Request $request): View|Response
    {
        SiteAnalyticsEvent::create([
            'event_key' => 'booking_checkout_view',
            'properties' => [],
            'session_id' => substr(sha1($request->session()->getId()), 0, 40),
        ]);

        $setting = Setting::first();
        $rooms = Room::orderBy('roomName')->get(['id', 'slug', 'roomName', 'price', 'price_rwf', 'image', 'accommodation_type']);
        $experiences = ExperienceCatalog::items($request->attributes->get('page_headers'));

        $prefillRoom = null;
        if ($request->filled('room')) {
            $prefillRoom = $rooms->firstWhere('slug', $request->query('room'));
        } elseif ($request->filled('room_id')) {
            $prefillRoom = $rooms->firstWhere('id', (int) $request->query('room_id'));
        }

        $hotelWhatsappReady = self::hotelWhatsappReady($setting);
        $hotelEmailReady = self::hotelEmailReady($setting);

        return $this->spaView('frontend.booking-checkout', compact(
            'rooms',
            'experiences',
            'prefillRoom',
            'setting',
            'hotelWhatsappReady',
            'hotelEmailReady',
        ), 'Confirm booking');
    }

    public function store(Request $request): RedirectResponse
    {
        $setting = Setting::first();
        $hotelWhatsappReady = self::hotelWhatsappReady($setting);
        $hotelEmailReady = self::hotelEmailReady($setting);

        $validated = Validator::make($request->all(), [
            'cart_json' => 'required|json',
            'guest_first_name' => 'required|string|max:120',
            'guest_last_name' => 'required|string|max:120',
            'guest_phone' => 'required|string|max:64',
            'guest_email' => 'required|email|max:255',
            'guest_country' => 'required|string|max:120',
            'payment_method' => 'required|in:pay_now,pay_at_hotel',
            'pay_at_hotel_channel' => 'nullable|in:whatsapp,email',
            'airport_pickup' => 'sometimes|boolean',
            'airport_dropoff' => 'sometimes|boolean',
            'additional_requests' => 'nullable|string|max:5000',
            'terms_accepted' => 'accepted',
        ], [
            'terms_accepted.accepted' => 'Please accept the hotel policy and terms to continue.',
            'cart_json.required' => 'Your cart is empty. Add a room or experience before booking.',
        ])->validate();

        $cart = json_decode($validated['cart_json'], true);
        if (! is_array($cart) || ! self::cartHasItems($cart)) {
            return back()->withErrors(['cart_json' => 'Add at least one room or experience to your cart.'])->withInput();
        }

        $cart = self::sanitizeCart($cart);

        if ($validated['payment_method'] === 'pay_at_hotel') {
            if (! self::guestWhatsappReady($validated['guest_phone'])) {
                return back()->withErrors(['guest_phone' => 'Enter a valid mobile number with WhatsApp so we can reach you.'])->withInput();
            }
            if (! filter_var($validated['guest_email'], FILTER_VALIDATE_EMAIL)) {
                return back()->withErrors(['guest_email' => 'Enter a valid email address.'])->withInput();
            }
            $channel = $validated['pay_at_hotel_channel'] ?? '';
            if ($channel === 'whatsapp' && ! $hotelWhatsappReady) {
                return back()->withErrors(['pay_at_hotel_channel' => 'WhatsApp booking is temporarily unavailable. Please use email or pay online.'])->withInput();
            }
            if ($channel === 'email' && ! $hotelEmailReady) {
                return back()->withErrors(['pay_at_hotel_channel' => 'Email booking is temporarily unavailable. Please use WhatsApp or pay online.'])->withInput();
            }
            if (! in_array($channel, ['whatsapp', 'email'], true)) {
                return back()->withErrors(['pay_at_hotel_channel' => 'Choose WhatsApp or email to send your reservation request.'])->withInput();
            }
            $fulfillment = $channel;
        } else {
            $fulfillment = 'direct_pay';
        }

        foreach ($cart['rooms'] ?? [] as $roomLine) {
            if (empty($roomLine['check_in']) || empty($roomLine['check_out'])) {
                return back()->withErrors(['cart_json' => 'Each room needs check-in and check-out dates.'])->withInput();
            }
            if ($roomLine['check_out'] <= $roomLine['check_in']) {
                return back()->withErrors(['cart_json' => 'Check-out must be after check-in for all rooms.'])->withInput();
            }
        }

        $guestName = trim($validated['guest_first_name'].' '.$validated['guest_last_name']);
        $stay = StayBookingMessageBuilder::primaryStayFromCart($cart);
        $totalUsd = StayBookingMessageBuilder::estimateTotalUsd($cart);
        $firstRoom = $cart['rooms'][0] ?? null;

        $guestPayload = [
            'guest_name' => $guestName,
            'guest_phone' => $validated['guest_phone'],
            'guest_email' => $validated['guest_email'],
            'guest_country' => $validated['guest_country'],
            'airport_pickup' => $request->boolean('airport_pickup'),
            'airport_dropoff' => $request->boolean('airport_dropoff'),
            'additional_requests' => $validated['additional_requests'] ?? null,
            'total_usd' => $totalUsd,
        ];

        $body = StayBookingMessageBuilder::build(
            $guestPayload,
            $cart,
            $setting,
            $validated['payment_method'],
            $fulfillment
        );

        $record = GuestBookingRequest::create([
            'room_id' => $stay['room_id'],
            'cart_items' => $cart,
            'check_in' => $stay['check_in'],
            'check_out' => $stay['check_out'],
            'airport_pickup' => $guestPayload['airport_pickup'],
            'airport_dropoff' => $guestPayload['airport_dropoff'],
            'additional_requests' => $guestPayload['additional_requests'],
            'guest_name' => $guestName,
            'guest_phone' => $validated['guest_phone'],
            'guest_email' => $validated['guest_email'],
            'guest_country' => $validated['guest_country'],
            'payment_method' => $validated['payment_method'],
            'total_usd' => $totalUsd > 0 ? $totalUsd : null,
            'adults' => isset($firstRoom['adults']) ? (int) $firstRoom['adults'] : null,
            'children' => isset($firstRoom['children']) ? (int) $firstRoom['children'] : null,
            'fulfillment_choice' => $fulfillment,
            'message_body' => $body,
        ]);

        SiteAnalyticsEvent::create([
            'event_key' => 'stay_cart_submitted',
            'properties' => [
                'payment_method' => $validated['payment_method'],
                'fulfillment' => $fulfillment,
                'rooms' => count($cart['rooms'] ?? []),
                'experiences' => count($cart['experiences'] ?? []),
            ],
            'session_id' => substr(sha1($request->session()->getId()), 0, 40),
        ]);

        $request->session()->flash('clear_stay_cart', true);

        return match ($fulfillment) {
            'direct_pay' => redirect()->route('pay.dpo', array_filter([
                'booking' => $record->public_id,
            ]))->with('booking_public_id', $record->public_id),
            'whatsapp' => redirect()->route('room.booking.whatsapp', $record->public_id),
            'email' => redirect()->route('room.booking.email', $record->public_id),
        };
    }

    public function catalog(Request $request): JsonResponse
    {
        $rooms = Room::orderBy('roomName')->get(['id', 'slug', 'roomName', 'price', 'price_rwf', 'image']);
        $experiences = ExperienceCatalog::items();

        return response()->json([
            'rooms' => $rooms->map(fn (Room $r) => [
                'room_id' => $r->id,
                'slug' => $r->slug,
                'name' => $r->roomName,
                'price' => $r->price,
                'price_rwf' => $r->price_rwf,
                'image' => $r->image ? asset('storage/images/rooms/'.$r->image) : null,
            ]),
            'experiences' => collect($experiences)->map(fn ($e) => [
                'id' => $e['id'] ?? '',
                'title' => $e['title'] ?? '',
                'icon' => $e['icon'] ?? 'fa-star',
                'text' => $e['text'] ?? '',
            ]),
            'checkout_url' => route('booking.checkout'),
            'hotel_whatsapp_ready' => self::hotelWhatsappReady(Setting::first()),
            'hotel_email_ready' => self::hotelEmailReady(Setting::first()),
        ]);
    }

    /**
     * @param  array<string, mixed>  $cart
     */
    private static function cartHasItems(array $cart): bool
    {
        return count($cart['rooms'] ?? []) > 0 || count($cart['experiences'] ?? []) > 0;
    }

    /**
     * @param  array<string, mixed>  $cart
     * @return array{rooms: list<array<string, mixed>>, experiences: list<array<string, mixed>>}
     */
    private static function sanitizeCart(array $cart): array
    {
        $rooms = [];
        foreach ($cart['rooms'] ?? [] as $line) {
            if (! is_array($line)) {
                continue;
            }
            $roomId = isset($line['room_id']) ? (int) $line['room_id'] : null;
            if ($roomId && ! Room::whereKey($roomId)->exists()) {
                continue;
            }
            $rooms[] = [
                'room_id' => $roomId,
                'slug' => $line['slug'] ?? null,
                'name' => substr((string) ($line['name'] ?? 'Room'), 0, 255),
                'image' => $line['image'] ?? null,
                'price' => $line['price'] ?? null,
                'check_in' => $line['check_in'] ?? null,
                'check_out' => $line['check_out'] ?? null,
                'nights' => (int) ($line['nights'] ?? 1),
                'adults' => max(1, min(20, (int) ($line['adults'] ?? 1))),
                'children' => max(0, min(20, (int) ($line['children'] ?? 0))),
            ];
        }

        $experiences = [];
        $knownIds = collect(ExperienceCatalog::items())->pluck('id')->filter()->all();
        foreach ($cart['experiences'] ?? [] as $line) {
            if (! is_array($line)) {
                continue;
            }
            $id = (string) ($line['id'] ?? '');
            if ($id === '' || ! in_array($id, $knownIds, true)) {
                continue;
            }
            $catalog = ExperienceCatalog::find($id);
            $experiences[] = [
                'id' => $id,
                'title' => $catalog['title'] ?? $line['title'] ?? $id,
                'icon' => $catalog['icon'] ?? $line['icon'] ?? 'fa-star',
            ];
        }

        return ['rooms' => $rooms, 'experiences' => $experiences];
    }

    public static function hotelWhatsappReady(?Setting $setting): bool
    {
        $digits = preg_replace('/\D+/', '', (string) ($setting->phone ?? ''));

        return strlen($digits) >= 8;
    }

    public static function hotelEmailReady(?Setting $setting): bool
    {
        $email = trim((string) ($setting->email ?? ''));

        return $email !== '' && filter_var($email, FILTER_VALIDATE_EMAIL);
    }

    public static function guestWhatsappReady(string $phone): bool
    {
        $digits = preg_replace('/\D+/', '', $phone);

        return strlen($digits) >= 8;
    }
}
