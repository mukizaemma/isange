<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->unsignedInteger('discount_unlock_count')->default(0)->after('email_otp_attempts');
            $table->timestamp('last_discount_unlocked_at')->nullable()->after('discount_unlock_count');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['discount_unlock_count', 'last_discount_unlocked_at']);
        });
    }
};
