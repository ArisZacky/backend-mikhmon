<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Seed 1 superadmin
        User::updateOrCreate(
            ['email' => 'superadmin@example.com'],
            [
                'user' => 'superadmin',
                'password' => Hash::make('admin123'),
                'role' => 'superadmin'
            ]
        );

        // Seed user biasa (opsional)
        User::updateOrCreate(
            ['email' => 'user@example.com'],
            [
                'user' => 'regularuser',
                'password' => Hash::make('user123'),
                'role' => 'user'
            ]
        );
    }
}
