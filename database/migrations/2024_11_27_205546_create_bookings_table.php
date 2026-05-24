<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    public function up(): void
    {
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->string('names');
            $table->string('phone')->nullable();
            $table->string('email');
            $table->date('checkin');
            $table->date('checkout')->nullable();
            $table->integer('adults')->nullable();
            $table->integer('rooms')->nullable();
            $table->integer('children')->nullable();
            $table->integer('nights')->nullable();
            $table->decimal('total', 10, 2)->nullable();
            $table->string('address')->nullable();
            $table->longText('description')->nullable();
            $table->enum('status', ['Pending', 'Confirmed','Cancelled','Noshow'])->default('Pending');

            $table->unsignedBigInteger('room_id')->nullable();
            $table->foreign('room_id')->references('id')->on('rooms')->onDelete('cascade');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};
