<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class Settings extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('settings')->insert([
            'title' => 'Ireme Technologies',
            'company' => 'Ireme Technologies',
            'address' => 'Kigali Kicukiro',
            'phone' => '+250 783 168 164',
            'email' => 'info@Ireme Technologies.org',
            'facebook' => 'https://facebook.com',
            'instagram' => 'https://instagram.com',
            'twitter' => 'https://twitter.com',
            'youtube' => 'https://youtube.com',
            'linkedin' => 'https://linkedin.com',
            'reserveUrl' => 'https://linktree.com',
            'logo' => 'path/to/default/logo.png',
            'created_at' => now(),
            'updated_at' => now(),
            ]);
    }
}
