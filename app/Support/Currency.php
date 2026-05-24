<?php

namespace App\Support;

use App\Models\Setting;

class Currency
{
    public static function rwfPerUsd(?float $override = null): float
    {
        if ($override !== null && $override > 0) {
            return (float) $override;
        }

        $setting = Setting::first();
        $rate = $setting?->usd_to_rwf_rate;

        return $rate > 0 ? (float) $rate : 1300.0;
    }

    public static function usdToRwf(float $usd, ?float $rate = null): float
    {
        return round($usd * self::rwfPerUsd($rate), 0);
    }

    /**
     * USD only — for accommodation (no RWF hover or toggle).
     */
    public static function formatUsdOnly(float|string|null $usd): string
    {
        if ($usd === null || $usd === '') {
            return '';
        }

        $usd = (float) $usd;
        $usdFmt = number_format($usd, $usd == floor($usd) ? 0 : 2);

        return '<span class="price-usd-only">$'.$usdFmt.'</span>';
    }

    /**
     * Primary display: USD. RWF from stored amount if set, otherwise from settings rate.
     * Hover: native tooltip on title. Click/tap/Enter: toggles inline RWF (see dual-currency.js).
     */
    public static function formatUsdWithLocal(float|string|null $usd, float|string|null $rwfStored = null, ?float $rateFallback = null): string
    {
        if ($usd === null || $usd === '') {
            return '';
        }

        $usd = (float) $usd;
        $hasStoredRwf = $rwfStored !== null && $rwfStored !== '' && (float) $rwfStored > 0;
        $rwf = $hasStoredRwf ? (float) $rwfStored : self::usdToRwf($usd, $rateFallback);
        $usdFmt = number_format($usd, $usd == floor($usd) ? 0 : 2);
        $rwfFmt = number_format($rwf, 0, '.', ',');
        $source = $hasStoredRwf
            ? 'Listed in admin as RWF.'
            : 'Approximate RWF from your settings exchange rate.';
        $title = '≈ '.$rwfFmt.' RWF. '.$source.' Click to show or hide next to the dollar price.';

        $titleEsc = htmlspecialchars($title, ENT_QUOTES, 'UTF-8');

        return '<span class="dual-currency js-dual-currency" tabindex="0" role="button" aria-expanded="false" title="'.$titleEsc.'">'
            .'<span class="dual-currency__usd">$'.$usdFmt.'</span>'
            .'<span class="dual-currency__suffix"> · '.$rwfFmt.' RWF</span>'
            .'</span>';
    }

    /**
     * @deprecated Use formatUsdWithLocal($usd, null, $rate) — kept for older call sites.
     */
    public static function formatUsdHover(float|string|null $usd, ?float $rate = null): string
    {
        return self::formatUsdWithLocal($usd, null, $rate);
    }

    /**
     * Plain-text price for select option labels and similar (no HTML).
     */
    public static function formatRoomPriceLabel(float|string|null $usd, float|string|null $rwfStored = null): string
    {
        if ($usd === null || $usd === '') {
            return 'Price on request';
        }

        $usd = (float) $usd;
        $usdFmt = '$'.number_format($usd, $usd == floor($usd) ? 0 : 2);

        return $usdFmt.' / night';
    }

    public static function formatDiningPrice(float|string|null $usd, float|string|null $rwfStored = null, string $currency = 'usd'): string
    {
        if ($usd === null || $usd === '') {
            return '';
        }

        if ($currency === 'rwf') {
            $hasStoredRwf = $rwfStored !== null && $rwfStored !== '' && (float) $rwfStored > 0;
            $rwf = $hasStoredRwf ? (float) $rwfStored : self::usdToRwf((float) $usd);
            $rwfFmt = number_format($rwf, 0, '.', ',');

            return '<span class="dining-price dining-price--rwf">'.$rwfFmt.' RWF</span>';
        }

        $usd = (float) $usd;
        $usdFmt = number_format($usd, $usd == floor($usd) ? 0 : 2);

        return '<span class="dining-price dining-price--usd">$'.$usdFmt.'</span>';
    }
}
