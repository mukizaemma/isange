<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('guest_updates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('title');
            $table->string('cover_image')->nullable();
            $table->text('description');
            $table->string('recipient_mode', 24);
            $table->date('booking_from')->nullable();
            $table->date('booking_to')->nullable();
            $table->unsignedInteger('recipient_count')->default(0);
            $table->unsignedInteger('sent_count')->default(0);
            $table->timestamp('sent_at')->nullable();
            $table->timestamps();
        });

        Schema::create('guest_update_recipients', function (Blueprint $table) {
            $table->id();
            $table->foreignId('guest_update_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->timestamp('sent_at')->nullable();
            $table->text('failure_reason')->nullable();
            $table->timestamps();
            $table->unique(['guest_update_id', 'user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('guest_update_recipients');
        Schema::dropIfExists('guest_updates');
    }
};
