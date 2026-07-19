<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('marketing_opt_in')->default(false)->after('email_verified_at');
            $table->timestamp('marketing_consented_at')->nullable()->after('marketing_opt_in');
            $table->string('marketing_unsubscribe_token', 64)->nullable()->unique()->after('marketing_consented_at');
            $table->string('email_otp_hash')->nullable()->after('marketing_unsubscribe_token');
            $table->timestamp('email_otp_expires_at')->nullable()->after('email_otp_hash');
            $table->unsignedTinyInteger('email_otp_attempts')->default(0)->after('email_otp_expires_at');
        });

        Schema::table('guest_booking_requests', function (Blueprint $table) {
            $table->foreignId('user_id')->nullable()->after('id')->constrained()->nullOnDelete();
            $table->boolean('discount_applied')->default(false)->after('total_usd');
        });
    }

    public function down(): void
    {
        Schema::table('guest_booking_requests', function (Blueprint $table) {
            $table->dropConstrainedForeignId('user_id');
            $table->dropColumn('discount_applied');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropUnique(['marketing_unsubscribe_token']);
            $table->dropColumn([
                'marketing_opt_in',
                'marketing_consented_at',
                'marketing_unsubscribe_token',
                'email_otp_hash',
                'email_otp_expires_at',
                'email_otp_attempts',
            ]);
        });
    }
};
