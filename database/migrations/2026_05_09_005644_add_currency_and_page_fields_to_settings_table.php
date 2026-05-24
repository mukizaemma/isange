<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->decimal('usd_to_rwf_rate', 14, 2)->default(1300)->after('google_map_embed');
            $table->string('facilities_hero_image')->nullable()->after('usd_to_rwf_rate');
            $table->longText('facilities_intro')->nullable()->after('facilities_hero_image');
            $table->string('dining_hero_image')->nullable()->after('facilities_intro');
            $table->longText('dining_intro')->nullable()->after('dining_hero_image');
        });
    }

    public function down(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->dropColumn([
                'usd_to_rwf_rate',
                'facilities_hero_image',
                'facilities_intro',
                'dining_hero_image',
                'dining_intro',
            ]);
        });
    }
};
