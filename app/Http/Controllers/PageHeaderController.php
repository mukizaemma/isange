<?php

namespace App\Http\Controllers;

use App\Models\PageHeader;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\View;

class PageHeaderController extends Controller
{
    public const CACHE_KEY = 'front_layout.page_headers';

    public function index(): View
    {
        $headers = PageHeader::query()->orderBy('label')->get();

        return view('admin.page-headers', compact('headers'));
    }

    public function save(Request $request): RedirectResponse
    {
        $headers = PageHeader::query()->orderBy('id')->get();

        foreach ($headers as $header) {
            $key = $header->page_key;
            $header->title = $request->input("headers.{$key}.title");
            $header->subtitle = $request->input("headers.{$key}.subtitle");

            if ($request->hasFile("headers.{$key}.hero_image")) {
                $path = $request->file("headers.{$key}.hero_image")->store('public/images/pages');
                $header->hero_image = basename($path);
            }

            $header->save();
        }

        Cache::forget(self::CACHE_KEY);

        return redirect()->back()->with('success', 'Page banners have been updated successfully.');
    }
}
