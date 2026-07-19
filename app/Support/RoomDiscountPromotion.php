<?php

namespace App\Support;

use App\Models\Room;
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
}
