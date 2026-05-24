<?php

namespace App\Support;

use Illuminate\Support\Collection;

class ExperienceCatalog
{
    /**
     * @return list<array{id: string, icon: string, title: string, text: string}>
     */
    public static function items(?Collection $pageHeaders = null): array
    {
        $page = PageContent::get('experiences', $pageHeaders ?? collect());
        $items = $page['sections']['items'] ?? [];

        if ($items !== []) {
            return $items;
        }

        return PageContentDefaults::sections('experiences')['items'] ?? [];
    }

    public static function find(string $id, ?Collection $pageHeaders = null): ?array
    {
        foreach (self::items($pageHeaders) as $item) {
            if (($item['id'] ?? '') === $id) {
                return $item;
            }
        }

        return null;
    }
}
