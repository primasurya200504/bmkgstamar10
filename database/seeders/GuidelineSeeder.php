<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Guideline;

class GuidelineSeeder extends Seeder
{
    public function run()
    {
        $guidelines = [
            [
                'title' => 'Data Cuaca Harian',
                'content' => 'Data observasi cuaca harian meliputi suhu, kelembaban, tekanan udara, kecepatan angin, dan curah hujan.',
                'requirements' => [
                    'Surat pengantar dari instansi/pribadi',
                    'Fotokopi KTP pemohon',
                    'Surat keterangan tujuan penggunaan data'
                ],
                'example_data' => [
                    'periode' => '1 Januari 2024 - 31 Januari 2024',
                    'parameter' => 'Suhu, Kelembaban, Tekanan, Angin, Hujan',
                    'format' => 'Excel (.xlsx)',
                    'biaya' => 'Rp 50.000 per bulan'
                ]
            ],
            [
                'title' => 'Data Iklim Bulanan',
                'content' => 'Data klimatologi bulanan yang mencakup rata-rata suhu, curah hujan, dan kondisi iklim lainnya.',
                'requirements' => [
                    'Surat pengantar resmi',
                    'Identitas pemohon',
                    'Proposal penelitian (untuk keperluan akademik)'
                ],
                'example_data' => [
                    'periode' => '2020-2024 (5 tahun)',
                    'parameter' => 'Rata-rata suhu, total curah hujan, hari hujan',
                    'format' => 'PDF dan Excel',
                    'biaya' => 'Rp 100.000 per tahun'
                ]
            ],
            [
                'title' => 'Data Angin dan Gelombang',
                'content' => 'Data meteorologi maritim meliputi kecepatan angin, arah angin, tinggi gelombang, dan kondisi laut.',
                'requirements' => [
                    'Surat pengantar dari perusahaan/instansi',
                    'Dokumen legalitas usaha (untuk komersial)',
                    'Surat pernyataan tidak menyalahgunakan data'
                ],
                'example_data' => [
                    'periode' => 'Per hari/bulan/tahun',
                    'parameter' => 'Kecepatan angin, arah angin, tinggi gelombang',
                    'lokasi' => 'Perairan Pontianak dan sekitarnya',
                    'biaya' => 'Rp 200.000 per tahun'
                ]
            ]
        ];

        foreach ($guidelines as $guideline) {
            Guideline::create($guideline);
        }
    }
}
