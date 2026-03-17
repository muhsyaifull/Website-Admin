<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Package;
use App\Models\Tour;

class PackageSeeder extends Seeder
{
    public function run(): void
    {
        $tours = Tour::active()->get();

        $pkg1 = Package::create([
            'name' => 'Tour Museum',
            'label' => 'Reguler',
            'description' => 'Tour Museum Atsiri',
            'price' => 75000,
            'includes' => ['Tour Museum Atsiri'],
            'is_active' => true,
        ]);
        $pkg1->tours()->attach($tours->pluck('id'));

        $pkg2 = Package::create([
            'name' => 'Tour Museum & Tour Aromatic Garden',
            'label' => 'Package 1',
            'description' => 'Tour Museum & Tour Aromatic Garden',
            'price' => 125000,
            'includes' => ['Tour Aromatic Garden', 'Tour Museum'],
            'is_active' => true,
        ]);
        $pkg2->tours()->attach($tours->pluck('id'));

        $pkg3 = Package::create([
            'name' => 'Tour + Refreshment',
            'label' => 'Package 2',
            'description' => 'Tour lengkap dengan refreshment',
            'price' => 115000,
            'includes' => ['Tour Aromatic Garden', 'Tour Museum', '1x Minuman', '1x Snack'],
            'is_active' => true,
        ]);
        $pkg3->tours()->attach($tours->pluck('id'));
    }
}