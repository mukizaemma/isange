<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('guest_dining_submissions', function (Blueprint $table) {
            $table->id();
            $table->string('channel', 16);
            $table->json('items_json');
            $table->longText('message_body');
            $table->string('grand_total_usd', 32)->nullable();
            $table->string('session_id', 64)->nullable()->index();
            $table->timestamps();

            $table->index(['channel', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('guest_dining_submissions');
    }
};
