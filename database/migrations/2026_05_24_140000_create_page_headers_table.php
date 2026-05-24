<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('page_headers', function (Blueprint $table) {
            $table->id();
            $table->string('page_key')->unique();
            $table->string('label');
            $table->string('title')->nullable();
            $table->text('subtitle')->nullable();
            $table->string('hero_image')->nullable();
            $table->timestamps();
        });

        $now = now();
        $rows = [
            ['page_key' => 'booking', 'label' => 'Book a stay', 'title' => 'Book a stay', 'subtitle' => 'First choose how you want to book, then complete your stay details.'],
            ['page_key' => 'about', 'label' => 'About', 'title' => 'About Isange Paradise', 'subtitle' => 'A social enterprise eco-resort on the edge of Volcanoes National Park — owned by Future 4 Kids.'],
            ['page_key' => 'rooms', 'label' => 'Accommodation', 'title' => 'Accommodation', 'subtitle' => 'Stay in comfort surrounded by nature — 15 minutes drive from/to Volcanoes National Park office.'],
            ['page_key' => 'facilities', 'label' => 'Facilities', 'title' => 'Resort Facilities', 'subtitle' => 'Restaurant & bar, gardens, meeting spaces, and more — surrounded by nature in Musanze.'],
            ['page_key' => 'dining', 'label' => 'Dining', 'title' => 'Restaurant & Bar', 'subtitle' => 'Fresh local flavours in a relaxed garden setting.'],
            ['page_key' => 'experiences', 'label' => 'Experiences', 'title' => 'Experiences & Activities', 'subtitle' => 'Explore Northern Rwanda from Musanze — gorilla trekking, volcanoes, culture, and community visits.'],
            ['page_key' => 'future4kids', 'label' => 'Future 4 Kids', 'title' => 'Future 4 Kids', 'subtitle' => 'Isange Paradise is owned by Future 4 Kids — your stay funds education, healthcare, and empowerment in Rwanda.'],
            ['page_key' => 'contact', 'label' => 'Contact', 'title' => 'Get in touch', 'subtitle' => 'Choose how you would like to reach us or book — no contact form required.'],
            ['page_key' => 'gallery', 'label' => 'Gallery', 'title' => 'Gallery', 'subtitle' => 'Moments from Isange Paradise and the Musanze region.'],
            ['page_key' => 'terms', 'label' => 'Terms & conditions', 'title' => 'Terms & Conditions', 'subtitle' => 'Please read these terms before booking your stay.'],
            ['page_key' => 'services', 'label' => 'Services', 'title' => 'Our Services', 'subtitle' => 'Everything we offer to make your stay comfortable and memorable.'],
            ['page_key' => 'blogs', 'label' => 'Updates', 'title' => 'News & Updates', 'subtitle' => 'Stories, announcements, and news from Isange Paradise.'],
        ];

        foreach ($rows as $row) {
            DB::table('page_headers')->insert(array_merge($row, [
                'hero_image' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ]));
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('page_headers');
    }
};
