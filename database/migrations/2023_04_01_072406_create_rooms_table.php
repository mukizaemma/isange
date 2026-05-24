<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('rooms', function (Blueprint $table) {
            $table->id();
            $table->string('roomName');
            $table->enum('category', ['single', 'double', 'tween','apartment'])->nullable();
            $table->decimal('price', 8, 2)->nullable();
            $table->string('size')->nullable();
            $table->integer('maxAdults')->nullable();
            $table->integer('maxChildren')->nullable();
            $table->integer('quantity')->nullable();
            $table->date('dateAvailable')->nullable();
            $table->boolean('is_available')->default(true);
            $table->string('image');
            $table->longText('description');
            $table->string('slug')->unique()->nullable();
            $table->enum('status', ['active', 'inactive', 'maintenance'])->default('Active');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rooms');
    }
};
