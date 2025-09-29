<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Guideline;

class GuidelineSeeder extends Seeder
{
    public function run(): void
    {
        $guidelines = [
            [
                'title' => 'Data Klimatologi untuk Penelitian',
                'description' => 'Permintaan data klimatologi untuk keperluan penelitian akademik, skripsi, tesis, atau disertasi',
                'type' => 'non_pnbp',
                'required_documents' => json_encode([
                    "KTP/Identitas Diri",
                    "Surat Permohonan dari Instansi/Universitas",
                    "Surat Keterangan Mahasiswa (jika penelitian mahasiswa)"
                ]),
                'fee' => 0.00,
                'is_active' => true
            ],
            [
                'title' => 'Data Meteorologi Real-time Komersial',
                'description' => 'Data meteorologi real-time untuk keperluan komersial seperti pertanian, perkebunan, atau industri',
                'type' => 'pnbp',
                'required_documents' => json_encode([
                    "KTP/Identitas Diri",
                    "NPWP Perusahaan",
                    "Surat Permohonan Bermaterai",
                    "Surat Izin Usaha"
                ]),
                'fee' => 500000.00,
                'is_active' => true
            ],
            [
                'title' => 'Data Geofisika Gempa',
                'description' => 'Data geofisika untuk penelitian gempa dan tsunami, monitoring seismik',
                'type' => 'non_pnbp',
                'required_documents' => json_encode([
                    "KTP/Identitas Diri",
                    "Surat Permohonan dari Institusi",
                    "Proposal Penelitian"
                ]),
                'fee' => 0.00,
                'is_active' => true
            ],
            [
                'title' => 'Data Cuaca Maritim PNBP',
                'description' => 'Data cuaca maritim untuk pelayaran komersial, pelabuhan, dan industri kelautan',
                'type' => 'pnbp',
                'required_documents' => json_encode([
                    "KTP/Identitas Diri",
                    "Surat Izin Pelayaran",
                    "NPWP",
                    "Surat Permohonan Bermaterai"
                ]),
                'fee' => 750000.00,
                'is_active' => true
            ],
            [
                'title' => 'Informasi Cuaca Instansi',
                'description' => 'Informasi cuaca untuk keperluan instansi pemerintah atau perorangan',
                'type' => 'pnbp',
                'required_documents' => json_encode([
                    "Surat Pengantar Instansi/Perorangan",
                    "KTP/Identitas Diri"
                ]),
                'fee' => 25000.00,
                'is_active' => true
            ]
        ];

        foreach ($guidelines as $guidelineData) {
            Guideline::updateOrCreate(
                ['title' => $guidelineData['title']],
                $guidelineData
            );
        }

        echo "✅ Guideline seeder completed! Created " . Guideline::count() . " guidelines.\n";
    }
}
