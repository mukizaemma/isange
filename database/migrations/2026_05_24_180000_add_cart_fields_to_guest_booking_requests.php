<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('guest_booking_requests', function (Blueprint $table) {
            $table->json('cart_items')->nullable()->after('room_id');
            $table->string('payment_method', 32)->nullable()->after('fulfillment_choice');
            $table->decimal('total_usd', 10, 2)->nullable()->after('payment_method');
            $table->unsignedTinyInteger('adults')->nullable()->after('total_usd');
            $table->unsignedTinyInteger('children')->nullable()->after('adults');

            $table->index('payment_method');
        });
    }

    public function down(): void
    {
        Schema::table('guest_booking_requests', function (Blueprint $table) {
            $table->dropIndex(['payment_method']);
            $table->dropColumn(['cart_items', 'payment_method', 'total_usd', 'adults', 'children']);
        });
    }
};
