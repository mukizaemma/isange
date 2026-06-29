<?php

namespace App\Support;

use App\Models\GuestBookingRequest;
use App\Models\Setting;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class BookingEmailSender
{
    /**
     * Send the booking request to the hotel inbox via Resend (HTTP API — no local SMTP).
     */
    public static function send(GuestBookingRequest $booking, ?Setting $setting = null): bool
    {
        $apiKey = trim((string) config('services.resend.key', ''));
        if ($apiKey === '') {
            Log::warning('Booking email not sent: RESEND_API_KEY is not configured.');

            return false;
        }

        $setting = $setting ?? Setting::first();
        $hotelName = trim((string) ($setting->company ?? 'Hotel'));
        $to = trim((string) (config('services.booking_notification.to') ?: ($setting->email ?? '')));

        if ($to === '' || ! filter_var($to, FILTER_VALIDATE_EMAIL)) {
            Log::warning('Booking email not sent: hotel notification address is missing or invalid.');

            return false;
        }

        $fromAddress = trim((string) config('mail.from.address', ''));
        $fromName = trim((string) config('mail.from.name', $hotelName));

        if ($fromAddress === '' || ! filter_var($fromAddress, FILTER_VALIDATE_EMAIL)) {
            Log::warning('Booking email not sent: MAIL_FROM_ADDRESS is missing or invalid.');

            return false;
        }

        $payload = [
            'from' => $fromName.' <'.$fromAddress.'>',
            'to' => [$to],
            'subject' => 'Room booking — '.$hotelName,
            'text' => (string) $booking->message_body,
        ];

        $guestEmail = trim((string) ($booking->guest_email ?? ''));
        if ($guestEmail !== '' && filter_var($guestEmail, FILTER_VALIDATE_EMAIL)) {
            $payload['reply_to'] = $guestEmail;
        }

        $cc = trim((string) config('services.booking_notification.cc', ''));
        if ($cc !== '') {
            $ccList = array_values(array_filter(array_map('trim', explode(',', $cc)), static function ($addr) {
                return $addr !== '' && filter_var($addr, FILTER_VALIDATE_EMAIL);
            }));
            if ($ccList !== []) {
                $payload['cc'] = $ccList;
            }
        }

        $response = Http::withToken($apiKey)
            ->acceptJson()
            ->timeout(15)
            ->post('https://api.resend.com/emails', $payload);

        if (! $response->successful()) {
            Log::error('Resend booking email failed', [
                'status' => $response->status(),
                'response' => $response->json(),
                'booking_id' => $booking->public_id,
            ]);

            return false;
        }

        return true;
    }
}
