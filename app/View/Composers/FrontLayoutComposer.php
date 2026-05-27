<?php

namespace App\View\Composers;

use App\Models\About;
use App\Models\Facility;
use App\Models\PageHeader;
use App\Models\Partner;
use App\Models\Setting;
use App\Support\FrontendPageCache;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\View;

class FrontLayoutComposer
{
    public const CACHE_KEY_SETTING = 'front_layout.setting';

    public const CACHE_KEY_ABOUT = 'front_layout.about';

    public function compose(View $view): void
    {
        $data = $view->getData();

        $setting = $data['setting'] ?? null;
        if ($setting === null) {
            $setting = Cache::remember(self::CACHE_KEY_SETTING, 300, fn () => Setting::query()->first());
        }
        if ($setting === null) {
            $setting = (object) [
                'title' => '',
                'keywords' => '',
                'company' => config('app.name', ''),
                'address' => '',
                'phone' => '',
                'phone1' => '',
                'phone2' => '',
                'email' => '',
                'facebook' => '',
                'instagram' => '',
                'twitter' => '',
                'youtube' => '',
                'tiktok' => '',
                'linkedin' => '',
                'reserveUrl' => '',
                'logo' => '',
                'usd_to_rwf_rate' => 1300,
                'facilities_hero_image' => '',
                'facilities_intro' => '',
                'dining_hero_image' => '',
                'dining_intro' => '',
                'flexible_stay_bg_image' => '',
                'flexible_stay_heading' => '',
                'flexible_stay_subheading' => '',
                'flexible_stay_card1_title' => '',
                'flexible_stay_card1_text' => '',
                'flexible_stay_card1_icon' => '',
                'flexible_stay_card2_title' => '',
                'flexible_stay_card2_text' => '',
                'flexible_stay_card2_icon' => '',
                'flexible_stay_card3_title' => '',
                'flexible_stay_card3_text' => '',
                'flexible_stay_card3_icon' => '',
                'url_booking' => '',
                'booking_engine_url' => '',
                'url_expedia' => '',
                'url_emerging_travel' => '',
                'url_tripadvisor' => '',
                'url_google_business' => '',
                'google_map_embed' => '',
                'youtube_stories_embed' => '',
            ];
        }
        $view->with('setting', $setting);

        $about = $data['about'] ?? null;
        if ($about === null) {
            $about = Cache::remember(self::CACHE_KEY_ABOUT, 300, fn () => About::query()->first());
        }
        if ($about === null) {
            $about = (object) [
                'title' => '',
                'mission' => '',
                'vision' => '',
                'background' => '',
                'welcome' => '',
                'values' => '',
                'chooseUs' => '',
                'specialities' => '',
                'calculumn' => '',
                'startYear' => '',
                'students' => '',
                'graduates' => '',
                'aboutImage' => '',
                'middleImage' => '',
                'chooseusImage' => '',
                'terms' => '',
            ];
        }
        $view->with('about', $about);

        if (! array_key_exists('facilities', $data) || $data['facilities'] === null) {
            $view->with('facilities', Cache::remember(FrontendPageCache::FOOTER_FACILITIES, 300, fn () => Facility::query()
                ->orderBy('created_at', 'asc')
                ->limit(12)
                ->get()));
        }

        if (! array_key_exists('partners', $data)) {
            $view->with('partners', Cache::remember('front_layout.partners', 300, fn () => Partner::query()
                ->whereNotNull('image')
                ->where('image', '!=', '')
                ->orderBy('created_at', 'asc')
                ->get()));
        }

        if (! array_key_exists('pageHeaders', $data)) {
            $view->with('pageHeaders', Cache::remember('front_layout.page_headers', 300, fn () => PageHeader::query()
                ->get()
                ->keyBy('page_key')));
        }
    }
}
