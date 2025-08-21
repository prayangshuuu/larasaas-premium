<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // This will find the user by email or create them if they don't exist.
        User::firstOrCreate(
            ['email' => 'prayangshu073@gmail.com'],
            [
                'name' => 'Prayangshu',
                'username' => 'prayangshu',
                'password' => Hash::make('Test@321'),
                'role' => 'user',
                'email_verified_at' => now(),
            ]
        );

        // Does the same for the admin user.
        User::firstOrCreate(
            ['email' => 'prayangshuuu@gmail.com'],
            [
                'name' => 'Admin',
                'username' => 'admin',
                'password' => Hash::make('Test@321'),
                'role' => 'admin',
                'email_verified_at' => now(),
            ]
        );
    }
}
