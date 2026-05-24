<?php

namespace App\Http\Controllers;

use App\Models\DiningGalleryImage;
use App\Models\DiningMenuItem;
use App\Models\MenuCategory;
use App\Models\Setting;
use App\Services\OpenAiImageService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class DiningController extends Controller
{
    public function index()
    {
        $setting = Setting::first() ?? new Setting;
        $gallery = DiningGalleryImage::orderBy('sort_order')->orderBy('id')->get();

        return view('admin.dining.index', compact('setting', 'gallery'));
    }

    public function menuManage()
    {
        $items = DiningMenuItem::with('category')->orderBy('sort_order')->orderBy('title')->get();
        $categories = MenuCategory::orderBy('sort_order')->orderBy('name')->get();

        return view('admin.dining.menu', compact('items', 'categories'));
    }

    public function menuCategoriesManage()
    {
        $categories = MenuCategory::withCount('items')->orderBy('sort_order')->orderBy('name')->get();

        return view('admin.dining.menu-categories', compact('categories'));
    }

    public function savePage(Request $request)
    {
        $setting = Setting::firstOrFail();

        DB::transaction(function () use ($request, $setting) {
            $setting->dining_intro = $request->input('dining_intro');

            if ($request->hasFile('dining_hero_image') && $request->file('dining_hero_image')->isValid()) {
                $path = $request->file('dining_hero_image')->store('public/images/pages');
                $setting->dining_hero_image = basename($path);
            }

            $setting->save();
        });

        return redirect()->route('diningMenu')->with('success', 'Dining page content saved.');
    }

    public function storeCategory(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'cover_image' => 'nullable|image|max:6144',
        ]);

        $imageName = null;
        if ($request->hasFile('cover_image')) {
            $path = $request->file('cover_image')->store('images/menu-categories', 'public');
            $imageName = basename($path);
        }

        $maxSort = (int) MenuCategory::max('sort_order');

        MenuCategory::create([
            'name' => $request->name,
            'cover_image' => $imageName,
            'sort_order' => $maxSort + 1,
        ]);

        return redirect()->route('diningMenu.categories.manage')->with('success', 'Menu category created.');
    }

    public function updateCategory(Request $request, MenuCategory $category)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'cover_image' => 'nullable|image|max:6144',
        ]);

        $category->name = $request->name;

        if ($request->hasFile('cover_image')) {
            if ($category->cover_image) {
                Storage::disk('public')->delete('images/menu-categories/'.$category->cover_image);
            }
            $path = $request->file('cover_image')->store('images/menu-categories', 'public');
            $category->cover_image = basename($path);
        }

        $category->save();

        return redirect()->route('diningMenu.categories.manage')->with('success', 'Category updated.');
    }

    public function destroyCategory(MenuCategory $category)
    {
        if ($category->cover_image) {
            Storage::disk('public')->delete('images/menu-categories/'.$category->cover_image);
        }
        $category->delete();

        return redirect()->route('diningMenu.categories.manage')->with('success', 'Category removed. Items in this category are now uncategorized.');
    }

    public function aiSuggestImages(Request $request, OpenAiImageService $openAi)
    {
        $request->validate([
            'menu_title' => 'nullable|string|max:255',
            'items_summary' => 'required|string|max:2000',
        ]);

        if (! config('services.openai.key')) {
            return response()->json(['message' => 'Add OPENAI_API_KEY to your .env file to enable AI images.'], 422);
        }

        $title = $request->input('menu_title') ?: 'Restaurant menu';
        $summary = $request->input('items_summary');
        $prompt = 'Premium hotel restaurant hero image for category titled "'.addslashes($title).'". Dishes and mood inspired by: '.$summary.'. Photorealistic, appetizing, warm lighting, no text overlay, no logos.';

        $urls = $openAi->generateMenuCovers($prompt, 3);

        if ($urls === []) {
            return response()->json(['message' => 'No images returned. Check API billing and model access.'], 422);
        }

        return response()->json(['urls' => $urls]);
    }

    public function saveCategoryCoverFromUrl(Request $request, MenuCategory $category)
    {
        $request->validate([
            'url' => 'required|url|max:2000',
        ]);

        $url = $request->input('url');
        $host = strtolower((string) parse_url($url, PHP_URL_HOST));
        $allowed =
            str_contains($host, 'openai')
            || str_contains($host, 'blob.core.windows.net')
            || str_contains($host, 'azure');
        if (! $allowed) {
            return response()->json(['message' => 'URL host is not allowed.'], 422);
        }

        try {
            $bin = Http::timeout(60)->get($url)->body();
            if (strlen($bin) < 500) {
                return response()->json(['message' => 'Image download failed.'], 422);
            }
            $name = 'cat-cover-'.Str::random(12).'.png';
            Storage::disk('public')->put('images/menu-categories/'.$name, $bin);
            if ($category->cover_image) {
                Storage::disk('public')->delete('images/menu-categories/'.$category->cover_image);
            }
            $category->cover_image = $name;
            $category->save();
        } catch (\Throwable $e) {
            return response()->json(['message' => 'Could not save image.'], 422);
        }

        return response()->json(['ok' => true, 'cover_url' => asset('storage/images/menu-categories/'.$category->cover_image)]);
    }

    public function storeMenuItem(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:5000',
            'price_usd' => 'required|numeric|min:0',
            'price_rwf' => 'nullable|numeric|min:0',
            'prep_minutes' => 'nullable|integer|min:1|max:600',
            'image' => 'nullable|image|max:4096',
            'menu_category_id' => 'nullable|exists:menu_categories,id',
        ]);

        $imageName = null;
        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('public/images/dining');
            $imageName = basename($path);
        }

        $maxSort = (int) DiningMenuItem::max('sort_order');

        DiningMenuItem::create([
            'title' => $request->title,
            'description' => $request->input('description'),
            'price_usd' => $request->price_usd,
            'price_rwf' => $request->filled('price_rwf') ? $request->input('price_rwf') : null,
            'prep_minutes' => $request->filled('prep_minutes') ? $request->input('prep_minutes') : null,
            'image' => $imageName,
            'sort_order' => $maxSort + 1,
            'menu_category_id' => $request->input('menu_category_id'),
        ]);

        return redirect()->route('diningMenu.manage')->with('success', 'Menu item added.');
    }

    public function updateMenuItem(Request $request, DiningMenuItem $item)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:5000',
            'price_usd' => 'required|numeric|min:0',
            'price_rwf' => 'nullable|numeric|min:0',
            'prep_minutes' => 'nullable|integer|min:1|max:600',
            'image' => 'nullable|image|max:4096',
            'menu_category_id' => 'nullable|exists:menu_categories,id',
        ]);

        $item->title = $request->title;
        $item->description = $request->input('description');
        $item->price_usd = $request->price_usd;
        $item->price_rwf = $request->filled('price_rwf') ? $request->input('price_rwf') : null;
        $item->prep_minutes = $request->filled('prep_minutes') ? $request->input('prep_minutes') : null;
        $item->menu_category_id = $request->input('menu_category_id');

        if ($request->hasFile('image')) {
            if ($item->image) {
                Storage::delete('public/images/dining/'.$item->image);
            }
            $path = $request->file('image')->store('public/images/dining');
            $item->image = basename($path);
        }

        $item->save();

        return redirect()->route('diningMenu.manage')->with('success', 'Menu item updated.');
    }

    public function destroyMenuItem(DiningMenuItem $item)
    {
        if ($item->image) {
            Storage::delete('public/images/dining/'.$item->image);
        }
        $item->delete();

        return redirect()->route('diningMenu.manage')->with('success', 'Menu item removed.');
    }

    public function storeGallery(Request $request)
    {
        $request->validate([
            'image' => 'required|image|max:6144',
            'caption' => 'nullable|string|max:255',
        ]);

        $path = $request->file('image')->store('public/images/dining-gallery');
        $maxSort = (int) DiningGalleryImage::max('sort_order');

        DiningGalleryImage::create([
            'image' => basename($path),
            'caption' => $request->caption,
            'sort_order' => $maxSort + 1,
        ]);

        return redirect()->route('diningMenu')->with('success', 'Gallery image added.');
    }

    public function destroyGallery(DiningGalleryImage $diningGalleryImage)
    {
        Storage::delete('public/images/dining-gallery/'.$diningGalleryImage->image);
        $diningGalleryImage->delete();

        return redirect()->route('diningMenu')->with('success', 'Gallery image removed.');
    }
}
