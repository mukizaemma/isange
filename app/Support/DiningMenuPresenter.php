<?php

namespace App\Support;

use App\Models\DiningMenuItem;
use App\Models\MenuCategory;
use Illuminate\Support\Str;

class DiningMenuPresenter
{
    /**
     * @return list<array<string, mixed>>
     */
    public static function serializeItem(DiningMenuItem $item): array
    {
        $rawDesc = $item->description ? strip_tags($item->description) : '';
        $rawDesc = html_entity_decode($rawDesc, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $rawDesc = trim(preg_replace('/\s+/u', ' ', $rawDesc));
        $short = Str::limit($rawDesc, 160);

        $rwfAttr = $item->price_rwf && (float) $item->price_rwf > 0
            ? (string) (int) round((float) $item->price_rwf)
            : '';

        $imageUrl = null;
        if (! empty($item->image)) {
            $imageUrl = asset('storage/images/dining/'.ltrim($item->image, '/'));
        }

        return [
            'id' => $item->id,
            'title' => $item->title,
            'description' => $short,
            'descriptionTitle' => $rawDesc,
            'imageUrl' => $imageUrl,
            'priceHtml' => Currency::formatUsdWithLocal($item->price_usd, $item->price_rwf),
            'priceHtmlUsd' => Currency::formatDiningPrice($item->price_usd, $item->price_rwf, 'usd'),
            'priceHtmlRwf' => Currency::formatDiningPrice($item->price_usd, $item->price_rwf, 'rwf'),
            'priceUsd' => number_format((float) $item->price_usd, 2, '.', ''),
            'priceRwfAttr' => $rwfAttr,
            'prepMinutes' => $item->prep_minutes ? (int) $item->prep_minutes : null,
        ];
    }

    /**
     * @return list<array<string, mixed>>
     */
    public static function todaysMenuItems(?int $limit = null): array
    {
        $query = DiningMenuItem::query()
            ->where('is_today_menu', true)
            ->latest('updated_at')
            ->latest('id');

        if ($limit !== null) {
            $query->take($limit);
        }

        $items = $query->get();

        if ($items->isEmpty()) {
            return [];
        }

        return $items->map(fn (DiningMenuItem $item) => self::serializeItem($item))->values()->all();
    }

    /**
     * @return array<int, array{label: string, items: list<array<string, mixed>>}>
     */
    public static function fullMenuColumns(): array
    {
        $allMenuItems = DiningMenuItem::query()
            ->with('category')
            ->leftJoin('menu_categories as mc', 'dining_menu_items.menu_category_id', '=', 'mc.id')
            ->orderByRaw('COALESCE(mc.sort_order, 999999)')
            ->orderBy('mc.name')
            ->orderBy('dining_menu_items.sort_order')
            ->orderBy('dining_menu_items.title')
            ->select('dining_menu_items.*')
            ->get();

        $menuCategories = MenuCategory::orderBy('sort_order')->orderBy('name')->get();
        $columns = [];

        foreach ($menuCategories as $cat) {
            $catItems = $allMenuItems->where('menu_category_id', $cat->id)->values();
            if ($catItems->isEmpty()) {
                continue;
            }
            $coverUrl = ! empty($cat->cover_image)
                ? asset('storage/images/menu-categories/'.ltrim($cat->cover_image, '/'))
                : null;
            $columns[] = [
                'label' => $cat->name,
                'coverUrl' => $coverUrl,
                'items' => $catItems->map(fn (DiningMenuItem $i) => self::serializeItem($i))->values()->all(),
            ];
        }

        $uncatItems = $allMenuItems->whereNull('menu_category_id')->values();
        if ($menuCategories->isNotEmpty() && $uncatItems->isNotEmpty()) {
            $columns[] = [
                'label' => 'Other',
                'coverUrl' => null,
                'items' => $uncatItems->map(fn (DiningMenuItem $i) => self::serializeItem($i))->values()->all(),
            ];
        }

        if ($columns === [] && $allMenuItems->isNotEmpty()) {
            $columns[] = [
                'label' => 'Menu',
                'coverUrl' => null,
                'items' => $allMenuItems->map(fn (DiningMenuItem $i) => self::serializeItem($i))->values()->all(),
            ];
        }

        return $columns;
    }
}
