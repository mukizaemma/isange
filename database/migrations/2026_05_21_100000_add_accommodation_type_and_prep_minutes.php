<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('rooms', function (Blueprint $table) {
            $table->string('accommodation_type', 16)->default('room')->after('category');
        });

        DB::table('rooms')->where('category', 'apartment')->update(['accommodation_type' => 'apartment']);

        Schema::table('dining_menu_items', function (Blueprint $table) {
            $table->unsignedSmallInteger('prep_minutes')->nullable()->after('price_rwf');
        });
    }

    public function down(): void
    {
        Schema::table('rooms', function (Blueprint $table) {
            $table->dropColumn('accommodation_type');
        });

        Schema::table('dining_menu_items', function (Blueprint $table) {
            $table->dropColumn('prep_minutes');
        });
    }
};
