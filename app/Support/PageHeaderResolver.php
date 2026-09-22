<?php

namespace App\Support;

use App\Models\PageHeader;

class PageHeaderResolver
{
    public static function resolve(
        string $pageKey,
        ?object $setting = null,
        ?object $about = null,
        ?PageHeader $header = null,
        array $overrides = []
    ): array {
        $fallback = self::fallback($pageKey, $setting, $about);

        $title = trim((string) ($overrides['title'] ?? $header?->title ?? $fallback['title'] ?? 'Page'));
        $subtitle = trim((string) ($overrides['subtitle'] ?? $header?->subtitle ?? $fallback['subtitle'] ?? ''));

        $imageFile = $header?->hero_image
            ?: ($overrides['imageFile'] ?? null)
            ?: ($fallback['imageFile'] ?? null);

        $imageDisk = $fallback['imageDisk'] ?? 'pages';

        return [
            'title' => $title !== '' ? $title : 'Page',
            'subtitle' => $subtitle !== '' ? $subtitle : null,
            'imageUrl' => self::imageUrl($imageFile, $imageDisk),
        ];
    }

    public static function imageUrl(?string $filename, string $disk = 'pages'): ?string
    {
        if ($filename === null || trim($filename) === '') {
            return null;
        }

        if (str_starts_with($filename, 'http://') || str_starts_with($filename, 'https://')) {
            return $filename;
        }

        $base = $disk === 'gallery'
            ? 'storage/images/gallery/'
            : 'storage/images/pages/';

        return asset($base.ltrim($filename, '/'));
    }

    /**
     * Pixel size of a locally stored page or gallery image, when the file is readable.
     *
     * @return array{width: int, height: int}|null
     */
    public static function dimensions(?string $imageUrl): ?array
    {
        if ($imageUrl === null || trim($imageUrl) === '') {
            return null;
        }

        $path = parse_url($imageUrl, PHP_URL_PATH) ?? '';
        if (! preg_match('~/(storage/images/(?:pages|gallery)/[^/?#]+)$~', $path, $matches)) {
            return null;
        }

        $full = public_path($matches[1]);
        if (! is_file($full)) {
            return null;
        }

        $size = @getimagesize($full);
        if ($size === false || ($size[0] ?? 0) < 1 || ($size[1] ?? 0) < 1) {
            return null;
        }

        return [
            'width' => (int) $size[0],
            'height' => (int) $size[1],
        ];
    }

    private static function fallback(string $pageKey, ?object $setting, ?object $about): array
    {
        $flexBg = $setting->flexible_stay_bg_image ?? null;
        $facilitiesHero = $setting->facilities_hero_image ?? null;
        $diningHero = $setting->dining_hero_image ?? null;

        return match ($pageKey) {
            'booking' => [
                'title' => 'Book a stay',
                'subtitle' => 'First choose how you want to book, then complete your stay details.',
                'imageFile' => $flexBg,
                'imageDisk' => 'pages',
            ],
            'about' => [
                'title' => 'About Isange Paradise',
                'subtitle' => 'A social enterprise eco-resort on the edge of Volcanoes National Park — owned by Future 4 Kids.',
                'imageFile' => $facilitiesHero ?? $flexBg,
                'imageDisk' => 'pages',
            ],
            'rooms' => [
                'title' => 'Accommodation',
                'subtitle' => $setting->flexible_stay_subheading ?? 'Stay in comfort surrounded by nature — 15 minutes drive from/to Volcanoes National Park office.',
                'imageFile' => $flexBg,
                'imageDisk' => 'pages',
            ],
            'facilities' => [
                'title' => 'Resort Facilities',
                'subtitle' => 'Restaurant & bar, gardens, meeting spaces, and more — surrounded by nature in Musanze.',
                'imageFile' => $facilitiesHero,
                'imageDisk' => 'pages',
            ],
            'dining' => [
                'title' => 'Restaurant & Bar',
                'subtitle' => 'Fresh local flavours in a relaxed garden setting.',
                'imageFile' => $diningHero,
                'imageDisk' => 'pages',
            ],
            'experiences' => [
                'title' => 'Experiences & Activities',
                'subtitle' => 'Explore Northern Rwanda from Musanze — gorilla trekking, volcanoes, culture, and community visits.',
                'imageFile' => $facilitiesHero,
                'imageDisk' => 'pages',
            ],
            'future4kids' => [
                'title' => 'Future 4 Kids',
                'subtitle' => 'Isange Paradise is owned by Future 4 Kids — your stay funds education, healthcare, and empowerment in Rwanda.',
                'imageFile' => $facilitiesHero ?? $flexBg,
                'imageDisk' => 'pages',
            ],
            'contact' => [
                'title' => 'Get in touch',
                'subtitle' => 'Choose how you would like to reach us or book — no contact form required.',
                'imageFile' => $facilitiesHero ?? $diningHero,
                'imageDisk' => 'pages',
            ],
            'gallery' => [
                'title' => 'Gallery',
                'subtitle' => 'Moments from Isange Paradise and the Musanze region.',
                'imageFile' => $facilitiesHero ?? $flexBg,
                'imageDisk' => 'pages',
            ],
            'terms' => [
                'title' => 'Terms & Conditions',
                'subtitle' => 'Please read these terms before booking your stay.',
                'imageFile' => $flexBg,
                'imageDisk' => 'pages',
            ],
            'services' => [
                'title' => 'Our Services',
                'subtitle' => 'Everything we offer to make your stay comfortable and memorable.',
                'imageFile' => $facilitiesHero ?? $flexBg,
                'imageDisk' => 'pages',
            ],
            'blogs' => [
                'title' => 'News & Updates',
                'subtitle' => 'Stories, announcements, and news from Isange Paradise.',
                'imageFile' => $flexBg,
                'imageDisk' => 'pages',
            ],
            default => [
                'title' => 'Page',
                'subtitle' => null,
                'imageFile' => null,
                'imageDisk' => 'pages',
            ],
        };
    }
}
