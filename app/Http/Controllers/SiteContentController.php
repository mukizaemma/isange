<?php

namespace App\Http\Controllers;

use App\Models\PageHeader;
use App\Support\PageContent;
use App\View\Composers\FrontLayoutComposer;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\View;

class SiteContentController extends Controller
{
    public const CACHE_KEY = 'front_layout.page_headers';

    public function index(): View
    {
        $headers = PageHeader::query()->get()->keyBy('page_key');

        $tabOrder = [
            'home', 'about', 'rooms', 'experiences', 'future4kids',
            'facilities', 'dining', 'services', 'gallery', 'contact',
            'booking', 'terms', 'blogs', 'global',
        ];

        $pages = collect($tabOrder)
            ->map(fn (string $key) => PageContent::get($key, $headers))
            ->filter(fn (array $page) => $headers->has($page['page_key']))
            ->values();

        return view('admin.site-content.index', compact('headers', 'pages', 'tabOrder'));
    }

    public function save(Request $request): RedirectResponse
    {
        $pages = PageHeader::query()->orderBy('id')->get();

        foreach ($pages as $page) {
            $key = $page->page_key;
            $input = $request->input("pages.{$key}", []);

            $page->title = $input['title'] ?? $page->title;
            $page->subtitle = $input['subtitle'] ?? $page->subtitle;
            $page->intro_html = $input['intro_html'] ?? null;
            $page->body_html = $input['body_html'] ?? null;

            if ($request->hasFile("pages.{$key}.hero_image")) {
                $path = $request->file("pages.{$key}.hero_image")->store('public/images/pages');
                $page->hero_image = basename($path);
            }

            if (isset($input['sections']) && is_array($input['sections'])) {
                $sections = $input['sections'];

                if ($key === 'future4kids' && isset($sections['mission_bullets_text'])) {
                    $sections['mission_bullets'] = array_values(array_filter(array_map(
                        'trim',
                        preg_split('/\r\n|\r|\n/', (string) $sections['mission_bullets_text']) ?: []
                    )));
                    unset($sections['mission_bullets_text']);
                }

                $page->sections_json = $this->cleanSections($sections);
            }

            $page->save();
        }

        Cache::forget(self::CACHE_KEY);
        Cache::forget(FrontLayoutComposer::CACHE_KEY_ABOUT);

        return redirect()->route('siteContent')->with('success', 'Website content has been saved.');
    }

    private function cleanSections(array $sections): array
    {
        $clean = [];

        foreach ($sections as $key => $value) {
            if (is_array($value)) {
                $filtered = array_values(array_filter($value, function ($item) {
                    if (! is_array($item)) {
                        return trim((string) $item) !== '';
                    }

                    return collect($item)->filter(fn ($v) => trim((string) $v) !== '')->isNotEmpty();
                }));
                if ($filtered !== []) {
                    $clean[$key] = $filtered;
                }
            } elseif (trim((string) $value) !== '') {
                $clean[$key] = $value;
            }
        }

        return $clean;
    }
}
