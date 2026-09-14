<?php

namespace App\Support;

use App\Models\DiscountClosedDate;
use App\Models\GuestBookingRequest;
use Carbon\Carbon;
use Carbon\CarbonInterface;

final class DiscountStayAvailability
{
    /**
     * Occupied nights of a stay (check-in inclusive, check-out exclusive).
     *
     * @return list<string>
     */
    public static function nights(mixed $checkIn, mixed $checkOut): array
    {
        $in = self::parseDate($checkIn);
        $out = self::parseDate($checkOut);
        if ($in === null || $out === null || ! $out->greaterThan($in)) {
            return [];
        }

        $nights = [];
        for ($day = $in->copy(); $day->lt($out); $day->addDay()) {
            $nights[] = $day->toDateString();
        }

        return $nights;
    }

    public static function hasValidStay(mixed $checkIn, mixed $checkOut): bool
    {
        return self::nights($checkIn, $checkOut) !== [];
    }

    /**
     * Promo applies only when every night of the stay is still open.
     */
    public static function isOpenForStay(mixed $checkIn, mixed $checkOut): bool
    {
        $nights = self::nights($checkIn, $checkOut);
        if ($nights === []) {
            return false;
        }

        return ! DiscountClosedDate::query()->whereIn('closed_on', $nights)->exists();
    }

    /**
     * @return list<string>
     */
    public static function closedNightsInStay(mixed $checkIn, mixed $checkOut): array
    {
        $nights = self::nights($checkIn, $checkOut);
        if ($nights === []) {
            return [];
        }

        return DiscountClosedDate::query()
            ->whereIn('closed_on', $nights)
            ->orderBy('closed_on')
            ->pluck('closed_on')
            ->map(fn ($date) => Carbon::parse($date)->toDateString())
            ->values()
            ->all();
    }

    public static function isNightClosed(mixed $date): bool
    {
        $parsed = self::parseDate($date);
        if ($parsed === null) {
            return false;
        }

        return DiscountClosedDate::query()->whereDate('closed_on', $parsed->toDateString())->exists();
    }

    public static function toggleNight(mixed $date): bool
    {
        $parsed = self::parseDate($date);
        if ($parsed === null) {
            return false;
        }

        $existing = DiscountClosedDate::query()->whereDate('closed_on', $parsed->toDateString())->first();
        if ($existing) {
            $existing->delete();

            return true;
        }

        DiscountClosedDate::query()->create(['closed_on' => $parsed->toDateString()]);

        return true;
    }

    /**
     * @param  list<string>|iterable<int, mixed>  $dates
     */
    public static function closeDates(iterable $dates): int
    {
        $updated = 0;
        foreach ($dates as $date) {
            $parsed = self::parseDate($date);
            if ($parsed === null) {
                continue;
            }
            DiscountClosedDate::query()->firstOrCreate(['closed_on' => $parsed->toDateString()]);
            $updated++;
        }

        return $updated;
    }

    /**
     * @param  list<string>|iterable<int, mixed>  $dates
     */
    public static function openDates(iterable $dates): int
    {
        $keys = [];
        foreach ($dates as $date) {
            $parsed = self::parseDate($date);
            if ($parsed === null) {
                continue;
            }
            $keys[] = $parsed->toDateString();
        }
        if ($keys === []) {
            return 0;
        }

        return DiscountClosedDate::query()->whereIn('closed_on', $keys)->delete();
    }

    /**
     * Pending + confirmed bookings overlapping each night in the range (inclusive).
     *
     * @return array<string, int>
     */
    public static function occupancyByNight(CarbonInterface $from, CarbonInterface $to): array
    {
        $start = $from->copy()->startOfDay();
        $end = $to->copy()->startOfDay();
        if ($end->lt($start)) {
            return [];
        }

        $counts = [];
        for ($day = $start->copy(); $day->lte($end); $day->addDay()) {
            $counts[$day->toDateString()] = 0;
        }

        $bookings = GuestBookingRequest::query()
            ->whereIn('status', [
                GuestBookingRequest::STATUS_PENDING,
                GuestBookingRequest::STATUS_CONFIRMED,
            ])
            ->whereDate('check_in', '<=', $end->toDateString())
            ->whereDate('check_out', '>', $start->toDateString())
            ->get(['check_in', 'check_out']);

        foreach ($bookings as $booking) {
            foreach (self::nights($booking->check_in, $booking->check_out) as $night) {
                if (isset($counts[$night])) {
                    $counts[$night]++;
                }
            }
        }

        return $counts;
    }

    /**
     * @return list<string>
     */
    public static function dateRange(mixed $from, mixed $to, int $maxDays = 366): array
    {
        $nights = self::nights($from, Carbon::parse((string) $to)->addDay()->toDateString());
        if (count($nights) > $maxDays) {
            return array_slice($nights, 0, $maxDays);
        }

        return $nights;
    }

    public static function parseDate(mixed $value): ?Carbon
    {
        if ($value instanceof CarbonInterface) {
            return Carbon::parse($value->toDateString())->startOfDay();
        }

        $raw = is_string($value) ? trim($value) : '';
        if ($raw === '' || ! preg_match('/^\d{4}-\d{2}-\d{2}/', $raw)) {
            return null;
        }

        try {
            return Carbon::parse(substr($raw, 0, 10))->startOfDay();
        } catch (\Throwable) {
            return null;
        }
    }
}
