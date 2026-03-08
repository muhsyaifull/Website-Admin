<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Package;

class PackageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Package::create([
            'name' => 'Tour Package',
            'label' => 'Skema 1',
            'description' => 'Tour Taman Atsiri dan Tour Museum Atsiri',
            'price' => 75000,
            'includes' => ['Tour Taman Atsiri', 'Tour Museum Atsiri'],
            'color' => '#7B3F2A',
            'bg_color' => '#F5EDE8',
            'has_saldo' => false,
            'has_resto' => false,
            'is_active' => true,
        ]);

        Package::create([
            'name' => 'Tour + Saldo',
            'label' => 'Skema 2',
            'description' => 'Tour lengkap dengan saldo voucher',
            'price' => 125000,
            'includes' => ['Tour Taman Atsiri', 'Tour Museum Atsiri', 'Saldo Voucher Rp 50.000'],
            'color' => '#B8860B',
            'bg_color' => '#FDF8E8',
            'has_saldo' => true,
            'saldo_amount' => 50000,
            'has_resto' => false,
            'is_active' => true,
        ]);

        Package::create([
            'name' => 'Tour + Refreshment',
            'label' => 'Skema 3',
            'description' => 'Tour lengkap dengan refreshment',
            'price' => 115000,
            'includes' => ['Tour Taman Atsiri', 'Tour Museum Atsiri', '1x Minuman', '1x Snack'],
            'color' => '#5A7A5A',
            'bg_color' => '#EBF2EB',
            'has_saldo' => false,
            'has_resto' => true,
            'is_active' => true,
        ]);
    }
}