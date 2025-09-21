<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run()
    {
        User::create([
            'name' => 'Administrator BMKG',
            'email' => 'admin@bmkgstamar4.com',
            'password' => Hash::make('admin123'),
            'role' => 'admin',
            'phone' => '081234567890',
            'institution' => 'BMKG Stasiun Maritim Pontianak',
            'email_verified_at' => now(),
        ]);

        User::create([
            'name' => 'User Demo',
            'email' => 'user@bmkgstamar4.com',
            'password' => Hash::make('user123'),
            'role' => 'user',
            'phone' => '081234567891',
            'institution' => 'Universitas Demo',
            'email_verified_at' => now(),
        ]);
    }
}
