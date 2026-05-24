<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('rooms', function (Blueprint $table) {
            $table->decimal('price_rwf', 12, 2)->nullable()->after('price');
        });

        Schema::table('dining_menu_items', function (Blueprint $table) {
            $table->decimal('price_rwf', 12, 2)->nullable()->after('price_usd');
        });
    }

    public function down(): void
    {
        Schema::table('rooms', function (Blueprint $table) {
            $table->dropColumn('price_rwf');
        });

        Schema::table('dining_menu_items', function (Blueprint $table) {
            $table->dropColumn('price_rwf');
        });
    }
};
