<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('guest_booking_requests', function (Blueprint $table) {
            $table->dropUnique(['public_id']);
        });

        $ref = 1;
        foreach (DB::table('guest_booking_requests')->orderBy('id')->pluck('id') as $id) {
            DB::table('guest_booking_requests')->where('id', $id)->update([
                'public_id' => (string) $ref,
            ]);
            $ref++;
        }

        Schema::table('guest_booking_requests', function (Blueprint $table) {
            $table->string('public_id', 4)->change();
            $table->unique('public_id');
        });
    }

    public function down(): void
    {
        Schema::table('guest_booking_requests', function (Blueprint $table) {
            $table->dropUnique(['public_id']);
        });

        foreach (DB::table('guest_booking_requests')->orderBy('id')->pluck('id') as $id) {
            DB::table('guest_booking_requests')->where('id', $id)->update([
                'public_id' => (string) \Illuminate\Support\Str::uuid(),
            ]);
        }

        Schema::table('guest_booking_requests', function (Blueprint $table) {
            $table->uuid('public_id')->change();
            $table->unique('public_id');
        });
    }
};
