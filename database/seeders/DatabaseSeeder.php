<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            UserSeeder::class,
            TourSeeder::class,
            PackageSeeder::class,
            EducatorSeeder::class,
            SessionTemplateSeeder::class,
            TourSessionSeeder::class,
        ]);
    }
}