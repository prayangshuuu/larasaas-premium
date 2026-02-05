<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Demo Admin User
        User::firstOrCreate(
            ['email' => 'admin@demo.com'],
            [
                'name' => 'Admin User',
                'username' => 'admin',
                'password' => Hash::make('password'),
                'is_admin' => true,
                'email_verified_at' => now(),
            ]
        );

        // Demo Regular User
        User::firstOrCreate(
            ['email' => 'user@demo.com'],
            [
                'name' => 'Regular User',
                'username' => 'user',
                'password' => Hash::make('password'),
                'is_admin' => false,
                'email_verified_at' => now(),
            ]
        );

        // Personal Admin Account
        User::firstOrCreate(
            ['email' => 'prayangshuuu@gmail.com'],
            [
                'name' => 'Prayangshu Admin',
                'username' => 'prayangshuuu',
                'password' => Hash::make('Test@321'),
                'is_admin' => true,
                'email_verified_at' => now(),
            ]
        );

        // Personal User Account
        User::firstOrCreate(
            ['email' => 'prayangshu073@gmail.com'],
            [
                'name' => 'Prayangshu',
                'username' => 'prayangshu',
                'password' => Hash::make('Test@321'),
                'is_admin' => false,
                'email_verified_at' => now(),
            ]
        );
    }
}
