<?php

namespace App\Support;

use App\Models\DiscountClosedDate;
use App\Models\DiscountPeriod;
use App\Models\Room;
use App\Models\Setting;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

final class RoomDiscountPromotion
{
    /** @var Collection<int, DiscountPeriod>|null */
    private static ?Collection $periodsMemo = null;

    /**
     * Highest effective percentage saving among active room discounts.
     * Fixed USD discounts are converted to their percentage of list price.
     * When date-range discounts exist, this is the rate guests see now
     * (the period that includes today, otherwise the next one).
     */
    public static function maximumPercent(): ?float
    {
        return Cache::remember(FrontendPageCache::ROOM_DISCOUNT_PROMOTION, 120, function (): ?float {
            $period = self::displayPeriod();
            if ($period) {
                return self::percentForRate($period->discount_type, (float) $period->discount_value);
            }

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

        return self::formatPercent($percent);
    }

    public static function hasPeriods(): bool
    {
        return self::allPeriods()->isNotEmpty();
    }

    /**
     * @return Collection<int, DiscountPeriod>
     */
    public static function allPeriods(): Collection
    {
        return self::$periodsMemo ??= DiscountPeriod::query()->orderBy('starts_on')->orderBy('id')->get();
    }

    /**
     * Periods that have not finished yet.
     *
     * @return Collection<int, DiscountPeriod>
     */
    public static function activePeriods(): Collection
    {
        $today = now()->toDateString();

        return self::allPeriods()
            ->filter(fn (DiscountPeriod $period) => $period->ends_on->toDateString() >= $today)
            ->values();
    }

    /**
     * The rate to advertise when the guest has not chosen dates yet.
     */
    public static function displayPeriod(): ?DiscountPeriod
    {
        $today = now()->toDateString();
        $current = self::allPeriods()->first(
            fn (DiscountPeriod $period) => $period->starts_on->toDateString() <= $today
                && $period->ends_on->toDateString() >= $today
        );
        if ($current) {
            return $current;
        }

        return self::activePeriods()->first(
            fn (DiscountPeriod $period) => $period->starts_on->toDateString() >= $today
        );
    }

    /**
     * @return array{type: string, value: float}|null
     */
    public static function displayRate(): ?array
    {
        $period = self::displayPeriod();
        if (! $period) {
            return null;
        }

        return [
            'type' => (string) $period->discount_type,
            'value' => (float) $period->discount_value,
        ];
    }

    public static function periodContaining(mixed $date): ?DiscountPeriod
    {
        $parsed = DiscountStayAvailability::parseDate($date);
        if ($parsed === null) {
            return null;
        }

        $key = $parsed->toDateString();

        return self::allPeriods()->first(
            fn (DiscountPeriod $period) => $period->starts_on->toDateString() <= $key
                && $period->ends_on->toDateString() >= $key
        );
    }

    /**
     * The one period that contains every night of the stay.
     */
    public static function coveringPeriod(mixed $checkIn, mixed $checkOut): ?DiscountPeriod
    {
        $nights = DiscountStayAvailability::nights($checkIn, $checkOut);
        if ($nights === []) {
            return null;
        }

        $match = null;
        foreach ($nights as $night) {
            $period = self::periodContaining($night);
            if (! $period) {
                return null;
            }
            if ($match && $match->id !== $period->id) {
                return null;
            }
            $match = $period;
        }

        return $match;
    }

    /**
     * Inclusive night window when the hotel-wide promo can apply.
     * With several periods, this is the one guests see first.
     *
     * @return array{start: Carbon, end: Carbon}|null
     */
    public static function window(): ?array
    {
        $period = self::displayPeriod() ?? self::activePeriods()->first() ?? self::allPeriods()->first();
        if ($period) {
            $start = DiscountStayAvailability::parseDate($period->starts_on);
            $end = DiscountStayAvailability::parseDate($period->ends_on);
            if ($start && $end) {
                return ['start' => $start, 'end' => $end];
            }
        }

        return self::legacyWindow();
    }

    /**
     * True when no window is set, or a promo period has not fully ended.
     */
    public static function windowIsCurrent(): bool
    {
        if (self::hasPeriods()) {
            return self::activePeriods()->isNotEmpty();
        }

        $window = self::legacyWindow();
        if ($window === null) {
            return true;
        }

        return $window['end']->gte(now()->startOfDay());
    }

    public static function periodLabel(): ?string
    {
        $periods = self::activePeriods();
        if ($periods->isEmpty()) {
            $periods = self::allPeriods();
        }
        if ($periods->isEmpty()) {
            $window = self::legacyWindow();
            if ($window === null) {
                return null;
            }

            return self::rangeLabel($window['start'], $window['end']);
        }

        $labels = $periods->map(function (DiscountPeriod $period) use ($periods) {
            $start = DiscountStayAvailability::parseDate($period->starts_on);
            $end = DiscountStayAvailability::parseDate($period->ends_on);
            if (! $start || ! $end) {
                return null;
            }
            $label = self::rangeLabel($start, $end);
            if ($periods->count() > 1) {
                $rate = self::ratePhrase($period->discount_type, (float) $period->discount_value);
                if ($rate !== '') {
                    $label .= ' ('.$rate.')';
                }
            }

            return $label;
        })->filter()->values();

        if ($labels->isEmpty()) {
            return null;
        }
        if ($labels->count() === 1) {
            return $labels->first();
        }

        $last = $labels->pop();

        return $labels->implode(', ').' and '.$last;
    }

    /**
     * Open nights from today through the last saved period, skipping nights
     * the hotel has marked full.
     */
    public static function openRangeLabel(): ?string
    {
        $periods = self::activePeriods();
        if ($periods->isEmpty()) {
            return null;
        }

        $today = now()->startOfDay();
        $endDate = $periods
            ->map(fn (DiscountPeriod $period) => DiscountStayAvailability::parseDate($period->ends_on))
            ->filter()
            ->sortBy(fn (Carbon $date) => $date->toDateString())
            ->last();
        if (! $endDate || $endDate->lt($today)) {
            return null;
        }

        $closed = DiscountClosedDate::query()
            ->whereDate('closed_on', '>=', $today->toDateString())
            ->whereDate('closed_on', '<=', $endDate->toDateString())
            ->pluck('closed_on')
            ->map(fn ($date) => Carbon::parse($date)->toDateString())
            ->flip();

        $ranges = [];
        $start = null;
        $prev = null;
        for ($day = $today->copy(); $day->lte($endDate); $day->addDay()) {
            $key = $day->toDateString();
            $open = self::periodContaining($key) !== null && ! isset($closed[$key]);
            if ($open) {
                $start ??= $day->copy();
                $prev = $day->copy();
            } elseif ($start !== null && $prev !== null) {
                $ranges[] = [$start, $prev];
                $start = null;
                $prev = null;
            }
        }
        if ($start !== null && $prev !== null) {
            $ranges[] = [$start, $prev];
        }
        if ($ranges === []) {
            return null;
        }

        $labels = array_map(function (array $range) {
            [$from, $to] = $range;

            return self::rangeLabel($from, $to);
        }, $ranges);

        if (count($labels) === 1) {
            return $labels[0];
        }

        $last = array_pop($labels);

        return implode(', ', $labels).' and '.$last;
    }

    /**
     * Add a date-range discount without removing the others.
     * A single existing period can be moved by saving a new range over it.
     *
     * @return array{ok: bool, created: bool, message: string}
     */
    public static function addPeriod(mixed $from, mixed $to, string $type, float $value): array
    {
        $start = DiscountStayAvailability::parseDate($from);
        $end = DiscountStayAvailability::parseDate($to);
        if ($start === null || $end === null || $end->lt($start)) {
            return ['ok' => false, 'created' => false, 'message' => 'Choose a valid start and end date.'];
        }

        $overlaps = self::allPeriods()->filter(function (DiscountPeriod $period) use ($start, $end) {
            return $period->starts_on->toDateString() <= $end->toDateString()
                && $period->ends_on->toDateString() >= $start->toDateString();
        })->values();

        if ($overlaps->count() === 1 && self::allPeriods()->count() === 1) {
            $period = $overlaps->first();
            $period->update([
                'starts_on' => $start->toDateString(),
                'ends_on' => $end->toDateString(),
                'discount_type' => $type,
                'discount_value' => $value,
            ]);
            self::forget();
            self::mirrorEarliestPeriod();

            return ['ok' => true, 'created' => false, 'message' => 'Discount updated for '.$start->toDateString().' to '.$end->toDateString().'.'];
        }

        if ($overlaps->isNotEmpty()) {
            $period = $overlaps->first();
            $label = $period->starts_on->format('j M Y').' to '.$period->ends_on->format('j M Y');

            return [
                'ok' => false,
                'created' => false,
                'message' => 'Those dates overlap a discount already set for '.$label.'. Choose dates that do not overlap, or remove that period first.',
            ];
        }

        DiscountPeriod::query()->create([
            'starts_on' => $start->toDateString(),
            'ends_on' => $end->toDateString(),
            'discount_type' => $type,
            'discount_value' => $value,
        ]);
        self::forget();
        self::mirrorEarliestPeriod();
        $kept = self::allPeriods()->count() > 1;
        $span = $start->toDateString().' to '.$end->toDateString();

        return [
            'ok' => true,
            'created' => true,
            'message' => $kept
                ? 'Discount added for '.$span.'. Other discount periods were kept.'
                : 'Discount saved for '.$span.'.',
        ];
    }

    public static function saveWindow(mixed $from, mixed $to): void
    {
        $start = DiscountStayAvailability::parseDate($from);
        $end = DiscountStayAvailability::parseDate($to);
        self::writeLegacyWindow($start?->toDateString(), $end?->toDateString());
        self::forget();
    }

    public static function clearWindow(): void
    {
        DiscountPeriod::query()->delete();
        self::writeLegacyWindow(null, null);
        self::forget();
    }

    public static function mirrorEarliestPeriod(): void
    {
        $period = self::activePeriods()->first() ?? self::allPeriods()->sortBy(fn (DiscountPeriod $item) => $item->starts_on->toDateString())->first();
        self::writeLegacyWindow(
            $period?->starts_on?->toDateString(),
            $period?->ends_on?->toDateString()
        );
    }

    public static function isNightInWindow(mixed $date): bool
    {
        $parsed = DiscountStayAvailability::parseDate($date);
        if ($parsed === null) {
            return false;
        }

        if (self::hasPeriods()) {
            return self::periodContaining($parsed) !== null;
        }

        $window = self::legacyWindow();
        if ($window === null) {
            return true;
        }

        return $parsed->gte($window['start']) && $parsed->lte($window['end']);
    }

    public static function forget(): void
    {
        self::$periodsMemo = null;
        FrontendPageCache::forgetHomePage();
    }

    public static function percentForRate(string $type, float $value): ?float
    {
        if ($value <= 0) {
            return null;
        }

        if ($type === Room::DISCOUNT_PERCENT) {
            return $value <= 100 ? round($value, 1) : null;
        }

        if ($type !== Room::DISCOUNT_FIXED) {
            return null;
        }

        $maximum = Room::query()
            ->whereNotNull('price')
            ->where('price', '>', $value)
            ->get(['price'])
            ->map(fn (Room $room) => round(($value / (float) $room->price) * 100, 1))
            ->max();

        return $maximum !== null && $maximum > 0 ? (float) $maximum : null;
    }

    public static function ratePhrase(string $type, float $value): string
    {
        if ($type === Room::DISCOUNT_PERCENT && $value > 0) {
            return self::formatPercent($value).'% off';
        }

        if ($type === Room::DISCOUNT_FIXED && $value > 0) {
            $fmt = $value == floor($value) ? number_format($value, 0) : number_format($value, 2);

            return '$'.$fmt.' off';
        }

        return '';
    }

    /**
     * @return array{start: Carbon, end: Carbon}|null
     */
    private static function legacyWindow(): ?array
    {
        $setting = Setting::query()->first();
        $start = DiscountStayAvailability::parseDate($setting?->discount_starts_on);
        $end = DiscountStayAvailability::parseDate($setting?->discount_ends_on);
        if ($start === null || $end === null || $end->lt($start)) {
            return null;
        }

        return ['start' => $start, 'end' => $end];
    }

    private static function writeLegacyWindow(?string $from, ?string $to): void
    {
        $setting = Setting::query()->first() ?? Setting::query()->create([]);
        $setting->discount_starts_on = $from;
        $setting->discount_ends_on = $to;
        $setting->save();
    }

    private static function rangeLabel(Carbon $start, Carbon $end): string
    {
        if ($start->equalTo($end)) {
            return 'from '.self::formatPromoDay($start, true);
        }

        $startHasYear = $start->year !== $end->year;

        return 'from '.self::formatPromoDay($start, $startHasYear).' to '.self::formatPromoDay($end, true);
    }

    private static function formatPromoDay(Carbon $date, bool $withYear): string
    {
        $month = $date->format('M') === 'Sep' ? 'Sept' : $date->format('M');

        return $date->format('j').' '.$month.($withYear ? ' '.$date->format('Y') : '');
    }

    private static function formatPercent(float $percent): string
    {
        return $percent == floor($percent)
            ? (string) (int) $percent
            : number_format($percent, 1);
    }
}
