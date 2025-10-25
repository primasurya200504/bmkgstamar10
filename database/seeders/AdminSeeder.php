<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@bmkgstamar4.com'],
            [
                'name' => 'Administrator BMKG',
                'email' => 'admin@bmkgstamar4.com',
                'phone_number' => '081234567890',
                'role' => 'admin',
                'password' => bcrypt('password'),
                'email_verified_at' => now(),
            ]
        );

        echo "✅ Admin user created successfully!\n";
    }
}
