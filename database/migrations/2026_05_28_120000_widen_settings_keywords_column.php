<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Meta keyword lists for SEO often exceed VARCHAR(255).
     */
    public function up(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->text('keywords')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->string('keywords')->nullable()->change();
        });
    }
};
