<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Educator;
use App\Models\Tour;

class EducatorSeeder extends Seeder
{
    public function run(): void
    {
        $taman = Tour::where('slug', 'taman')->first();
        $museum = Tour::where('slug', 'museum')->first();

        $raka = Educator::create([
            'name' => 'Raka',
            'phone' => '081234567890',
            'specialization' => 'taman',
            'is_active' => true,
        ]);
        $raka->tours()->attach($taman);

        $sari = Educator::create([
            'name' => 'Sari',
            'phone' => '081234567891',
            'specialization' => 'taman',
            'is_active' => true,
        ]);
        $sari->tours()->attach($taman);

        $dian = Educator::create([
            'name' => 'Dian',
            'phone' => '081234567892',
            'specialization' => 'museum',
            'is_active' => true,
        ]);
        $dian->tours()->attach($museum);

        $budi = Educator::create([
            'name' => 'Budi',
            'phone' => '081234567893',
            'specialization' => 'museum',
            'is_active' => true,
        ]);
        $budi->tours()->attach($museum);
    }
}