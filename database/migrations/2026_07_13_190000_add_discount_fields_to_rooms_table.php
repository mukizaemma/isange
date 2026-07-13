<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('rooms', function (Blueprint $table) {
            $table->boolean('discount_enabled')->default(false)->after('price_rwf');
            $table->string('discount_type', 16)->nullable()->after('discount_enabled');
            $table->decimal('discount_value', 10, 2)->nullable()->after('discount_type');
        });
    }

    public function down(): void
    {
        Schema::table('rooms', function (Blueprint $table) {
            $table->dropColumn(['discount_enabled', 'discount_type', 'discount_value']);
        });
    }
};
