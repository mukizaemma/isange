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

    private static function fallback(string $pageKey, ?object $setting, ?object $about): array
    {
        return match ($pageKey) {
            'booking' => [
                'title' => 'Book a stay',
                'subtitle' => 'First choose how you want to book, then complete your stay details.',
                'imageFile' => $setting->flexible_stay_bg_image ?? $about->aboutImage ?? null,
                'imageDisk' => ! empty($setting->flexible_stay_bg_image ?? null) ? 'pages' : 'gallery',
            ],
            'about' => [
                'title' => 'About Isange Paradise',
                'subtitle' => 'A social enterprise eco-resort on the edge of Volcanoes National Park — owned by Future 4 Kids.',
                'imageFile' => $about->aboutImage ?? null,
                'imageDisk' => 'gallery',
            ],
            'rooms' => [
                'title' => 'Accommodation',
                'subtitle' => $setting->flexible_stay_subheading ?? 'Stay in comfort surrounded by nature — 15 minutes drive from/to Volcanoes National Park office.',
                'imageFile' => $about->chooseusImage ?? null,
                'imageDisk' => 'gallery',
            ],
            'facilities' => [
                'title' => 'Resort Facilities',
                'subtitle' => 'Restaurant & bar, gardens, meeting spaces, and more — surrounded by nature in Musanze.',
                'imageFile' => $setting->facilities_hero_image ?? null,
                'imageDisk' => 'pages',
            ],
            'dining' => [
                'title' => 'Restaurant & Bar',
                'subtitle' => 'Fresh local flavours in a relaxed garden setting.',
                'imageFile' => $setting->dining_hero_image ?? null,
                'imageDisk' => 'pages',
            ],
            'experiences' => [
                'title' => 'Experiences & Activities',
                'subtitle' => 'Explore Northern Rwanda from Musanze — gorilla trekking, volcanoes, culture, and community visits.',
                'imageFile' => $setting->facilities_hero_image ?? null,
                'imageDisk' => 'pages',
            ],
            'future4kids' => [
                'title' => 'Future 4 Kids',
                'subtitle' => 'Isange Paradise is owned by Future 4 Kids — your stay funds education, healthcare, and empowerment in Rwanda.',
                'imageFile' => $about->aboutImage ?? null,
                'imageDisk' => 'gallery',
            ],
            'contact' => [
                'title' => 'Get in touch',
                'subtitle' => 'Choose how you would like to reach us or book — no contact form required.',
                'imageFile' => $about->middleImage ?? null,
                'imageDisk' => 'gallery',
            ],
            'gallery' => [
                'title' => 'Gallery',
                'subtitle' => 'Moments from Isange Paradise and the Musanze region.',
                'imageFile' => $about->middleImage ?? null,
                'imageDisk' => 'gallery',
            ],
            'terms' => [
                'title' => 'Terms & Conditions',
                'subtitle' => 'Please read these terms before booking your stay.',
                'imageFile' => $about->middleImage ?? null,
                'imageDisk' => 'gallery',
            ],
            'services' => [
                'title' => 'Our Services',
                'subtitle' => 'Everything we offer to make your stay comfortable and memorable.',
                'imageFile' => $about->aboutImage ?? null,
                'imageDisk' => 'gallery',
            ],
            'blogs' => [
                'title' => 'News & Updates',
                'subtitle' => 'Stories, announcements, and news from Isange Paradise.',
                'imageFile' => $about->middleImage ?? null,
                'imageDisk' => 'gallery',
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
