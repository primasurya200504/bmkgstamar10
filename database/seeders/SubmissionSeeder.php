<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Guideline;
use App\Models\Submission;
use App\Models\Payment;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class SubmissionSeeder extends Seeder
{
    public function run()
    {
        // Create Admin User
        $admin = User::updateOrCreate(
            ['email' => 'admin@bmkg.go.id'],
            [
                'name' => 'Admin BMKG STAMAR',
                'password' => Hash::make('admin123'),
                'role' => 'admin',
                'phone' => '081234567890',
                'email_verified_at' => Carbon::now()
            ]
        );

        // Create User Example
        $user = User::updateOrCreate(
            ['email' => 'user@example.com'],
            [
                'name' => 'User Example',
                'password' => Hash::make('user123'),
                'role' => 'user',
                'phone' => '081234567891',
                'email_verified_at' => Carbon::now()
            ]
        );

        // Create Additional Users
        $users = [
            [
                'name' => 'Dr. Ahmad Meteorologi',
                'email' => 'ahmad.meteor@univ.ac.id',
                'password' => Hash::make('user123'),
                'role' => 'user',
                'phone' => '081234567892',
                'email_verified_at' => Carbon::now()
            ],
            [
                'name' => 'PT. Pelayaran Nusantara',
                'email' => 'data@pelayaran-nusantara.co.id',
                'password' => Hash::make('user123'),
                'role' => 'user',
                'phone' => '081234567893',
                'email_verified_at' => Carbon::now()
            ],
            [
                'name' => 'Peneliti Klimatologi',
                'email' => 'klimat.research@bmkg.go.id',
                'password' => Hash::make('user123'),
                'role' => 'user',
                'phone' => '081234567894',
                'email_verified_at' => Carbon::now()
            ]
        ];

        foreach ($users as $userData) {
            User::updateOrCreate(
                ['email' => $userData['email']],
                $userData
            );
        }



        // Create Sample Submissions
        $guideline1 = Guideline::where('title', 'Data Klimatologi untuk Penelitian')->first();
        $guideline2 = Guideline::where('title', 'Data Cuaca Maritim PNBP')->first();
        $guideline3 = Guideline::where('title', 'Data Meteorologi Real-time Komersial')->first();
        $guideline4 = Guideline::where('title', 'je9a')->first();

        $allUsers = User::where('role', 'user')->get();

        if ($guideline1 && $allUsers->count() > 0) {
            $submission1 = Submission::create([
                'user_id' => $allUsers->first()->id,
                'guideline_id' => $guideline1->id,
                'submission_number' => 'BMKG-SUR-2809-2025-0001',
                'type' => 'non_pnbp',
                'documents' => [
                    [
                        'index' => 0,
                        'original_name' => 'KTP_Peneliti.pdf',
                        'stored_name' => 'ktp_peneliti_' . time() . '.pdf',
                        'path' => 'submissions/non_pnbp/2025/09/ktp_peneliti_' . time() . '.pdf',
                        'size' => 1024000,
                        'mime_type' => 'application/pdf',
                        'extension' => 'pdf',
                        'uploaded_at' => Carbon::now()->toISOString()
                    ],
                    [
                        'index' => 1,
                        'original_name' => 'Surat_Permohonan_Univ.pdf',
                        'stored_name' => 'surat_univ_' . time() . '.pdf',
                        'path' => 'submissions/non_pnbp/2025/09/surat_univ_' . time() . '.pdf',
                        'size' => 2048000,
                        'mime_type' => 'application/pdf',
                        'extension' => 'pdf',
                        'uploaded_at' => Carbon::now()->toISOString()
                    ]
                ],
                'start_date' => Carbon::now()->addDays(7),
                'end_date' => Carbon::now()->addDays(14),
                'purpose' => 'Penelitian cuaca dan iklim untuk disertasi doktor dengan fokus pada analisis pola curah hujan di wilayah Kalimantan Barat dalam 10 tahun terakhir. Data akan digunakan untuk memodelkan prediksi cuaca jangka panjang.',
                'status' => 'pending',
                'created_at' => Carbon::now()->subHours(2),
                'updated_at' => Carbon::now()->subHours(2)
            ]);

            // Add history for submission1
            $submission1->logHistory(
                'submitted',
                'user',
                $allUsers->first()->id,
                'Pengajuan Surat/Data Disubmit',
                'User mengajukan ' . $guideline1->title . ' untuk periode penelitian'
            );
        }

        if ($guideline2 && $allUsers->count() > 1) {
            $submission2 = Submission::create([
                'user_id' => $allUsers->get(1)->id,
                'guideline_id' => $guideline2->id,
                'submission_number' => 'BMKG-SUR-2809-2025-0002',
                'type' => 'pnbp',
                'documents' => [
                    [
                        'index' => 0,
                        'original_name' => 'NPWP_Pelayaran.pdf',
                        'stored_name' => 'npwp_pelayaran_' . time() . '.pdf',
                        'path' => 'submissions/pnbp/2025/09/npwp_pelayaran_' . time() . '.pdf',
                        'size' => 512000,
                        'mime_type' => 'application/pdf',
                        'extension' => 'pdf',
                        'uploaded_at' => Carbon::now()->toISOString()
                    ],
                    [
                        'index' => 1,
                        'original_name' => 'Surat_Izin_Pelayaran.pdf',
                        'stored_name' => 'izin_pelayaran_' . time() . '.pdf',
                        'path' => 'submissions/pnbp/2025/09/izin_pelayaran_' . time() . '.pdf',
                        'size' => 1536000,
                        'mime_type' => 'application/pdf',
                        'extension' => 'pdf',
                        'uploaded_at' => Carbon::now()->toISOString()
                    ]
                ],
                'start_date' => Carbon::now()->addDays(1),
                'end_date' => Carbon::now()->addDays(30),
                'purpose' => 'Data cuaca maritim untuk operasional kapal komersial PT. Pelayaran Nusantara rute Pontianak-Jakarta. Data diperlukan untuk perencanaan jadwal keberangkatan dan keselamatan pelayaran.',
                'status' => 'verified',
                'created_at' => Carbon::now()->subDays(1),
                'updated_at' => Carbon::now()->subHours(8)
            ]);

            // Create payment for submission2
            Payment::create([
                'submission_id' => $submission2->id,
                'amount' => $guideline2->fee,
                'status' => 'pending'
            ]);

            // Add histories for submission2
            $submission2->logHistory(
                'submitted',
                'user',
                $allUsers->get(1)->id,
                'Pengajuan Surat/Data Disubmit',
                'User mengajukan ' . $guideline2->title . ' untuk operasional kapal komersial'
            );

            $submission2->logHistory(
                'verified',
                'admin',
                $admin->id,
                'Pengajuan Surat Diverifikasi',
                'Admin memverifikasi pengajuan surat/data. Status berubah dari pending menjadi verified.'
            );
        }

        if ($guideline3 && $allUsers->count() > 2) {
            $submission3 = Submission::create([
                'user_id' => $allUsers->get(2)->id,
                'guideline_id' => $guideline3->id,
                'submission_number' => 'BMKG-SUR-2709-2025-0003',
                'type' => 'pnbp',
                'documents' => [
                    [
                        'index' => 0,
                        'original_name' => 'KTP_Perusahaan.pdf',
                        'stored_name' => 'ktp_perusahaan_' . time() . '.pdf',
                        'path' => 'submissions/pnbp/2025/09/ktp_perusahaan_' . time() . '.pdf',
                        'size' => 1024000,
                        'mime_type' => 'application/pdf',
                        'extension' => 'pdf',
                        'uploaded_at' => Carbon::now()->toISOString()
                    ]
                ],
                'start_date' => Carbon::now()->subDays(5),
                'end_date' => Carbon::now()->addDays(25),
                'purpose' => 'Data meteorologi real-time untuk monitoring kondisi cuaca pada fasilitas riset klimatologi BMKG. Data akan diintegrasikan dengan sistem monitoring otomatis.',
                'status' => 'completed',
                'created_at' => Carbon::now()->subDays(10),
                'updated_at' => Carbon::now()->subDays(1)
            ]);

            // Create and verify payment for submission3
            $payment3 = Payment::create([
                'submission_id' => $submission3->id,
                'amount' => $guideline3->fee,
                'status' => 'verified',
                'payment_proof' => 'payments/2025/09/payment_proof_' . time() . '.jpg',
                'payment_method' => 'Transfer Bank',
                'payment_reference' => 'TRF' . time(),
                'paid_at' => Carbon::now()->subDays(8),
                'verified_at' => Carbon::now()->subDays(7),
                'verified_by' => $admin->id
            ]);

            // Add complete histories for submission3
            $submission3->logHistory(
                'submitted',
                'user',
                $allUsers->get(2)->id,
                'Pengajuan Surat/Data Disubmit',
                'User mengajukan ' . $guideline3->title . ' untuk riset klimatologi'
            );

            $submission3->logHistory(
                'verified',
                'admin',
                $admin->id,
                'Pengajuan Surat Diverifikasi',
                'Admin memverifikasi pengajuan surat/data'
            );

            $submission3->logHistory(
                'payment_uploaded',
                'user',
                $allUsers->get(2)->id,
                'Bukti Pembayaran Diunggah',
                'User mengunggah bukti pembayaran melalui Transfer Bank'
            );

            $submission3->logHistory(
                'payment_verified',
                'admin',
                $admin->id,
                'Pembayaran Diverifikasi',
                'Admin memverifikasi pembayaran. Status berubah menjadi paid.'
            );

            $submission3->logHistory(
                'processing',
                'admin',
                $admin->id,
                'Sedang Diproses',
                'Surat/data sedang dalam proses pembuatan'
            );

            $submission3->logHistory(
                'completed',
                'admin',
                $admin->id,
                'Surat/Data Siap Diambil',
                'Surat/data telah selesai diproses dan siap diambil oleh pemohon'
            );
        }

        echo "✅ Submission seeder completed successfully!\n";
        echo "📊 Created:\n";
        echo "   - " . User::count() . " users\n";
        echo "   - " . Guideline::count() . " guidelines\n";
        echo "   - " . Submission::count() . " submissions\n";
        echo "   - " . Payment::count() . " payments\n";
        echo "\n🔐 Login credentials:\n";
        echo "   Admin: admin@bmkg.go.id / admin123\n";
        echo "   User: user@example.com / user123\n";
    }
}
