<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('dining_menu_items', function (Blueprint $table) {
            $table->foreignId('menu_category_id')->nullable()->after('id')->constrained('menu_categories')->nullOnDelete();
            $table->text('description')->nullable()->after('title');
        });
    }

    public function down(): void
    {
        Schema::table('dining_menu_items', function (Blueprint $table) {
            $table->dropForeign(['menu_category_id']);
            $table->dropColumn(['menu_category_id', 'description']);
        });
    }
};
