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
                'name' => 'Taman Atsiri',
                'description' => 'Tour Taman Atsiri - Aromatic Garden Tour',
                'icon' => 'fas fa-seedling',
                'color' => '#27AE60',
                'is_active' => true,
                'sort_order' => 1,
            ]
        );

        Tour::firstOrCreate(
            ['slug' => 'museum'],
            [
                'name' => 'Museum Atsiri',
                'description' => 'Tour Museum Atsiri - Essential Oil Museum Tour',
                'icon' => 'fas fa-building',
                'color' => '#7B3F2A',
                'is_active' => true,
                'sort_order' => 2,
            ]
        );
    }
}
