<?php

namespace App\Console\Commands;

use App\Models\Setting;
use App\Support\BookingEmailSender;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class DiagnoseBookingEmail extends Command
{
    protected $signature = 'booking:diagnose-email {--send : Send a test booking email}';

    protected $description = 'Check Resend configuration for booking emails (hotel, guest ack, status updates)';

    public function handle(): int
    {
        $setting = Setting::first();
        $apiKey = trim((string) config('services.resend.key', ''));
        $from = trim((string) config('mail.from.address', ''));
        $to = trim((string) (config('services.booking_notification.to') ?: ($setting->email ?? '')));

        $this->info('Booking email configuration');
        $this->line('Email flow: submit (hotel + guest ack) → admin review (guest status email, email channel only)');
        $this->newLine();
        $this->table(['Setting', 'Value', 'OK?'], [
            ['RESEND_API_KEY', $apiKey !== '' ? substr($apiKey, 0, 8).'…' : '(empty)', $apiKey !== '' ? 'yes' : 'NO'],
            ['MAIL_FROM_ADDRESS', $from ?: '(empty)', filter_var($from, FILTER_VALIDATE_EMAIL) ? 'yes' : 'NO'],
            ['MAIL_NOTIFICATION_TO', $to ?: '(empty)', filter_var($to, FILTER_VALIDATE_EMAIL) ? 'yes' : 'NO'],
            ['Admin setting email', $setting->email ?? '(empty)', 'info'],
        ]);

        if ($apiKey === '') {
            $this->error('Add RESEND_API_KEY to .env on this server, then run: php artisan config:clear');

            return self::FAILURE;
        }

        $this->newLine();
        $this->info('Testing connection to Resend API…');
        $probe = Http::withToken($apiKey)
            ->acceptJson()
            ->timeout(15)
            ->get('https://api.resend.com/domains');

        if ($probe->status() === 401 || $probe->status() === 403) {
            $this->error('Resend rejected the API key (HTTP '.$probe->status().'). Check RESEND_API_KEY.');

            return self::FAILURE;
        }

        if (! $probe->successful()) {
            $this->warn('Could not list domains (HTTP '.$probe->status().'). Outbound HTTPS may be blocked on this host.');
            $this->line(json_encode($probe->json(), JSON_PRETTY_PRINT));
        } else {
            $domains = collect($probe->json('data') ?? [])->pluck('name')->filter()->values();
            if ($domains->isEmpty()) {
                $this->warn('No verified domains in Resend. Add isangeparadiseresort.com in the Resend dashboard.');
            } else {
                $this->info('Verified domains in Resend: '.$domains->implode(', '));
                $fromDomain = substr(strrchr($from, '@') ?: '', 1);
                if ($fromDomain && ! $domains->contains($fromDomain)) {
                    $this->error("MAIL_FROM_ADDRESS uses @{$fromDomain} but that domain is not verified in Resend.");
                }
            }
        }

        if (! $this->option('send')) {
            $this->newLine();
            $this->line('Run with --send to deliver a test message to MAIL_NOTIFICATION_TO.');

            return self::SUCCESS;
        }

        if (! filter_var($to, FILTER_VALIDATE_EMAIL)) {
            $this->error('MAIL_NOTIFICATION_TO (or admin email) is invalid.');

            return self::FAILURE;
        }

        $response = Http::withToken($apiKey)
            ->acceptJson()
            ->timeout(15)
            ->post('https://api.resend.com/emails', [
                'from' => config('mail.from.name').' <'.$from.'>',
                'to' => [$to],
                'subject' => 'Test booking email — '.config('app.name'),
                'text' => "This is a test from php artisan booking:diagnose-email\nTime: ".now()->toIso8601String(),
            ]);

        if ($response->successful()) {
            $this->info('Test email sent to '.$to.' (id: '.$response->json('id').')');

            return self::SUCCESS;
        }

        $this->error('Send failed (HTTP '.$response->status().')');
        $this->line(json_encode($response->json(), JSON_PRETTY_PRINT));

        return self::FAILURE;
    }
}
