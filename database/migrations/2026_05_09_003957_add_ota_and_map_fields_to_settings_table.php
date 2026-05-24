<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->string('url_booking')->nullable()->after('linkedin');
            $table->string('url_tripadvisor')->nullable()->after('url_booking');
            $table->string('url_google_business')->nullable()->after('url_tripadvisor');
            $table->string('url_expedia')->nullable()->after('url_google_business');
            $table->text('google_map_embed')->nullable()->after('url_expedia');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->dropColumn([
                'url_booking',
                'url_tripadvisor',
                'url_google_business',
                'url_expedia',
                'google_map_embed',
            ]);
        });
    }
};
