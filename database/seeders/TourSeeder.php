<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Tour;

class TourSeeder extends Seeder
{
    public function run(): void
    {
        Tour::firstOrCreate(
            ['slug' => 'taman'],
            [
                'name' => 'Aromatic Garden',
                'description' => 'Tour Taman Atsiri - Aromatic Garden Tour',
                'is_active' => true,
                'sort_order' => 1,
            ]
        );

        Tour::firstOrCreate(
            ['slug' => 'museum'],
            [
                'name' => 'Museum',
                'description' => 'Tour Museum Atsiri - Essential Oil Museum Tour',
                'is_active' => true,
                'sort_order' => 2,
            ]
        );
    }
}
