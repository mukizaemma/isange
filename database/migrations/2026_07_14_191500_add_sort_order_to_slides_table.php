<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('slides', function (Blueprint $table) {
            $table->unsignedInteger('sort_order')->default(0)->after('image');
        });

        // Preserve current visual order (oldest first) for existing slides.
        $slides = DB::table('slides')->orderBy('created_at')->orderBy('id')->get(['id']);
        foreach ($slides as $index => $slide) {
            DB::table('slides')->where('id', $slide->id)->update(['sort_order' => $index + 1]);
        }
    }

    public function down(): void
    {
        Schema::table('slides', function (Blueprint $table) {
            $table->dropColumn('sort_order');
        });
    }
};
