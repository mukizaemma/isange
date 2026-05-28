<?php

namespace App\Support;

class PageContentDefaults
{
    public static function sections(string $pageKey): array
    {
        return self::all()[$pageKey]['sections'] ?? [];
    }

    public static function introHtml(string $pageKey): ?string
    {
        return self::all()[$pageKey]['intro_html'] ?? null;
    }

    public static function bodyHtml(string $pageKey): ?string
    {
        return self::all()[$pageKey]['body_html'] ?? null;
    }

    public static function all(): array
    {
        return [
            'global' => [
                'intro_html' => null,
                'body_html' => null,
                'sections' => [
                    'header_tagline' => 'Musanze, Rwanda — 15 minutes drive from/to Volcanoes National Park office',
                    'footer_blurb' => 'Your sustainable escape — 15 minutes drive from/to Volcanoes National Park office. Comfort, nature, and purpose — every stay supports community development through Future 4 Kids.',
                    'amenities_title' => 'Resort Facilities',
                    'amenities_lead' => 'Isange Paradise Eco Resort blends comfort, nature, and purpose — explore what awaits you in Musanze.',
                    'amenities_items' => [
                        ['emoji' => '🌿', 'title' => 'Eco-Friendly Resort', 'text' => 'Solar energy, organic gardens, and sustainable operations near Volcanoes National Park.', 'href' => '/about'],
                        ['emoji' => '🍽️', 'title' => 'Restaurant & Bar', 'text' => 'Fresh meals from our garden and local farmers — breakfast, lunch, and dinner.', 'href' => '/dining'],
                        ['emoji' => '🦍', 'title' => 'Gorilla Trekking Access', 'text' => '15 minutes drive from/to Volcanoes National Park office for unforgettable mountain gorilla encounters.', 'href' => '/experiences#gorilla'],
                        ['emoji' => '🌺', 'title' => 'Tropical Gardens', 'text' => 'Peaceful outdoor spaces for relaxation, events, and garden dining.', 'href' => '/facilities#garden'],
                        ['emoji' => '🛏️', 'title' => 'Comfortable Rooms', 'text' => 'Private bathrooms, Wi-Fi, balconies, and garden views for every traveler.', 'href' => '/accommodation'],
                        ['emoji' => '🎪', 'title' => 'Events & Celebrations', 'text' => 'Weddings, retreats, and conferences for up to 150 guests in nature.', 'href' => '/facilities'],
                        ['emoji' => '🛍️', 'title' => 'Future 4 Kids Shop', 'text' => 'Handmade Rwandan crafts and fashion — every purchase empowers local artisans.', 'href' => '/future-4-kids#shop'],
                        ['emoji' => '💚', 'title' => 'Social Impact', 'text' => '100% of profits support education, health, and community programs.', 'href' => '/future-4-kids'],
                        ['emoji' => '📶', 'title' => 'Free Wi-Fi', 'text' => 'Stay connected throughout the resort during your Musanze adventure.', 'href' => '/facilities'],
                    ],
                ],
            ],
            'home' => [
                'intro_html' => null,
                'body_html' => null,
                'sections' => [
                    'about_eyebrow' => 'About us',
                    'about_title' => 'Welcome to Isange Paradise',
                    'accommodation_eyebrow' => 'Accommodation',
                    'accommodation_title' => 'Stay in Comfort, Surrounded by Nature',
                    'accommodation_intro' => 'Each room is thoughtfully designed to offer comfort, privacy, and beautiful garden views.',
                    'accommodation_footnote' => 'Amenities include private bathroom, hot shower, balcony or terrace, garden access, and comfortable bedding.',
                    'why_eyebrow' => 'Why stay with us',
                    'why_title' => 'Why Guests Love Isange Paradise',
                    'why_cards' => [
                        ['icon' => 'fa-map-marker-alt', 'title' => 'Prime Location', 'text' => '15 minutes drive from/to Volcanoes National Park office — ideal for gorilla trekking and volcano adventures.'],
                        ['icon' => 'fa-leaf', 'title' => 'Eco-Friendly Living', 'text' => 'Solar energy, local sourcing, organic gardens, and sustainable operations throughout the resort.'],
                        ['icon' => 'fa-heart', 'title' => 'Authentic Hospitality', 'text' => 'Warm service, peaceful surroundings, and locally inspired experiences in Northern Rwanda.'],
                        ['icon' => 'fa-hands', 'title' => 'Social Impact', 'text' => '100% of profits support community development through Future 4 Kids.'],
                    ],
                    'experiences_eyebrow' => 'Experiences',
                    'experiences_title' => 'Explore Northern Rwanda',
                    'experiences_intro' => 'From iconic gorilla trekking to cultural village visits — we can help arrange your full adventure itinerary.',
                    'events_eyebrow' => 'Events & garden',
                    'events_title' => 'Celebrate in Nature',
                    'events_intro' => 'Our lush gardens and function hall welcome weddings, retreats, conferences, family celebrations, and community events.',
                    'events_capacity' => 'Capacity: up to 150 guests',
                    'events_types' => ['Weddings', 'Retreats', 'Conferences', 'Family celebrations', 'Community events'],
                    'cta_title' => 'Book Your Eco Stay — 15 Minutes from Volcanoes National Park',
                    'cta_text' => 'Experience Rwanda’s natural beauty while supporting meaningful change. Book today and stay with purpose.',
                ],
            ],
            'about' => [
                'intro_html' => '<p>Isange Paradise is more than a resort—it is a social enterprise designed to create lasting impact in Musanze, one of Rwanda’s top tourist destinations.</p><p>Owned by <strong>Future 4 Kids</strong>, our eco-resort provides meaningful employment, supports vulnerable families, and funds education, healthcare, skills development, and women’s empowerment.</p>',
                'body_html' => null,
                'sections' => [
                    'story_eyebrow' => 'Our story',
                    'story_title' => 'Welcome to Isange Paradise',
                    'team_eyebrow' => 'Our team',
                    'team_title' => 'Warm Rwandan Hospitality',
                    'team_intro' => 'Our team brings authentic care, local knowledge, and a commitment to sustainable tourism — so your stay is comfortable, memorable, and meaningful.',
                ],
            ],
            'rooms' => [
                'intro_html' => null,
                'body_html' => null,
                'sections' => [
                    'rooms_title' => 'Rooms',
                    'rooms_intro' => 'Comfortable garden rooms with private bathrooms, Wi-Fi, and beautiful views — ideal for couples, solo travellers, and small groups.',
                    'apartments_title' => 'Apartments',
                    'apartments_intro' => 'Spacious apartment-style stays with extra privacy and room to relax — perfect for families and longer visits.',
                ],
            ],
            'experiences' => [
                'intro_html' => '<p>Isange Paradise is your base for unforgettable adventures near Volcanoes National Park. We can help arrange your full itinerary — tell us your dates and interests when you book.</p>',
                'body_html' => null,
                'sections' => [
                    'items' => [
                        ['id' => 'gorilla', 'icon' => 'fa-paw', 'title' => 'Gorilla Trekking', 'text' => 'Track mountain gorillas in Volcanoes National Park — one of Africa’s most profound wildlife experiences, just 15 minutes from the resort.'],
                        ['id' => 'golden-monkey', 'icon' => 'fa-tree', 'title' => 'Golden Monkey Trekking', 'text' => 'Meet playful golden monkeys in their bamboo forest habitat — a lighter trek ideal for families and nature lovers.'],
                        ['id' => 'volcano', 'icon' => 'fa-mountain', 'title' => 'Volcano Hiking', 'text' => 'Hike the Virunga volcanoes for panoramic views of Rwanda, Uganda, and the DRC — guided options for varied fitness levels.'],
                        ['id' => 'caves', 'icon' => 'fa-dungeon', 'title' => 'Musanze Caves', 'text' => 'Explore ancient lava tubes beneath Musanze with expert guides — geology, history, and adventure underground.'],
                        ['id' => 'lakes', 'icon' => 'fa-water', 'title' => 'Twin Lakes', 'text' => 'Visit Burera and Ruhondo — twin crater lakes surrounded by hills, perfect for boat trips, picnics, and photography.'],
                        ['id' => 'culture', 'icon' => 'fa-people-arrows', 'title' => 'Cultural Village Experiences', 'text' => 'Connect with local traditions, dance, crafts, and daily life in communities around Musanze.'],
                        ['id' => 'birds', 'icon' => 'fa-dove', 'title' => 'Bird Watching', 'text' => 'Discover Albertine Rift endemics and forest species in gardens, wetlands, and park buffer zones.'],
                        ['id' => 'cycling', 'icon' => 'fa-bicycle', 'title' => 'Cycling Tours', 'text' => 'Pedal scenic routes through villages and farmland with views of the Virungas.'],
                        ['id' => 'community', 'icon' => 'fa-hands-helping', 'title' => 'Community Visits', 'text' => 'See how Future 4 Kids programs support education, health, and empowerment — travel with purpose.'],
                    ],
                ],
            ],
            'future4kids' => [
                'intro_html' => null,
                'body_html' => null,
                'sections' => [
                    'mission_eyebrow' => 'Our mission',
                    'mission_title' => 'Stay with purpose. Explore with impact.',
                    'mission_lead' => 'Future 4 Kids created Isange Paradise Eco Resort as a sustainable funding engine for vulnerable children and families in Musanze.',
                    'mission_text' => '<strong>100% of resort profits</strong> support education, healthcare, skills development, and women’s empowerment.',
                    'mission_bullets' => [
                        'Education support for children',
                        'Community healthcare outreach',
                        'Skills development & capacity building',
                        'Women empowerment initiatives',
                        'Meaningful local employment at the resort',
                    ],
                    'impact_title' => 'Travel that gives back',
                    'impact_text' => 'Every night at Isange Paradise directly supports Future 4 Kids programs. Conscious travel makes a measurable difference in Rwanda.',
                    'shop_eyebrow' => 'On-site shop',
                    'shop_title' => 'Future 4 Kids Shop',
                    'shop_intro' => 'Handmade Rwandan fashion, crafts, and accessories from artisans in our programs — available during your stay.',
                    'shop_items' => [
                        ['icon' => 'fa-tshirt', 'title' => 'Made in Rwanda clothing', 'text' => 'Locally designed and produced apparel.'],
                        ['icon' => 'fa-gem', 'title' => 'Handmade accessories', 'text' => 'Jewelry, bags, and crafts by skilled artisans.'],
                        ['icon' => 'fa-gift', 'title' => 'Gifts & souvenirs', 'text' => 'Meaningful mementos from your Rwanda journey.'],
                        ['icon' => 'fa-store', 'title' => 'Community-made products', 'text' => 'Every purchase empowers local families.'],
                    ],
                    'shop_footer' => 'Visit the shop during your stay, or contact us for availability.',
                ],
            ],
            'terms' => [
                'intro_html' => null,
                'body_html' => null,
                'sections' => [
                    'payment_title' => 'Our Payment Methods',
                    'payment_intro' => 'We accept payments through the following bank accounts:',
                    'payment_accounts' => [
                        ['label' => 'Bank Account (RWF - Equity):', 'value' => '4032200030584'],
                        ['label' => 'Bank Account (USD - Equity):', 'value' => '4032200030708'],
                        ['label' => 'SWIFT Code:', 'value' => 'EQBLRWRW'],
                        ['label' => 'Momo Pay:', 'value' => 'Contact resort for current payment details'],
                    ],
                    'policies' => [
                        ['icon' => 'fa-ban', 'title' => 'Cancellation Policy', 'text' => 'Free cancellation within 5 days.'],
                        ['icon' => 'fa-laptop-house', 'title' => 'Booking Channels', 'text' => 'Bookings are accepted only via our website, Booking.com, or listed partners.'],
                        ['icon' => 'fa-credit-card', 'title' => 'Payment Options', 'text' => 'Payments are only accepted through our official accounts or in person.'],
                    ],
                ],
            ],
            'contact' => [
                'intro_html' => null,
                'body_html' => null,
                'sections' => [],
            ],
            'facilities' => [
                'intro_html' => null,
                'body_html' => null,
                'sections' => [],
            ],
            'dining' => [
                'intro_html' => null,
                'body_html' => null,
                'sections' => [],
            ],
            'services' => [
                'intro_html' => null,
                'body_html' => null,
                'sections' => [],
            ],
            'gallery' => [
                'intro_html' => null,
                'body_html' => null,
                'sections' => [],
            ],
            'blogs' => [
                'intro_html' => null,
                'body_html' => null,
                'sections' => [],
            ],
            'booking' => [
                'intro_html' => null,
                'body_html' => null,
                'sections' => [
                    'benefits_heading' => 'Benefits of booking with us',
                    'benefits_lines' => [
                        ['text' => 'Best Price Guaranteed', 'type' => 'bullet'],
                        ['text' => 'Direct customer support', 'type' => 'bullet'],
                        ['text' => 'Late check out (1 pm)', 'type' => 'bullet'],
                        ['text' => 'Early check-in (10 am)', 'type' => 'bullet'],
                        ['text' => 'Subject to availability', 'type' => 'note'],
                        ['text' => 'Free Bird Watching', 'type' => 'bullet'],
                        ['text' => 'Support community projects', 'type' => 'bullet'],
                        ['text' => 'Exclusive Offers', 'type' => 'bullet'],
                    ],
                ],
            ],
        ];
    }
}
