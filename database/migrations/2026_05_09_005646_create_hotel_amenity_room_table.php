<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('hotel_amenity_room', function (Blueprint $table) {
            $table->id();
            $table->foreignId('room_id')->constrained('rooms')->cascadeOnDelete();
            $table->foreignId('hotel_amenity_option_id')->constrained('hotel_amenity_options')->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['room_id', 'hotel_amenity_option_id'], 'room_amenity_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hotel_amenity_room');
    }
};
