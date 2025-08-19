<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Demo user
        User::create([
            'name' => 'Prayangshu',
            'username' => 'prayangshu',
            'email' => 'prayangshu073@gmail.com',
            'password' => Hash::make('Test@321'),
        ]);
    }
}
