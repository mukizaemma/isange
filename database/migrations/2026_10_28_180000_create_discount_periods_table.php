<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('discount_periods', function (Blueprint $table) {
            $table->id();
            $table->date('starts_on');
            $table->date('ends_on');
            $table->string('discount_type', 20);
            $table->decimal('discount_value', 8, 2);
            $table->timestamps();
        });

        if (! Schema::hasTable('settings') || ! Schema::hasColumn('settings', 'discount_starts_on')) {
            return;
        }

        $setting = DB::table('settings')->first();
        if (! $setting || empty($setting->discount_starts_on) || empty($setting->discount_ends_on)) {
            return;
        }

        $rooms = DB::table('rooms')
            ->where('discount_enabled', 1)
            ->where('discount_value', '>', 0)
            ->get();
        if ($rooms->isEmpty()) {
            return;
        }

        $sample = $rooms
            ->groupBy(fn ($room) => $room->discount_type.'|'.$room->discount_value)
            ->sortByDesc(fn ($group) => $group->count())
            ->first()
            ->first();

        DB::table('discount_periods')->insert([
            'starts_on' => $setting->discount_starts_on,
            'ends_on' => $setting->discount_ends_on,
            'discount_type' => $sample->discount_type ?: 'percent',
            'discount_value' => $sample->discount_value,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('discount_periods');
    }
};
