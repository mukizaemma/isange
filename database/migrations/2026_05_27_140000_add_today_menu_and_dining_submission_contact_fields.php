<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('dining_menu_items', function (Blueprint $table) {
            $table->boolean('is_today_menu')->default(false)->after('sort_order');
        });

        Schema::table('guest_dining_submissions', function (Blueprint $table) {
            $table->string('guest_name', 255)->nullable()->after('channel');
            $table->string('guest_phone', 64)->nullable()->after('guest_name');
            $table->string('guest_email', 255)->nullable()->after('guest_phone');
            $table->text('special_requests')->nullable()->after('guest_email');
            $table->string('currency', 8)->default('usd')->after('special_requests');
            $table->string('grand_total_rwf', 32)->nullable()->after('grand_total_usd');
        });
    }

    public function down(): void
    {
        Schema::table('guest_dining_submissions', function (Blueprint $table) {
            $table->dropColumn([
                'guest_name',
                'guest_phone',
                'guest_email',
                'special_requests',
                'currency',
                'grand_total_rwf',
            ]);
        });

        Schema::table('dining_menu_items', function (Blueprint $table) {
            $table->dropColumn('is_today_menu');
        });
    }
};
