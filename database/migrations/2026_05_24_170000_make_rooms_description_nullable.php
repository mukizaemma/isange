<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (DB::getDriverName() !== 'sqlite') {
            DB::statement('ALTER TABLE rooms MODIFY description LONGTEXT NULL');
        }
        DB::table('rooms')->whereNull('description')->update(['description' => '']);
    }

    public function down(): void
    {
        DB::table('rooms')->whereNull('description')->update(['description' => '']);
        DB::statement('ALTER TABLE rooms MODIFY description LONGTEXT NOT NULL');
    }
};
