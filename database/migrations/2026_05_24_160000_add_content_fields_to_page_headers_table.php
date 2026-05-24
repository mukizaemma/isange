<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('page_headers', function (Blueprint $table) {
            $table->text('intro_html')->nullable()->after('hero_image');
            $table->longText('body_html')->nullable()->after('intro_html');
            $table->json('sections_json')->nullable()->after('body_html');
        });

        $now = now();
        foreach ([
            ['page_key' => 'home', 'label' => 'Home page', 'title' => 'Isange Paradise Eco Resort', 'subtitle' => 'Your sustainable escape near Volcanoes National Park'],
            ['page_key' => 'global', 'label' => 'Site-wide (header & footer)', 'title' => null, 'subtitle' => null],
        ] as $row) {
            if (! DB::table('page_headers')->where('page_key', $row['page_key'])->exists()) {
                DB::table('page_headers')->insert(array_merge($row, [
                    'hero_image' => null,
                    'intro_html' => null,
                    'body_html' => null,
                    'sections_json' => null,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]));
            }
        }
    }

    public function down(): void
    {
        Schema::table('page_headers', function (Blueprint $table) {
            $table->dropColumn(['intro_html', 'body_html', 'sections_json']);
        });

        DB::table('page_headers')->whereIn('page_key', ['home', 'global'])->delete();
    }
};
