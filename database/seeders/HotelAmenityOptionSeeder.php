<?php

namespace Database\Seeders;

use App\Models\HotelAmenityOption;
use Illuminate\Database\Seeder;

class HotelAmenityOptionSeeder extends Seeder
{
    public function run(): void
    {
        $labels = [
            'Wi‑Fi',
            'Air conditioning',
            'Private bathroom',
            'Flat‑screen TV',
            'Family room',
            'Fully furnished apartment',
            'Kitchen / kitchenette',
            'Room service',
            'Coffee & tea facilities',
            'Airport pickup',
            'Garden / terrace',
            'Bar & lounge access',
            'Restaurant on site',
            'Sports screens',
            'Daily housekeeping',
            'Safe',
            'Workspace / desk',
        ];

        foreach ($labels as $i => $label) {
            HotelAmenityOption::firstOrCreate(
                ['label' => $label],
                ['sort_order' => $i]
            );
        }
    }
}
