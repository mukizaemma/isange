<?php

namespace App\Support;

use App\Models\PageHeader;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;

class PageContent
{
    public static function get(string $pageKey, ?Collection $headers = null): array
    {
        $headers ??= collect();
        /** @var PageHeader|null $row */
        $row = $headers->get($pageKey);
        $defaults = PageContentDefaults::all()[$pageKey] ?? [];

        $sections = array_replace_recursive(
            $defaults['sections'] ?? [],
            is_array($row?->sections_json) ? $row->sections_json : []
        );

        return [
            'page_key' => $pageKey,
            'label' => $row?->label,
            'title' => $row?->title,
            'subtitle' => $row?->subtitle,
            'hero_image' => $row?->hero_image,
            'intro_html' => self::firstNonEmpty($row?->intro_html, $defaults['intro_html'] ?? null),
            'body_html' => self::firstNonEmpty($row?->body_html, $defaults['body_html'] ?? null),
            'sections' => $sections,
        ];
    }

    public static function section(string $pageKey, string $key, mixed $default = null, ?Collection $headers = null): mixed
    {
        return Arr::get(self::get($pageKey, $headers)['sections'], $key, $default);
    }

    public static function html(string $pageKey, string $field, ?Collection $headers = null): ?string
    {
        $data = self::get($pageKey, $headers);

        return $data[$field] ?? null;
    }

    private static function firstNonEmpty(?string ...$values): ?string
    {
        foreach ($values as $value) {
            if ($value !== null && trim(strip_tags($value)) !== '') {
                return $value;
            }
        }

        return null;
    }
}
