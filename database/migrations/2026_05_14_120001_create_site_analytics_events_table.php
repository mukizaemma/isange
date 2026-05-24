<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('site_analytics_events', function (Blueprint $table) {
            $table->id();
            $table->string('event_key', 80)->index();
            $table->json('properties')->nullable();
            $table->string('session_id', 64)->nullable()->index();
            $table->timestamps();

            $table->index(['event_key', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('site_analytics_events');
    }
};
