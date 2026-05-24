<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('guest_booking_requests', function (Blueprint $table) {
            $table->id();
            $table->uuid('public_id')->unique();
            $table->foreignId('room_id')->nullable()->constrained('rooms')->nullOnDelete();
            $table->date('check_in');
            $table->date('check_out');
            $table->boolean('airport_pickup')->default(false);
            $table->boolean('airport_dropoff')->default(false);
            $table->text('additional_requests')->nullable();
            $table->string('guest_name');
            $table->string('guest_phone', 64);
            $table->string('guest_email');
            $table->string('guest_country', 120);
            $table->string('fulfillment_choice', 32);
            $table->string('completed_channel', 32)->nullable();
            $table->longText('message_body');
            $table->timestamps();

            $table->index('fulfillment_choice');
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('guest_booking_requests');
    }
};
