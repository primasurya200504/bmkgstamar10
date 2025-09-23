<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Guideline;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class CompleteSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Create Admin Users
        $admin = User::firstOrCreate(
            ['email' => 'admin@bmkg.go.id'],
            [
                'name' => 'Admin BMKG STAMAR',
                'phone' => '081234567890',
                'role' => 'admin',
                'password' => Hash::make('admin123'),
                'email_verified_at' => now()
            ]
        );

        // 2. Create Sample User
        $user = User::firstOrCreate(
            ['email' => 'user@example.com'],
            [
                'name' => 'User Example',
                'phone' => '081234567891',
                'role' => 'user',
                'password' => Hash::make('user123'),
                'email_verified_at' => now()
            ]
        );

        // 3. Create Sample Guidelines
        $guidelines = [
            [
                'title' => 'Data Klimatologi',
                'description' => 'Permintaan data klimatologi untuk penelitian akademik',
                'type' => 'non_pnbp',
                'required_documents' => ['KTP', 'Surat Permohonan dari Institusi'],
                'fee' => 0,
                'is_active' => true
            ],
            [
                'title' => 'Data Meteorologi Real-time',
                'description' => 'Data meteorologi untuk keperluan komersial/bisnis',
                'type' => 'pnbp',
                'required_documents' => ['KTP', 'NPWP', 'Surat Permohonan', 'Proposal Penggunaan'],
                'fee' => 500000,
                'is_active' => true
            ],
            [
                'title' => 'Data Geofisika',
                'description' => 'Data geofisika untuk penelitian gempa dan tsunami',
                'type' => 'non_pnbp',
                'required_documents' => ['KTP', 'Surat Permohonan Institusi Penelitian'],
                'fee' => 0,
                'is_active' => true
            ]
        ];

        foreach ($guidelines as $guidelineData) {
            Guideline::firstOrCreate(
                ['title' => $guidelineData['title']],
                $guidelineData
            );
        }

        echo "✅ Complete seeder executed successfully!\n";
        echo "📧 Admin: admin@bmkg.go.id (password: admin123)\n";
        echo "👤 User: user@example.com (password: user123)\n";
        echo "📋 " . count($guidelines) . " guidelines created\n";
    }
}
