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
        // Create Admin User
        User::create([
            'name' => 'Admin User',
            'email' => 'admin@admin.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'status' => 'active',
        ]);

        // Create Regular User
        User::create([
            'name' => 'John Doe',
            'email' => 'user@user.com',
            'password' => Hash::make('password'),
            'role' => 'user',
            'status' => 'active',
        ]);

        // Create additional sample users
        User::create([
            'name' => 'Jane Smith',
            'email' => 'jane@example.com',
            'password' => Hash::make('password'),
            'role' => 'user',
            'status' => 'active',
        ]);

        User::create([
            'name' => 'Mike Johnson',
            'email' => 'mike@example.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'status' => 'inactive',
        ]);

        User::create([
            'name' => 'Sarah Wilson',
            'email' => 'sarah@example.com',
            'password' => Hash::make('password'),
            'role' => 'user',
            'status' => 'active',
        ]);
    }
}