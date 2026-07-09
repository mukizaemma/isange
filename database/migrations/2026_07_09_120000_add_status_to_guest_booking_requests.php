<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('guest_booking_requests', function (Blueprint $table) {
            $table->string('status', 32)->default('pending')->after('completed_channel');
            $table->timestamp('confirmed_at')->nullable()->after('status');

            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::table('guest_booking_requests', function (Blueprint $table) {
            $table->dropIndex(['status']);
            $table->dropColumn(['status', 'confirmed_at']);
        });
    }
};
