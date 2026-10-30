<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('guest_booking_requests', function (Blueprint $table) {
            $table->timestamp('canceled_at')->nullable()->after('reviewed_at');
        });
    }

    public function down(): void
    {
        Schema::table('guest_booking_requests', function (Blueprint $table) {
            $table->dropColumn('canceled_at');
        });
    }
};
