<?php

namespace App\Support;

use App\Models\Setting;

class BookingEngine
{
    public static function url(?Setting $setting): ?string
    {
        $url = trim((string) ($setting->booking_engine_url ?? ''));

        if ($url === '') {
            return null;
        }

        if (! preg_match('#^https?://#i', $url)) {
            $url = 'https://'.$url;
        }

        return $url;
    }

    public static function isConfigured(?Setting $setting): bool
    {
        return self::url($setting) !== null;
    }
}
