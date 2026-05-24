<?php

namespace App\View\Composers;

use App\Models\Setting;
use App\View\Composers\FrontLayoutComposer;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\View;

class AuthLayoutComposer
{
    public function compose(View $view): void
    {
        $setting = Cache::remember(FrontLayoutComposer::CACHE_KEY_SETTING, 300, fn () => Setting::query()->first());

        if ($setting === null) {
            $setting = (object) [
                'company' => config('app.name', 'Isange Paradise Eco Resort'),
                'logo' => '',
                'flexible_stay_bg_image' => '',
                'facilities_hero_image' => '',
            ];
        }

        $brandLogo = ! empty($setting->logo ?? null)
            ? asset('storage/images/'.ltrim($setting->logo, '/'))
            : asset('assets/images/isange-logo.png');

        $heroImage = ! empty($setting->flexible_stay_bg_image ?? null)
            ? asset('storage/images/pages/'.ltrim($setting->flexible_stay_bg_image, '/'))
            : (! empty($setting->facilities_hero_image ?? null)
                ? asset('storage/images/pages/'.ltrim($setting->facilities_hero_image, '/'))
                : null);

        $view->with([
            'setting' => $setting,
            'brandLogo' => $brandLogo,
            'brandName' => $setting->company ?? config('app.name', 'Isange Paradise Eco Resort'),
            'authHeroImage' => $heroImage,
        ]);
    }
}
