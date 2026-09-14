<?php

namespace App\Support;

use App\Models\Room;
use App\Models\Setting;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;

final class RoomDiscountPromotion
{
    /**
     * Highest effective percentage saving among active room discounts.
     * Fixed USD discounts are converted to their percentage of list price.
     */
    public static function maximumPercent(): ?float
    {
        return Cache::remember(FrontendPageCache::ROOM_DISCOUNT_PROMOTION, 120, function (): ?float {
            if (! self::windowIsCurrent()) {
                return null;
            }

            $maximum = Room::query()
                ->where('discount_enabled', true)
                ->whereNotNull('price')
                ->where('price', '>', 0)
                ->get(['price', 'discount_enabled', 'discount_type', 'discount_value'])
                ->filter(fn (Room $room) => $room->hasActiveDiscount())
                ->map(fn (Room $room): float => (float) $room->effectiveDiscountPercent())
                ->max();

            return $maximum !== null && $maximum > 0 ? round((float) $maximum, 1) : null;
        });
    }

    public static function hasActivePromotion(): bool
    {
        return self::maximumPercent() !== null;
    }

    public static function formattedMaximumPercent(): ?string
    {
        $percent = self::maximumPercent();
        if ($percent === null) {
            return null;
        }

        return $percent == floor($percent)
            ? (string) (int) $percent
            : number_format($percent, 1);
    }

    /**
     * Inclusive night window when the hotel-wide promo can apply.
     *
     * @return array{start: Carbon, end: Carbon}|null
     */
    public static function window(): ?array
    {
        $setting = Setting::query()->first();
        $start = DiscountStayAvailability::parseDate($setting?->discount_starts_on);
        $end = DiscountStayAvailability::parseDate($setting?->discount_ends_on);
        if ($start === null || $end === null || $end->lt($start)) {
            return null;
        }

        return ['start' => $start, 'end' => $end];
    }

    /**
     * True when no window is set, or the window has not fully ended.
     */
    public static function windowIsCurrent(): bool
    {
        $window = self::window();
        if ($window === null) {
            return true;
        }

        return $window['end']->gte(now()->startOfDay());
    }

    public static function periodLabel(): ?string
    {
        $window = self::window();
        if ($window === null) {
            return null;
        }

        $start = $window['start'];
        $end = $window['end'];
        if ($start->equalTo($end)) {
            return $start->format('j M Y');
        }
        if ($start->year === $end->year && $start->month === $end->month) {
            return $start->format('j').'–'.$end->format('j M Y');
        }
        if ($start->year === $end->year) {
            return $start->format('j M').' – '.$end->format('j M Y');
        }

        return $start->format('j M Y').' – '.$end->format('j M Y');
    }

    public static function saveWindow(mixed $from, mixed $to): void
    {
        $start = DiscountStayAvailability::parseDate($from);
        $end = DiscountStayAvailability::parseDate($to);
        $setting = Setting::query()->first() ?? Setting::query()->create([]);

        $setting->discount_starts_on = $start?->toDateString();
        $setting->discount_ends_on = $end?->toDateString();
        $setting->save();
        FrontendPageCache::forgetHomePage();
    }

    public static function clearWindow(): void
    {
        self::saveWindow(null, null);
    }

    public static function isNightInWindow(mixed $date): bool
    {
        $parsed = DiscountStayAvailability::parseDate($date);
        if ($parsed === null) {
            return false;
        }

        $window = self::window();
        if ($window === null) {
            return true;
        }

        return $parsed->gte($window['start']) && $parsed->lte($window['end']);
    }
}
