<?php

namespace App\Http\Controllers;

use App\Models\About;
use App\Models\Setting;
use App\View\Composers\FrontLayoutComposer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class SettingController extends Controller
{
    public function setting()
    {
        $data = Setting::first();
        if ($data === null) {
            $data = new Setting;
            $data->title = 'Company Name';
            $data->company = 'Company Name';
            $data->save();
            $data = Setting::first();
        }

        $about = About::first();
        if ($about === null) {
            $about = new About;
            $about->title = 'About';
            $about->save();
            $about = About::first();
        }

        return view('admin.settings', [
            'data' => $data,
            'about' => $about,
        ]);
    }

    public function saveSetting(Request $request)
    {
        $data = Setting::firstOrFail();

        DB::transaction(function () use ($request, $data) {
            $data->company = $request->input('company');
            $data->address = $request->input('address');
            $data->phone = $request->input('phone');
            $data->email = $request->input('email');
            $data->keywords = $request->input('keywords');
            $data->facebook = $request->input('facebook');
            $data->instagram = $request->input('instagram');
            $data->twitter = $request->input('twitter');
            $data->youtube = $request->input('youtube');
            $data->tiktok = $request->input('tiktok');

            $data->url_booking = $request->input('url_booking');
            $data->url_tripadvisor = $request->input('url_tripadvisor');
            $data->url_google_business = $request->input('url_google_business');
            $data->url_expedia = $request->input('url_expedia');
            $data->url_emerging_travel = $request->input('url_emerging_travel');
            $data->google_map_embed = $request->input('google_map_embed');
            $data->youtube_stories_embed = $request->input('youtube_stories_embed');

            $rate = (float) $request->input('usd_to_rwf_rate', $data->usd_to_rwf_rate ?? 1300);
            $data->usd_to_rwf_rate = $rate > 0 ? $rate : 1300;
            $data->facilities_intro = $request->input('facilities_intro');

            if ($request->hasFile('facilities_hero_image') && request('facilities_hero_image') != '') {
                $path = $request->file('facilities_hero_image')->store('public/images/pages');
                $data->facilities_hero_image = basename($path);
            }

            if ($request->hasFile('logo') && request('logo') != '') {
                $dir = 'public/images';

                if (File::exists($dir)) {
                    unlink($dir);
                }
                $path = $request->file('logo')->store($dir);
                $fileName = str_replace($dir, '', $path);

                $data->logo = $fileName;
            }

            // Flexible Stay section
            $data->flexible_stay_heading = $request->input('flexible_stay_heading');
            $data->flexible_stay_subheading = $request->input('flexible_stay_subheading');

            $data->flexible_stay_card1_title = $request->input('flexible_stay_card1_title');
            $data->flexible_stay_card1_text = $request->input('flexible_stay_card1_text');
            $data->flexible_stay_card1_icon = $request->input('flexible_stay_card1_icon');

            $data->flexible_stay_card2_title = $request->input('flexible_stay_card2_title');
            $data->flexible_stay_card2_text = $request->input('flexible_stay_card2_text');
            $data->flexible_stay_card2_icon = $request->input('flexible_stay_card2_icon');

            $data->flexible_stay_card3_title = $request->input('flexible_stay_card3_title');
            $data->flexible_stay_card3_text = $request->input('flexible_stay_card3_text');
            $data->flexible_stay_card3_icon = $request->input('flexible_stay_card3_icon');

            if ($request->hasFile('flexible_stay_bg_image') && request('flexible_stay_bg_image') != '') {
                $path = $request->file('flexible_stay_bg_image')->store('public/images/pages');
                $data->flexible_stay_bg_image = basename($path);
            }

            $data->save();

            $about = About::first();
            if ($about === null) {
                $about = new About;
                $about->title = $data->company ?? 'About';
            }
            $about->welcome = $request->input('welcome');
            $about->terms = $request->input('terms');
            $about->background = $request->input('background');
            $about->save();
        });

        Cache::forget(FrontLayoutComposer::CACHE_KEY_SETTING);
        Cache::forget(FrontLayoutComposer::CACHE_KEY_ABOUT);

        return redirect()->back()->with('success', 'Settings have been updated successfully.');
    }

    public function about()
    {
        $data = About::first();
        if ($data === null) {
            $data = new About;
            $data->title = 'Company Name';
            $data->save();
            $data = About::first();
        }

        return view('admin.about', ['data' => $data]);
    }

    public function saveAbout(Request $request)
    {
        $data = About::first();
        $data->terms = $request->input('terms');
        $data->welcome = $request->input('welcome');
        $data->background = $request->input('background');

        $data->update();

        Cache::forget(FrontLayoutComposer::CACHE_KEY_ABOUT);

        return redirect()->back()->with('success', 'Page has been updated successfully');
    }
}
