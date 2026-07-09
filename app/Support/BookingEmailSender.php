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
        return self::sendHotelNotification($booking, $setting);
    }

    /**
     * On email-channel submit: notify hotel and acknowledge the guest.
     *
     * @return array{hotel: bool, guest: bool}
     */
    public static function sendOnSubmit(GuestBookingRequest $booking, ?Setting $setting = null): array
    {
        $hotel = self::sendHotelNotification($booking, $setting);
        $guest = self::sendGuestAcknowledgement($booking, $setting);

        return ['hotel' => $hotel, 'guest' => $guest];
    }

    public static function sendHotelNotification(GuestBookingRequest $booking, ?Setting $setting = null): bool
    {
        $setting = $setting ?? Setting::first();
        $to = trim((string) (config('services.booking_notification.to') ?: ($setting->email ?? '')));

        if ($to === '' || ! filter_var($to, FILTER_VALIDATE_EMAIL)) {
            Log::warning('Booking hotel email not sent: notification address is missing or invalid.');

            return false;
        }

        $payload = [
            'subject' => 'Room booking — '.self::guestLabel($booking),
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

        return self::deliver($to, $payload, 'hotel notification', $booking);
    }

    public static function sendGuestAcknowledgement(GuestBookingRequest $booking, ?Setting $setting = null): bool
    {
        $guestEmail = trim((string) ($booking->guest_email ?? ''));
        if ($guestEmail === '' || ! filter_var($guestEmail, FILTER_VALIDATE_EMAIL)) {
            Log::warning('Guest acknowledgement not sent: guest email is missing or invalid.', [
                'booking_id' => $booking->public_id,
            ]);

            return false;
        }

        $setting = $setting ?? Setting::first();
        $hotelName = self::resolveHotelName($setting);
        $hotelEmail = trim((string) ($setting->email ?? ''));

        $lines = [];
        $lines[] = 'Dear '.($booking->guest_name ?: 'Guest').',';
        $lines[] = '';
        $lines[] = 'Thank you for your reservation request at '.$hotelName.'.';
        $lines[] = 'We have received your booking and our team will review it shortly.';
        $lines[] = 'You will receive a confirmation email once your reservation is approved.';
        $lines[] = '';
        $lines[] = self::buildStaySummary($booking);
        $lines[] = '';
        $lines[] = 'Reference: '.$booking->public_id;
        $lines[] = '';
        if ($hotelEmail !== '') {
            $lines[] = 'If you have questions, reply to this email or contact us at '.$hotelEmail.'.';
        }
        $lines[] = '';
        $lines[] = '— '.$hotelName;

        $payload = [
            'subject' => 'We received your reservation — '.$hotelName,
            'text' => implode("\n", $lines),
        ];

        return self::deliver($guestEmail, $payload, 'guest acknowledgement', $booking);
    }

    public static function sendGuestConfirmation(GuestBookingRequest $booking, ?Setting $setting = null): bool
    {
        return self::sendGuestStatusUpdate($booking, GuestBookingRequest::STATUS_CONFIRMED, $setting);
    }

    public static function sendGuestStatusUpdate(GuestBookingRequest $booking, string $status, ?Setting $setting = null): bool
    {
        if ($booking->fulfillment_choice !== 'email') {
            return false;
        }

        if (! in_array($status, GuestBookingRequest::REVIEWABLE_STATUSES, true)) {
            return false;
        }

        $guestEmail = trim((string) ($booking->guest_email ?? ''));
        if ($guestEmail === '' || ! filter_var($guestEmail, FILTER_VALIDATE_EMAIL)) {
            Log::warning('Guest status email not sent: guest email is missing or invalid.', [
                'booking_id' => $booking->public_id,
                'status' => $status,
            ]);

            return false;
        }

        $setting = $setting ?? Setting::first();
        $hotelName = self::resolveHotelName($setting);
        $content = self::buildGuestStatusContent($booking, $status, $setting);

        $payload = [
            'subject' => $content['subject'].' — '.$hotelName,
            'text' => $content['body'],
        ];

        $kind = match ($status) {
            GuestBookingRequest::STATUS_CONFIRMED => 'guest confirmation',
            GuestBookingRequest::STATUS_UNFORTUNATE => 'guest unfortunate',
            GuestBookingRequest::STATUS_REJECTED => 'guest rejection',
            GuestBookingRequest::STATUS_NO_SHOW => 'guest no-show',
            default => 'guest status update',
        };

        return self::deliver($guestEmail, $payload, $kind, $booking);
    }

    /**
     * @return array{subject: string, body: string}
     */
    private static function buildGuestStatusContent(GuestBookingRequest $booking, string $status, ?Setting $setting): array
    {
        $setting = $setting ?? Setting::first();
        $hotelName = self::resolveHotelName($setting);
        $hotelEmail = trim((string) ($setting->email ?? ''));
        $hotelPhone = trim((string) ($setting->phone ?? ''));
        $guestName = $booking->guest_name ?: 'Guest';

        $lines = [];
        $lines[] = 'Dear '.$guestName.',';
        $lines[] = '';

        $subject = match ($status) {
            GuestBookingRequest::STATUS_CONFIRMED => 'Your reservation is confirmed',
            GuestBookingRequest::STATUS_UNFORTUNATE => 'Update on your reservation request',
            GuestBookingRequest::STATUS_REJECTED => 'Action needed on your reservation request',
            GuestBookingRequest::STATUS_NO_SHOW => 'Reservation marked as no-show',
            default => 'Update on your reservation',
        };

        match ($status) {
            GuestBookingRequest::STATUS_CONFIRMED => self::appendConfirmedBody($lines, $hotelName),
            GuestBookingRequest::STATUS_UNFORTUNATE => self::appendUnfortunateBody($lines, $hotelName),
            GuestBookingRequest::STATUS_REJECTED => self::appendRejectedBody($lines, $hotelName),
            GuestBookingRequest::STATUS_NO_SHOW => self::appendNoShowBody($lines, $hotelName),
            default => $lines[] = 'There is an update regarding your reservation.',
        };

        $lines[] = '';
        $lines[] = self::buildStaySummary($booking);
        $lines[] = '';
        $lines[] = 'Reference: '.$booking->public_id;
        $lines[] = '';

        if ($status === GuestBookingRequest::STATUS_CONFIRMED) {
            $lines[] = 'Payment: Pay at hotel on arrival.';
            $lines[] = '';
        }

        if ($hotelEmail !== '' || $hotelPhone !== '') {
            $lines[] = 'Contact us:';
            if ($hotelEmail !== '') {
                $lines[] = 'Email: '.$hotelEmail;
            }
            if ($hotelPhone !== '') {
                $lines[] = 'Phone / WhatsApp: '.$hotelPhone;
            }
            $lines[] = '';
        }

        if ($status === GuestBookingRequest::STATUS_CONFIRMED) {
            $lines[] = 'We look forward to welcoming you.';
        } elseif ($status === GuestBookingRequest::STATUS_UNFORTUNATE) {
            $lines[] = 'We hope to welcome you on another occasion.';
        } elseif ($status === GuestBookingRequest::STATUS_REJECTED) {
            $lines[] = 'We look forward to hearing from you.';
        } elseif ($status === GuestBookingRequest::STATUS_NO_SHOW) {
            $lines[] = 'If you would like to rebook, please get in touch.';
        }

        $lines[] = '';
        $lines[] = '— '.$hotelName;

        return [
            'subject' => $subject,
            'body' => implode("\n", $lines),
        ];
    }

    /**
     * @param  list<string>  $lines
     */
    private static function appendConfirmedBody(array &$lines, string $hotelName): void
    {
        $lines[] = 'Great news — your reservation at '.$hotelName.' is confirmed!';
    }

    /**
     * @param  list<string>  $lines
     */
    private static function appendUnfortunateBody(array &$lines, string $hotelName): void
    {
        $lines[] = 'Thank you for choosing '.$hotelName.'.';
        $lines[] = 'Unfortunately, we are unable to confirm your reservation for the requested dates.';
        $lines[] = 'This may be due to availability or other constraints beyond our control.';
        $lines[] = 'We sincerely apologise for any inconvenience.';
    }

    /**
     * @param  list<string>  $lines
     */
    private static function appendRejectedBody(array &$lines, string $hotelName): void
    {
        $lines[] = 'Thank you for your interest in '.$hotelName.'.';
        $lines[] = 'We were unable to process your reservation because some details in your request were missing or unclear.';
        $lines[] = 'Please contact us with complete booking information (dates, room preference, and guest details) so we can assist you.';
    }

    /**
     * @param  list<string>  $lines
     */
    private static function appendNoShowBody(array &$lines, string $hotelName): void
    {
        $lines[] = 'Our records show that you did not arrive for your reservation at '.$hotelName.'.';
        $lines[] = 'Your booking has been marked as a no-show.';
        $lines[] = 'If this is incorrect or you experienced difficulties, please let us know.';
    }

    /**
     * @param  array<string, mixed>  $payload
     */
    private static function deliver(string $to, array $payload, string $kind, GuestBookingRequest $booking): bool
    {
        $apiKey = trim((string) config('services.resend.key', ''));
        if ($apiKey === '') {
            Log::warning("Booking {$kind} not sent: RESEND_API_KEY is not configured.");

            return false;
        }

        $setting = Setting::first();
        $hotelName = self::resolveHotelName($setting);
        $fromAddress = trim((string) config('mail.from.address', ''));
        $fromName = trim((string) config('mail.from.name', '')) ?: $hotelName;

        if ($fromAddress === '' || ! filter_var($fromAddress, FILTER_VALIDATE_EMAIL)) {
            Log::warning("Booking {$kind} not sent: MAIL_FROM_ADDRESS is missing or invalid.");

            return false;
        }

        $request = array_merge([
            'from' => $fromName.' <'.$fromAddress.'>',
            'to' => [$to],
        ], $payload);

        $response = Http::withToken($apiKey)
            ->acceptJson()
            ->timeout(15)
            ->post('https://api.resend.com/emails', $request);

        if (! $response->successful()) {
            $body = $response->json();
            $message = is_array($body) ? ($body['message'] ?? $body['error'] ?? json_encode($body)) : $response->body();

            Log::error("Resend booking {$kind} failed", [
                'status' => $response->status(),
                'message' => $message,
                'response' => $body,
                'booking_id' => $booking->public_id,
                'from' => $fromAddress,
                'to' => $to,
            ]);

            return false;
        }

        return true;
    }

    private static function buildStaySummary(GuestBookingRequest $booking): string
    {
        $lines = [];
        $lines[] = 'Stay details:';
        $lines[] = 'Check-in: '.$booking->check_in->format('Y-m-d');
        $lines[] = 'Check-out: '.$booking->check_out->format('Y-m-d');

        if ($booking->room) {
            $lines[] = 'Room: '.$booking->room->roomName;
        }

        $cart = $booking->cart_items;
        if (is_array($cart)) {
            $rooms = $cart['rooms'] ?? [];
            if (count($rooms) > 1) {
                $lines[] = 'Rooms ('.count($rooms).'):';
                foreach ($rooms as $i => $room) {
                    $n = $i + 1;
                    $name = $room['name'] ?? 'Room';
                    $dates = '';
                    if (! empty($room['check_in']) && ! empty($room['check_out'])) {
                        $dates = ' ('.$room['check_in'].' → '.$room['check_out'].')';
                    }
                    $lines[] = '  '.$n.'. '.$name.$dates;
                }
            }

            $experiences = $cart['experiences'] ?? [];
            if ($experiences !== []) {
                $lines[] = 'Experiences:';
                foreach ($experiences as $exp) {
                    $lines[] = '  • '.($exp['title'] ?? 'Experience');
                }
            }
        }

        if ($booking->airport_pickup || $booking->airport_dropoff) {
            $lines[] = 'Airport pickup: '.($booking->airport_pickup ? 'Yes' : 'No');
            $lines[] = 'Airport drop-off: '.($booking->airport_dropoff ? 'Yes' : 'No');
        }

        if ($booking->additional_requests) {
            $lines[] = 'Special requests: '.$booking->additional_requests;
        }

        if ($booking->total_usd) {
            $lines[] = 'Estimated total: $'.number_format((float) $booking->total_usd, 2);
        }

        return implode("\n", $lines);
    }

    private static function resolveHotelName(?Setting $setting = null): string
    {
        $setting = $setting ?? Setting::first();
        $candidates = [
            trim((string) ($setting->company ?? '')),
            trim((string) config('mail.from.name', '')),
            trim((string) config('app.name', '')),
        ];

        foreach ($candidates as $name) {
            if ($name !== '' && strcasecmp($name, 'Company Name') !== 0) {
                return $name;
            }
        }

        return 'Hotel';
    }

    private static function guestLabel(GuestBookingRequest $booking): string
    {
        $name = trim((string) ($booking->guest_name ?? ''));
        $ref = trim((string) ($booking->public_id ?? ''));

        if ($name !== '') {
            return $ref !== '' ? $name.' ('.$ref.')' : $name;
        }

        $email = trim((string) ($booking->guest_email ?? ''));
        if ($email !== '') {
            return $ref !== '' ? $email.' ('.$ref.')' : $email;
        }

        return $ref !== '' ? 'Guest ('.$ref.')' : 'Guest';
    }
}
