<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->string('flexible_stay_bg_image')->nullable()->after('dining_intro');
            $table->string('flexible_stay_heading')->nullable()->after('flexible_stay_bg_image');
            $table->text('flexible_stay_subheading')->nullable()->after('flexible_stay_heading');

            $table->string('flexible_stay_card1_title')->nullable()->after('flexible_stay_subheading');
            $table->text('flexible_stay_card1_text')->nullable()->after('flexible_stay_card1_title');
            $table->string('flexible_stay_card1_icon')->nullable()->after('flexible_stay_card1_text');

            $table->string('flexible_stay_card2_title')->nullable()->after('flexible_stay_card1_icon');
            $table->text('flexible_stay_card2_text')->nullable()->after('flexible_stay_card2_title');
            $table->string('flexible_stay_card2_icon')->nullable()->after('flexible_stay_card2_text');

            $table->string('flexible_stay_card3_title')->nullable()->after('flexible_stay_card2_icon');
            $table->text('flexible_stay_card3_text')->nullable()->after('flexible_stay_card3_title');
            $table->string('flexible_stay_card3_icon')->nullable()->after('flexible_stay_card3_text');
        });
    }

    public function down(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->dropColumn([
                'flexible_stay_bg_image',
                'flexible_stay_heading',
                'flexible_stay_subheading',
                'flexible_stay_card1_title',
                'flexible_stay_card1_text',
                'flexible_stay_card1_icon',
                'flexible_stay_card2_title',
                'flexible_stay_card2_text',
                'flexible_stay_card2_icon',
                'flexible_stay_card3_title',
                'flexible_stay_card3_text',
                'flexible_stay_card3_icon',
            ]);
        });
    }
};

