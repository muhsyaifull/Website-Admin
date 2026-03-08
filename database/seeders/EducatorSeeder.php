<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Educator;

class EducatorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Educator::create([
            'name' => 'Raka',
            'phone' => '081234567890',
            'specialization' => 'taman',
            'is_active' => true,
        ]);

        Educator::create([
            'name' => 'Sari',
            'phone' => '081234567891',
            'specialization' => 'taman',
            'is_active' => true,
        ]);

        Educator::create([
            'name' => 'Dian',
            'phone' => '081234567892',
            'specialization' => 'museum',
            'is_active' => true,
        ]);

        Educator::create([
            'name' => 'Budi',
            'phone' => '081234567893',
            'specialization' => 'museum',
            'is_active' => true,
        ]);
    }
}