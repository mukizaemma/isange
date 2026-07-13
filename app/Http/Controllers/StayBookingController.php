<?php

namespace App\Http\Controllers;

use App\Models\GuestBookingRequest;
use App\Models\Room;
use App\Models\Setting;
use App\Models\SiteAnalyticsEvent;
use App\Support\ExperienceCatalog;
use App\Support\SpamProtection;
use App\Support\BookingEmailSender;
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
        $rooms = Room::orderBy('roomName')->get([
            'id', 'slug', 'roomName', 'price', 'price_rwf', 'image', 'accommodation_type',
            'discount_enabled', 'discount_type', 'discount_value',
        ]);
        $experiences = ExperienceCatalog::items($request->attributes->get('page_headers'));

        $prefillRoom = null;
        if ($request->filled('room')) {
            $prefillRoom = $rooms->firstWhere('slug', $request->query('room'));
        } elseif ($request->filled('room_id')) {
            $prefillRoom = $rooms->firstWhere('id', (int) $request->query('room_id'));
        }

        $hotelWhatsappReady = self::hotelWhatsappReady($setting);
        $hotelEmailReady = self::hotelEmailReady($setting);
        $prefillPayAtHotelChannel = in_array($request->query('channel'), ['whatsapp', 'email'], true)
            ? $request->query('channel')
            : null;

        return $this->spaView('frontend.booking-checkout', compact(
            'rooms',
            'experiences',
            'prefillRoom',
            'setting',
            'hotelWhatsappReady',
            'hotelEmailReady',
            'prefillPayAtHotelChannel',
        ), 'Confirm booking');
    }

    public function store(Request $request): RedirectResponse
    {
        SpamProtection::validateRequest($request);

        $setting = Setting::first();
        $hotelWhatsappReady = self::hotelWhatsappReady($setting);
        $hotelEmailReady = self::hotelEmailReady($setting);

        $validated = Validator::make($request->all(), [
            'cart_json' => 'required|json|max:65535',
            'guest_first_name' => 'nullable|string|max:120',
            'guest_last_name' => 'nullable|string|max:120',
            'guest_phone' => 'nullable|string|max:64',
            'guest_email' => 'nullable|email|max:255',
            'guest_country' => 'nullable|string|max:120',
            'payment_method' => 'required|in:pay_at_hotel',
            'pay_at_hotel_channel' => 'nullable|in:whatsapp,email',
            'airport_pickup' => 'sometimes|boolean',
            'airport_dropoff' => 'sometimes|boolean',
            'additional_requests' => 'nullable|string|max:5000',
            'terms_accepted' => 'accepted',
        ], [
            'terms_accepted.accepted' => 'Please accept the hotel policy and terms to continue.',
            'cart_json.required' => 'Your booking details are missing. Please set your stay dates and try again.',
        ])->validate();

        $cart = json_decode($validated['cart_json'], true);
        if (! is_array($cart) || ! self::cartHasItems($cart)) {
            return back()->withErrors(['cart_json' => 'Please set valid check-in and check-out dates, then try again.'])->withInput();
        }

        $rawItemCount = count($cart['rooms'] ?? []) + count($cart['experiences'] ?? []);
        $cart = self::sanitizeCart($cart);

        if (! self::cartHasItems($cart)) {
            return back()->withErrors([
                'cart_json' => $rawItemCount > 0
                    ? 'Some cart items could not be validated. Please remove and re-add them, then try again.'
                    : 'Please set valid check-in and check-out dates, then try again.',
            ])->withInput();
        }

        $channel = $validated['pay_at_hotel_channel'] ?? '';
        if ($channel === 'whatsapp' && ! $hotelWhatsappReady) {
            return back()->withErrors(['pay_at_hotel_channel' => 'WhatsApp booking is temporarily unavailable. Please use email.'])->withInput();
        }
        if ($channel === 'email' && ! $hotelEmailReady) {
            return back()->withErrors(['pay_at_hotel_channel' => 'Email booking is temporarily unavailable. Please use WhatsApp.'])->withInput();
        }
        if (! in_array($channel, ['whatsapp', 'email'], true)) {
            return back()->withErrors(['pay_at_hotel_channel' => 'Choose WhatsApp or email to send your reservation request.'])->withInput();
        }
        if ($channel === 'whatsapp' && ! self::guestWhatsappReady($validated['guest_phone'] ?? '')) {
            return back()->withErrors(['guest_phone' => 'Enter a valid mobile number with WhatsApp so we can reach you.'])->withInput();
        }
        if ($channel === 'email' && ! filter_var($validated['guest_email'] ?? '', FILTER_VALIDATE_EMAIL)) {
            return back()->withErrors(['guest_email' => 'Enter a valid email address so we can send your reservation.'])->withInput();
        }
        $fulfillment = $channel;

        foreach ($cart['rooms'] ?? [] as $roomLine) {
            if (empty($roomLine['check_in']) || empty($roomLine['check_out'])) {
                return back()->withErrors(['cart_json' => 'Each room needs check-in and check-out dates on the confirm booking page.'])->withInput();
            }
            if (! preg_match('/^\d{4}-\d{2}-\d{2}$/', (string) $roomLine['check_in'])
                || ! preg_match('/^\d{4}-\d{2}-\d{2}$/', (string) $roomLine['check_out'])) {
                return back()->withErrors(['cart_json' => 'Use valid dates for each room.'])->withInput();
            }
            if ($roomLine['check_out'] <= $roomLine['check_in']) {
                return back()->withErrors(['cart_json' => 'Check-out must be after check-in for all rooms.'])->withInput();
            }
        }

        $guestName = trim(($validated['guest_first_name'] ?? '').' '.($validated['guest_last_name'] ?? ''));
        if ($guestName === '') {
            $guestName = $validated['guest_email'] ?? $validated['guest_phone'] ?? 'Guest';
        }
        $stay = StayBookingMessageBuilder::primaryStayFromCart($cart);
        $totalUsd = StayBookingMessageBuilder::estimateTotalUsd($cart);
        $firstRoom = $cart['rooms'][0] ?? null;

        $guestPhone = trim((string) ($validated['guest_phone'] ?? ''));
        $guestEmail = trim((string) ($validated['guest_email'] ?? ''));
        $guestCountry = trim((string) ($validated['guest_country'] ?? ''));

        $guestPayload = [
            'guest_name' => $guestName,
            'guest_phone' => $guestPhone,
            'guest_email' => $guestEmail,
            'guest_country' => $guestCountry,
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
            'guest_phone' => $guestPhone,
            'guest_email' => $guestEmail,
            'guest_country' => $guestCountry,
            'payment_method' => $validated['payment_method'],
            'total_usd' => $totalUsd > 0 ? $totalUsd : null,
            'adults' => isset($firstRoom['adults']) ? (int) $firstRoom['adults'] : null,
            'children' => isset($firstRoom['children']) ? (int) $firstRoom['children'] : null,
            'fulfillment_choice' => $fulfillment,
            'message_body' => $body,
        ]);
        $record->update([
            'message_body' => GuestBookingRequest::appendReferenceToMessage($body, $record->public_id),
        ]);
        $record->refresh();

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

        if ($fulfillment === 'email') {
            $result = BookingEmailSender::sendOnSubmit($record, $setting);
            if ($result['hotel']) {
                $record->update(['completed_channel' => 'email']);
                SiteAnalyticsEvent::create([
                    'event_key' => 'booking_pay_delivery_email',
                    'properties' => [
                        'delivery' => 'resend',
                        'guest_ack' => $result['guest'],
                    ],
                    'session_id' => substr(sha1($request->session()->getId()), 0, 40),
                ]);

                $flash = ['email_sent' => true];
                if (! $result['guest']) {
                    $flash['warning'] = 'Your request was sent to the hotel, but we could not deliver the acknowledgement email to you. The hotel will still contact you.';
                }

                return redirect()
                    ->route('room.booking.email', $record->public_id)
                    ->with($flash);
            }

            return redirect()
                ->route('room.booking.email', $record->public_id)
                ->with('error', 'Your booking was saved, but we could not send the email automatically. You can send it via WhatsApp instead, or contact the hotel directly.');
        }

        return match ($fulfillment) {
            'whatsapp' => redirect()->route('room.booking.whatsapp', $record->public_id),
            default => redirect()->route('room.booking.confirmation', $record->public_id),
        };
    }

    public function catalog(Request $request): JsonResponse
    {
        $rooms = Room::orderBy('roomName')->get([
            'id', 'slug', 'roomName', 'price', 'price_rwf', 'image',
            'discount_enabled', 'discount_type', 'discount_value',
        ]);
        $experiences = ExperienceCatalog::items();

        return response()->json([
            'rooms' => $rooms->map(fn (Room $r) => [
                'room_id' => $r->id,
                'slug' => $r->slug,
                'name' => $r->roomName,
                'price' => $r->salePriceUsd(),
                'list_price' => $r->listPriceUsd(),
                'price_rwf' => $r->salePriceRwf(),
                'discount' => $r->hasActiveDiscount() ? [
                    'badge' => $r->discountBadgeLabel(),
                    'type' => $r->discount_type,
                    'value' => $r->discount_value,
                ] : null,
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
            $checkIn = $line['check_in'] ?? null;
            $checkOut = $line['check_out'] ?? null;
            $nights = 1;
            if ($checkIn && $checkOut) {
                $inTs = strtotime((string) $checkIn);
                $outTs = strtotime((string) $checkOut);
                if ($inTs && $outTs && $outTs > $inTs) {
                    $nights = max(1, (int) round(($outTs - $inTs) / 86400));
                }
            }

            $rooms[] = [
                'room_id' => $roomId,
                'slug' => $line['slug'] ?? null,
                'name' => substr((string) ($line['name'] ?? 'Room'), 0, 255),
                'image' => $line['image'] ?? null,
                'price' => $line['price'] ?? null,
                'check_in' => $checkIn,
                'check_out' => $checkOut,
                'nights' => $nights,
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
