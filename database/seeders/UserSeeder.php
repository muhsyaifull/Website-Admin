<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create Admin IT User
        User::create([
            'name' => 'Admin IT',
            'username' => 'admin',
            'email' => 'admin@rumahatsiri.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'is_active' => true,
        ]);

        // Create Educator User
        User::create([
            'name' => 'Educator Staff',
            'username' => 'educator',
            'email' => 'educator@rumahatsiri.com',
            'password' => Hash::make('password'),
            'role' => 'educator',
            'is_active' => true,
        ]);

        // Create Cashier User
        User::create([
            'name' => 'Dewi - Cashier',
            'username' => 'kasir1',
            'email' => 'kasir@rumahatsiri.com',
            'password' => Hash::make('password'),
            'role' => 'cashier',
            'is_active' => true,
        ]);

        // Create additional sample users for testing
        User::create([
            'name' => 'Cashier 2',
            'username' => 'kasir2',
            'email' => 'kasir2@rumahatsiri.com',
            'password' => Hash::make('password'),
            'role' => 'cashier',
            'is_active' => true,
        ]);

        User::create([
            'name' => 'Educator 2',
            'username' => 'educator2',
            'email' => 'educator2@rumahatsiri.com',
            'password' => Hash::make('password'),
            'role' => 'educator',
            'is_active' => false,
        ]);
    }
}