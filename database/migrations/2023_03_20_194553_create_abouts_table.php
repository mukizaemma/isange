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
        Schema::create('abouts', function (Blueprint $table) {
            $table->id();
            $table->string('title')->nullable();
            $table->longText('mission')->nullable();
            $table->longText('vision')->nullable();
            $table->longText('background')->nullable();
            $table->longText('welcome')->nullable();
            $table->longText('values')->nullable();
            $table->longText('chooseUs')->nullable();
            $table->longText('specialities')->nullable();
            $table->longText('calculumn')->nullable();
            $table->text('startYear')->nullable();
            $table->text('students')->nullable();
            $table->text('graduates')->nullable();
            $table->text('aboutImage')->nullable();
            $table->text('middleImage')->nullable();
            $table->text('chooseusImage')->nullable();
            $table->longText('terms')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('abouts');
    }
};
