<?php

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run()
    {
        User::updateOrCreate(
            ['email' => 'superadmin@example.com'],
            [
                'user' => 'superadmin',
                'password' => Hash::make('admin123'),
                'role' => 'superadmin'
            ]
        );
    }
}
