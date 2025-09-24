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
        try {
            // Clear existing data untuk mencegah duplikasi
            $this->command->info('🧹 Clearing existing data...');

            // Periksa apakah model exists sebelum delete
            if (class_exists('\App\Models\Application')) {
                \App\Models\Application::query()->delete();
            }

            if (class_exists('\App\Models\ApplicationHistory')) {
                \App\Models\ApplicationHistory::query()->delete();
            }

            // Create Admin User
            $this->command->info('👨‍💼 Creating admin user...');
            $admin = User::updateOrCreate(
                ['email' => 'admin@bmkg.go.id'],
                [
                    'name' => 'Admin BMKG STAMAR',
                    'phone' => '081234567890',
                    'role' => 'admin',
                    'password' => Hash::make('admin123'),
                    'email_verified_at' => now()
                ]
            );

            // Create Sample User
            $this->command->info('👤 Creating sample user...');
            $user = User::updateOrCreate(
                ['email' => 'user@example.com'],
                [
                    'name' => 'User Example',
                    'phone' => '081234567891',
                    'role' => 'user',
                    'password' => Hash::make('user123'),
                    'email_verified_at' => now()
                ]
            );

            // Clear existing guidelines first
            $this->command->info('📋 Creating guidelines...');
            Guideline::query()->delete();

            // Create Sample Guidelines
            $guidelines = [
                [
                    'title' => 'Data Klimatologi untuk Penelitian',
                    'description' => 'Permintaan data klimatologi untuk keperluan penelitian akademik dan ilmiah',
                    'type' => 'non_pnbp',
                    'required_documents' => [
                        'KTP/Identitas Diri',
                        'Surat Permohonan dari Institusi',
                        'Proposal Penelitian'
                    ],
                    'fee' => 0,
                    'is_active' => true
                ],
                [
                    'title' => 'Data Meteorologi Real-time Komersial',
                    'description' => 'Data meteorologi real-time untuk keperluan komersial dan bisnis',
                    'type' => 'pnbp',
                    'required_documents' => [
                        'KTP/Identitas Diri',
                        'NPWP',
                        'Surat Permohonan Perusahaan',
                        'Proposal Penggunaan Data'
                    ],
                    'fee' => 500000,
                    'is_active' => true
                ],
                [
                    'title' => 'Data Geofisika Gempa',
                    'description' => 'Data geofisika untuk penelitian gempa dan tsunami',
                    'type' => 'non_pnbp',
                    'required_documents' => [
                        'KTP/Identitas Diri',
                        'Surat Permohonan Institusi Penelitian',
                        'Surat Keterangan Penelitian'
                    ],
                    'fee' => 0,
                    'is_active' => true
                ],
                [
                    'title' => 'Data Cuaca Maritim PNBP',
                    'description' => 'Data cuaca maritim untuk pelayaran komersial',
                    'type' => 'pnbp',
                    'required_documents' => [
                        'KTP/Identitas Diri',
                        'Surat Izin Pelayaran',
                        'NPWP Perusahaan',
                        'Surat Permohonan'
                    ],
                    'fee' => 750000,
                    'is_active' => true
                ]
            ];

            foreach ($guidelines as $guidelineData) {
                Guideline::create($guidelineData);
            }

            $this->command->info('✅ Complete seeder executed successfully!');
            $this->command->info('📧 Admin: admin@bmkg.go.id (password: admin123)');
            $this->command->info('👤 User: user@example.com (password: user123)');
            $this->command->info('📋 ' . count($guidelines) . ' guidelines created');
        } catch (\Exception $e) {
            $this->command->error('❌ Seeder failed: ' . $e->getMessage());
            throw $e;
        }
    }
}
