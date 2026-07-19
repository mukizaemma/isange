<?php

namespace App\Support;

use App\Models\GuestUpdate;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GuestEmailSender
{
    public static function sendOtp(User $user, string $code): bool
    {
        $hotel = self::hotelName();
        $html = view('emails.guest-otp', compact('user', 'code', 'hotel'))->render();

        return self::deliver($user->email, 'Your 4-digit booking discount code', $html, 'guest OTP');
    }

    public static function sendUpdate(User $user, GuestUpdate $update): bool
    {
        if (! $user->marketing_opt_in || ! $user->email_verified_at) {
            return false;
        }

        $hotel = self::hotelName();
        $unsubscribeUrl = route('guest.updates.unsubscribe', $user->marketing_unsubscribe_token);
        $coverUrl = $update->cover_image ? asset('storage/'.$update->cover_image) : null;
        $html = view('emails.guest-update', compact(
            'user',
            'update',
            'hotel',
            'unsubscribeUrl',
            'coverUrl',
        ))->render();

        return self::deliver($user->email, $update->title.' — '.$hotel, $html, 'guest update');
    }

    private static function deliver(string $to, string $subject, string $html, string $kind): bool
    {
        if (! filter_var($to, FILTER_VALIDATE_EMAIL)) {
            return false;
        }

        $apiKey = trim((string) config('services.resend.key', ''));
        $fromAddress = trim((string) config('mail.from.address', ''));
        if ($apiKey === '' || ! filter_var($fromAddress, FILTER_VALIDATE_EMAIL)) {
            Log::warning("{$kind} not sent: Resend or sender configuration is missing.");

            return false;
        }

        $response = Http::withToken($apiKey)
            ->acceptJson()
            ->timeout(15)
            ->post('https://api.resend.com/emails', [
                'from' => self::hotelName().' <'.$fromAddress.'>',
                'to' => [$to],
                'subject' => $subject,
                'html' => $html,
            ]);

        if (! $response->successful()) {
            Log::error("Resend {$kind} failed", [
                'status' => $response->status(),
                'to' => $to,
                'response' => $response->json() ?: $response->body(),
            ]);

            return false;
        }

        return true;
    }

    private static function hotelName(): string
    {
        $setting = Setting::first();

        return trim((string) ($setting->company ?? config('app.name'))) ?: 'Isange Paradise';
    }
}
