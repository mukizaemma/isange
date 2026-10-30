<?php

namespace App\Http\Controllers;

use App\Models\GuestBookingRequest;
use App\Models\Room;
use App\Models\Setting;
use App\Models\SiteAnalyticsEvent;
use App\Support\BookingEmailSender;
use App\Support\StayBookingMessageBuilder;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ReservationManageController extends Controller
{
    public function show(string $publicId): View
    {
        $booking = $this->findBooking($publicId);
        $rooms = Room::query()->orderBy('roomName')->get(['id', 'roomName']);
        $cartRooms = is_array($booking->cart_items) ? ($booking->cart_items['rooms'] ?? []) : [];

        return view('frontend.reservation-manage', [
            'booking' => $booking,
            'rooms' => $rooms,
            'cartRooms' => is_array($cartRooms) ? array_values($cartRooms) : [],
            'updateUrl' => $booking->guestUpdateUrl(),
            'cancelUrl' => $booking->guestCancelUrl(),
        ]);
    }

    public function update(Request $request, string $publicId): RedirectResponse
    {
        $booking = $this->findBooking($publicId);

        if (! $booking->canGuestUpdate()) {
            return $this->backToManage($booking, 'error', $this->closedMessage($booking));
        }

        $existingLines = $this->roomLines($booking);
        $roomCount = count($existingLines);

        $rules = [
            'check_in' => ['required', 'date', 'after_or_equal:today'],
            'check_out' => ['required', 'date', 'after:check_in'],
            'adults' => ['required', 'integer', 'min:1', 'max:20'],
            'children' => ['required', 'integer', 'min:0', 'max:20'],
            'additional_requests' => ['nullable', 'string', 'max:5000'],
        ];

        if ($roomCount > 1) {
            $rules['room_ids'] = ['required', 'array', 'size:'.$roomCount];
            $rules['room_ids.*'] = ['required', 'integer', 'exists:rooms,id'];
        } else {
            $rules['room_id'] = ['required', 'integer', 'exists:rooms,id'];
        }

        $validated = $request->validate($rules, [
            'check_in.after_or_equal' => 'Check-in must be today or a future date.',
            'check_out.after' => 'Check-out must be after check-in.',
            'room_id.required' => 'Choose a room.',
            'room_ids.required' => 'Choose a room for each line.',
        ]);

        $checkIn = Carbon::parse($validated['check_in'])->toDateString();
        $checkOut = Carbon::parse($validated['check_out'])->toDateString();
        $adults = (int) $validated['adults'];
        $children = (int) $validated['children'];
        $requests = trim((string) ($validated['additional_requests'] ?? ''));
        $selectedIds = $roomCount > 1
            ? array_map('intval', $validated['room_ids'])
            : [(int) $validated['room_id']];

        $selectedRooms = Room::query()->whereIn('id', $selectedIds)->get()->keyBy('id');
        foreach ($selectedIds as $roomId) {
            $room = $selectedRooms->get($roomId);
            if (! $room) {
                return back()->withErrors(['room_id' => 'Choose a room that is still offered.'])->withInput();
            }
            $capacityError = $this->capacityMessage($room, $adults, $children);
            if ($capacityError !== null) {
                return back()->withErrors(['adults' => $capacityError])->withInput();
            }
        }

        $nights = max(1, (int) Carbon::parse($checkIn)->startOfDay()->diffInDays(Carbon::parse($checkOut)->startOfDay()));
        $discountEligible = (bool) $booking->discount_applied;
        $newLines = [];

        if ($existingLines === []) {
            $room = $selectedRooms->get($selectedIds[0]);
            $newLines[] = $this->roomLine($room, $checkIn, $checkOut, $nights, $adults, $children, $discountEligible);
        } else {
            foreach ($existingLines as $index => $line) {
                $room = $selectedRooms->get($selectedIds[$index]);
                $newLines[] = $this->roomLine($room, $checkIn, $checkOut, $nights, $adults, $children, $discountEligible, is_array($line) ? $line : []);
            }
        }

        $cart = is_array($booking->cart_items) ? $booking->cart_items : [];
        $cart['rooms'] = $newLines;
        $cart['experiences'] = is_array($cart['experiences'] ?? null) ? $cart['experiences'] : [];

        $total = StayBookingMessageBuilder::estimateTotalUsd($cart);
        $previousRoomIds = array_map(static fn ($line) => (int) (is_array($line) ? ($line['room_id'] ?? 0) : 0), $existingLines);
        if ($previousRoomIds === [] && $booking->room_id) {
            $previousRoomIds = [(int) $booking->room_id];
        }

        $previousRequests = trim((string) $booking->additional_requests);
        $previousTotal = round((float) ($booking->total_usd ?? 0), 2);
        $material = $booking->check_in?->toDateString() !== $checkIn
            || $booking->check_out?->toDateString() !== $checkOut
            || (int) $booking->adults !== $adults
            || (int) $booking->children !== $children
            || $previousRoomIds !== $selectedIds
            || abs($previousTotal - $total) > 0.009;
        $requestsChanged = $previousRequests !== $requests;

        if (! $material && ! $requestsChanged) {
            return $this->backToManage($booking, 'success', 'No changes to save.');
        }

        $before = [
            'status' => $booking->status,
            'check_in' => $booking->check_in?->toDateString() ?: '—',
            'check_out' => $booking->check_out?->toDateString() ?: '—',
            'adults' => (string) ($booking->adults ?? '—'),
            'children' => (string) ($booking->children ?? '—'),
            'room' => $booking->roomSummary(),
            'requests' => $previousRequests !== '' ? $previousRequests : '—',
            'total' => $booking->formattedTotalUsd(),
        ];

        $wasConfirmed = $booking->isConfirmed();
        $status = $booking->status;
        $confirmedAt = $booking->confirmed_at;
        if ($material && $wasConfirmed) {
            $status = GuestBookingRequest::STATUS_PENDING;
            $confirmedAt = null;
        }

        $discountApplied = collect($newLines)->contains(fn (array $line) => ! empty($line['discount_applied']));
        $setting = Setting::first();
        $guestPayload = [
            'guest_name' => $booking->guest_name,
            'guest_phone' => $booking->guest_phone,
            'guest_email' => $booking->guest_email,
            'guest_country' => $booking->guest_country,
            'airport_pickup' => $booking->airport_pickup,
            'airport_dropoff' => $booking->airport_dropoff,
            'additional_requests' => $requests !== '' ? $requests : null,
            'total_usd' => $total,
        ];
        $body = StayBookingMessageBuilder::build(
            $guestPayload,
            $cart,
            $setting,
            (string) ($booking->payment_method ?: 'pay_at_hotel'),
            (string) $booking->fulfillment_choice
        );

        $booking->update([
            'room_id' => $selectedIds[0],
            'cart_items' => $cart,
            'check_in' => $checkIn,
            'check_out' => $checkOut,
            'additional_requests' => $requests !== '' ? $requests : null,
            'total_usd' => $total > 0 ? $total : null,
            'discount_applied' => $discountApplied,
            'adults' => $adults,
            'children' => $children,
            'status' => $status,
            'confirmed_at' => $confirmedAt,
            'message_body' => GuestBookingRequest::appendReferenceToMessage($body, $booking->public_id),
        ]);
        $booking->refresh()->load('room');

        $changes = $this->describeChanges($before, $booking);
        $needsReview = $wasConfirmed && $material;
        $hotelSent = BookingEmailSender::sendHotelGuestUpdated($booking, $changes, $setting);
        $guestSent = BookingEmailSender::sendGuestUpdated($booking, $changes, $needsReview, $setting);

        SiteAnalyticsEvent::create([
            'event_key' => 'booking_guest_updated',
            'properties' => [
                'reference' => $booking->public_id,
                'reopened' => $needsReview,
                'hotel_email' => $hotelSent,
                'guest_email' => $guestSent,
            ],
            'session_id' => null,
        ]);

        if ($needsReview) {
            $message = 'Your reservation was updated and sent back to the hotel for confirmation.';
        } else {
            $message = 'Your reservation details were updated.';
        }

        if (! $hotelSent) {
            return $this->backToManage($booking, 'warning', $message.' We could not email the hotel just now. Please contact the hotel if you do not hear back.');
        }

        return $this->backToManage($booking, 'success', $message.' The hotel has been notified.');
    }

    public function cancel(Request $request, string $publicId): RedirectResponse
    {
        $booking = $this->findBooking($publicId);

        if (! $booking->canGuestCancel()) {
            return $this->backToManage($booking, 'error', $this->closedMessage($booking));
        }

        $booking->update([
            'status' => GuestBookingRequest::STATUS_CANCELED_BY_GUEST,
            'canceled_at' => now(),
        ]);
        $booking->refresh()->load('room');

        $setting = Setting::first();
        $hotelSent = BookingEmailSender::sendHotelGuestCanceled($booking, $setting);
        BookingEmailSender::sendGuestCanceled($booking, $setting);

        SiteAnalyticsEvent::create([
            'event_key' => 'booking_guest_canceled',
            'properties' => [
                'reference' => $booking->public_id,
                'hotel_email' => $hotelSent,
            ],
            'session_id' => null,
        ]);

        $message = 'Your reservation has been canceled.';
        if (! $hotelSent) {
            return $this->backToManage($booking, 'warning', $message.' We could not email the hotel just now. Please contact the hotel so they can release the room.');
        }

        return $this->backToManage($booking, 'success', $message.' The hotel has been notified.');
    }

    private function findBooking(string $publicId): GuestBookingRequest
    {
        return GuestBookingRequest::with('room')->where('public_id', $publicId)->firstOrFail();
    }

    private function backToManage(GuestBookingRequest $booking, string $key, string $message): RedirectResponse
    {
        return redirect()->to($booking->guestManageUrl())->with($key, $message);
    }

    private function closedMessage(GuestBookingRequest $booking): string
    {
        if ($booking->isCanceled()) {
            return 'This reservation is already canceled.';
        }

        if ($booking->checkInHasStarted()) {
            return 'This reservation can no longer be changed or canceled. That option closes on the check-in date.';
        }

        return 'This reservation is closed and can no longer be changed.';
    }

    /**
     * @return list<array<string, mixed>>
     */
    private function roomLines(GuestBookingRequest $booking): array
    {
        $lines = is_array($booking->cart_items) ? ($booking->cart_items['rooms'] ?? []) : [];

        return is_array($lines) ? array_values(array_filter($lines, 'is_array')) : [];
    }

    /**
     * @param  array<string, mixed>  $previous
     * @return array<string, mixed>
     */
    private function roomLine(Room $room, string $checkIn, string $checkOut, int $nights, int $adults, int $children, bool $discountEligible, array $previous = []): array
    {
        $pricing = $room->pricingForStay($discountEligible, $checkIn, $checkOut);

        return array_merge($previous, [
            'room_id' => $room->id,
            'slug' => $room->slug,
            'name' => $room->roomName,
            'image' => $room->image,
            'price' => $pricing['price'],
            'list_price' => $pricing['list_price'],
            'discount_applied' => $pricing['discount_applied'],
            'check_in' => $checkIn,
            'check_out' => $checkOut,
            'nights' => $nights,
            'adults' => $adults,
            'children' => $children,
        ]);
    }

    private function capacityMessage(Room $room, int $adults, int $children): ?string
    {
        $maxAdults = (int) ($room->maxAdults ?? 0);
        $maxChildren = (int) ($room->maxChildren ?? 0);

        if ($maxAdults > 0 && $adults > $maxAdults) {
            return $room->roomName.' allows up to '.$maxAdults.' adult'.($maxAdults === 1 ? '' : 's').'.';
        }

        if ($maxChildren > 0 && $children > $maxChildren) {
            return $room->roomName.' allows up to '.$maxChildren.' '.($maxChildren === 1 ? 'child' : 'children').'.';
        }

        return null;
    }

    /**
     * @param  array{status: string, check_in: string, check_out: string, adults: string, children: string, room: string, requests: string, total: string}  $before
     */
    private function describeChanges(array $before, GuestBookingRequest $booking): string
    {
        $afterRequests = trim((string) $booking->additional_requests);
        $pairs = [
            'Check-in' => [$before['check_in'], $booking->check_in?->toDateString() ?: '—'],
            'Check-out' => [$before['check_out'], $booking->check_out?->toDateString() ?: '—'],
            'Adults' => [$before['adults'], (string) $booking->adults],
            'Children' => [$before['children'], (string) $booking->children],
            'Room' => [$before['room'], $booking->roomSummary()],
            'Special requests' => [$before['requests'], $afterRequests !== '' ? $afterRequests : '—'],
            'Estimated total' => [$before['total'], $booking->formattedTotalUsd()],
        ];

        $lines = [];
        foreach ($pairs as $label => [$old, $new]) {
            if ((string) $old !== (string) $new) {
                $lines[] = $label.': '.$old.' → '.$new;
            }
        }

        if ($before['status'] !== $booking->status) {
            $lines[] = 'Status: '.GuestBookingRequest::statusLabel($before['status']).' → '.GuestBookingRequest::statusLabel($booking->status);
        }

        return $lines === [] ? 'Details were saved.' : implode("\n", $lines);
    }
}
