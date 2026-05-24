<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * OTA / review URLs from partners often exceed 255 characters (tracking params).
     */
    public function up(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->text('url_booking')->nullable()->change();
            $table->text('url_tripadvisor')->nullable()->change();
            $table->text('url_google_business')->nullable()->change();
            $table->text('url_expedia')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->string('url_booking')->nullable()->change();
            $table->string('url_tripadvisor')->nullable()->change();
            $table->string('url_google_business')->nullable()->change();
            $table->string('url_expedia')->nullable()->change();
        });
    }
};
