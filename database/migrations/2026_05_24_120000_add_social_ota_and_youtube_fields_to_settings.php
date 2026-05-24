<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->string('tiktok')->nullable()->after('youtube');
            $table->text('url_emerging_travel')->nullable()->after('url_expedia');
            $table->text('youtube_stories_embed')->nullable()->after('google_map_embed');
        });
    }

    public function down(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->dropColumn(['tiktok', 'url_emerging_travel', 'youtube_stories_embed']);
        });
    }
};
