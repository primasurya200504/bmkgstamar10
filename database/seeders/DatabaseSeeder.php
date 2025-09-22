<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Guideline;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Create Admin User
        User::create([
            'name' => 'Admin BMKG STAMAR',
            'email' => 'admin@bmkgstamar.go.id',
            'password' => Hash::make('admin123'),
            'phone' => '081234567890',
            'role' => 'admin',
            'email_verified_at' => now(),
        ]);

        // Create Sample Users
        User::create([
            'name' => 'John Doe',
            'email' => 'user@example.com',
            'password' => Hash::make('user123'),
            'phone' => '081234567891',
            'role' => 'user',
            'email_verified_at' => now(),
        ]);

        User::create([
            'name' => 'Ahmad Mahasiswa',
            'email' => 'ahmad@university.ac.id',
            'password' => Hash::make('user123'),
            'phone' => '081234567892',
            'role' => 'user',
            'email_verified_at' => now(),
        ]);

        // Create Sample Guidelines
        Guideline::create([
            'title' => 'Data Meteorologi Bulanan',
            'description' => 'Layanan data meteorologi bulanan untuk keperluan penelitian dan analisis cuaca.',
            'type' => 'pnbp',
            'required_documents' => [
                'Surat pengantar dari instansi',
                'KTP pemohon',
                'Formulir permohonan'
            ],
            'fee' => 150000,
            'is_active' => true
        ]);

        Guideline::create([
            'title' => 'Data Klimatologi untuk Mahasiswa',
            'description' => 'Layanan data klimatologi untuk keperluan tugas akhir mahasiswa.',
            'type' => 'non_pnbp',
            'required_documents' => [
                'Surat pengantar dari kampus',
                'Kartu mahasiswa',
                'Proposal penelitian'
            ],
            'fee' => 50000,
            'is_active' => true
        ]);

        Guideline::create([
            'title' => 'Sertifikat Kalibrasi Alat',
            'description' => 'Layanan sertifikat kalibrasi untuk alat-alat meteorologi.',
            'type' => 'pnbp',
            'required_documents' => [
                'Surat permohonan',
                'Spesifikasi alat',
                'Bukti kepemilikan alat'
            ],
            'fee' => 500000,
            'is_active' => true
        ]);
    }
}
