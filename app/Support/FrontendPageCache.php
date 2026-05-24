<?php

namespace App\Support;

use Illuminate\Support\Facades\Cache;

/**
 * Cache keys for public pages. Cleared from model hooks when related content changes.
 */
final class FrontendPageCache
{
    public const HOME_PAGE_DATA = 'frontend.home_page_data_v1';

    public const DINING_MENU_COLUMNS = 'frontend.dining_menu_columns_v1';

    public const FOOTER_FACILITIES = 'frontend.footer_facilities_v1';

    public static function forgetHomePage(): void
    {
        Cache::forget(self::HOME_PAGE_DATA);
    }

    public static function forgetDiningMenu(): void
    {
        Cache::forget(self::DINING_MENU_COLUMNS);
    }

    public static function forgetFooterFacilities(): void
    {
        Cache::forget(self::FOOTER_FACILITIES);
    }

    public static function forgetDiningAndHome(): void
    {
        self::forgetDiningMenu();
        self::forgetHomePage();
    }
}
