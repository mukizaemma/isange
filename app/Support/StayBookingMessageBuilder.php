<?php

namespace App\Support;

use App\Models\Room;
use App\Models\Setting;

class StayBookingMessageBuilder
{
    /**
     * @param  array<string, mixed>  $guest
     * @param  array{rooms: list<array<string, mixed>>, experiences: list<array<string, mixed>>}  $cart
     */
    public static function build(array $guest, array $cart, ?Setting $setting, string $paymentMethod, string $fulfillmentChoice): string
    {
        $hotel = $setting->company ?? 'Hotel';
        $lines = [];
        $lines[] = '*'.$hotel.' — stay & experience request*';
        $lines[] = '';

        $rooms = $cart['rooms'] ?? [];
        if ($rooms !== []) {
            $lines[] = '*Rooms*';
            foreach ($rooms as $i => $room) {
                $n = $i + 1;
                $lines[] = $n.'. '.($room['name'] ?? 'Room');
                if (! empty($room['check_in']) && ! empty($room['check_out'])) {
                    $lines[] = '   Dates: '.$room['check_in'].' → '.$room['check_out'];
                    if (! empty($room['nights'])) {
                        $lines[] = '   Nights: '.$room['nights'];
                    }
                }
                $lines[] = '   Guests: '.(int) ($room['adults'] ?? 1).' adult(s), '.(int) ($room['children'] ?? 0).' child(ren)';
                if (! empty($room['price'])) {
                    $lines[] = '   Rate: $'.number_format((float) $room['price'], 2).' / night';
                }
            }
            $lines[] = '';
        }

        $experiences = $cart['experiences'] ?? [];
        if ($experiences !== []) {
            $lines[] = '*Experiences of interest*';
            foreach ($experiences as $exp) {
                $lines[] = '• '.($exp['title'] ?? 'Experience');
            }
            $lines[] = '';
        }

        if (! empty($guest['airport_pickup']) || ! empty($guest['airport_dropoff'])) {
            $lines[] = 'Airport pickup: '.(! empty($guest['airport_pickup']) ? 'Yes' : 'No');
            $lines[] = 'Airport drop-off: '.(! empty($guest['airport_dropoff']) ? 'Yes' : 'No');
            $lines[] = '';
        }

        if (! empty($guest['additional_requests'])) {
            $lines[] = 'Special requests: '.$guest['additional_requests'];
            $lines[] = '';
        }

        $lines[] = '*Guest contact*';
        $lines[] = 'Name: '.($guest['guest_name'] ?? '');
        $lines[] = 'Phone (WhatsApp): '.($guest['guest_phone'] ?? '');
        $lines[] = 'Email: '.($guest['guest_email'] ?? '');
        $lines[] = 'Country: '.($guest['guest_country'] ?? '');
        $lines[] = '';

        $lines[] = 'Payment preference: '.match ($paymentMethod) {
            'pay_now' => 'Pay now (online)',
            'pay_at_hotel' => 'Pay at hotel on arrival',
            default => $paymentMethod,
        };
        $lines[] = 'Submitted via: '.match ($fulfillmentChoice) {
            'direct_pay' => 'Secure online payment',
            'whatsapp' => 'WhatsApp',
            'email' => 'Email',
            default => $fulfillmentChoice,
        };

        if (! empty($guest['total_usd'])) {
            $lines[] = 'Estimated total (rooms): $'.number_format((float) $guest['total_usd'], 2);
        }

        $lines[] = '';
        $lines[] = '— Sent from the hotel website booking cart.';

        return implode("\n", $lines);
    }

    /**
     * @param  array{rooms: list<array<string, mixed>>, experiences: list<array<string, mixed>>}  $cart
     */
    public static function estimateTotalUsd(array $cart): float
    {
        $total = 0.0;
        foreach ($cart['rooms'] ?? [] as $room) {
            $price = (float) ($room['price'] ?? 0);
            $nights = max(1, (int) ($room['nights'] ?? 1));
            $total += $price * $nights;
        }

        return round($total, 2);
    }

    /**
     * @param  array{rooms: list<array<string, mixed>>, experiences: list<array<string, mixed>>}  $cart
     * @return array{check_in: string, check_out: string, room_id: int|null}
     */
    public static function primaryStayFromCart(array $cart): array
    {
        $rooms = $cart['rooms'] ?? [];
        if ($rooms === []) {
            return ['check_in' => now()->toDateString(), 'check_out' => now()->addDay()->toDateString(), 'room_id' => null];
        }

        $first = $rooms[0];
        $checkIn = $first['check_in'] ?? now()->toDateString();
        $checkOut = $first['check_out'] ?? now()->addDay()->toDateString();

        foreach ($rooms as $room) {
            if (! empty($room['check_in']) && $room['check_in'] < $checkIn) {
                $checkIn = $room['check_in'];
            }
            if (! empty($room['check_out']) && $room['check_out'] > $checkOut) {
                $checkOut = $room['check_out'];
            }
        }

        $roomId = isset($first['room_id']) ? (int) $first['room_id'] : null;
        if ($roomId && ! Room::whereKey($roomId)->exists()) {
            $roomId = null;
        }

        return [
            'check_in' => $checkIn,
            'check_out' => $checkOut,
            'room_id' => $roomId,
        ];
    }
}
