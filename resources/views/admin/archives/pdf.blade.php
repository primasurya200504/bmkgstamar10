<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Arsip & Laporan</title>
    <style>
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 12px;
            line-height: 1.4;
            color: #333;
            margin: 0;
            padding: 20px;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #333;
            padding-bottom: 20px;
        }
        .header h1 {
            font-size: 18px;
            font-weight: bold;
            margin: 0 0 10px 0;
            text-transform: uppercase;
        }
        .header h2 {
            font-size: 14px;
            font-weight: bold;
            margin: 0 0 5px 0;
        }
        .header p {
            margin: 2px 0;
            font-size: 11px;
        }
        .filters {
            margin-bottom: 20px;
            padding: 10px;
            background-color: #f8f9fa;
            border: 1px solid #dee2e6;
        }
        .filters h3 {
            font-size: 12px;
            font-weight: bold;
            margin: 0 0 8px 0;
        }
        .filters p {
            margin: 2px 0;
            font-size: 10px;
        }
        .summary {
            margin-bottom: 20px;
            padding: 15px;
            background-color: #e9ecef;
            border: 1px solid #ced4da;
        }
        .summary h3 {
            font-size: 13px;
            font-weight: bold;
            margin: 0 0 10px 0;
        }
        .summary-grid {
            display: table;
            width: 100%;
        }
        .summary-row {
            display: table-row;
        }
        .summary-cell {
            display: table-cell;
            padding: 5px;
            font-size: 11px;
        }
        .summary-label {
            font-weight: bold;
            width: 150px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            font-size: 10px;
        }
        th, td {
            border: 1px solid #dee2e6;
            padding: 6px;
            text-align: left;
            vertical-align: top;
        }
        th {
            background-color: #f8f9fa;
            font-weight: bold;
            font-size: 11px;
        }
        .text-center {
            text-align: center;
        }
        .text-right {
            text-align: right;
        }
        .category-badge {
            display: inline-block;
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 9px;
            font-weight: bold;
        }
        .category-pnbp {
            background-color: #d4edda;
            color: #155724;
        }
        .category-non-pnbp {
            background-color: #cce5ff;
            color: #004085;
        }
        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 10px;
            color: #666;
        }
        .page-break {
            page-break-before: always;
        }
        @media print {
            body { margin: 0; }
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Laporan Arsip & Laporan</h1>
        <h2>Badan Meteorologi, Klimatologi, dan Geofisika</h2>
        <p>Jl. Angkasa I No.2, Kemayoran, Jakarta Pusat 10720</p>
        <p>Telp: (021) 4246321 | Email: info@bmkg.go.id</p>
    </div>

    <div class="filters">
        <h3>Filter yang Diterapkan:</h3>
        @if($search)
            <p><strong>Pencarian:</strong> {{ $search }}</p>
        @endif
        @if($year)
            <p><strong>Tahun:</strong> {{ $year }}</p>
        @endif
        @if($month)
            <p><strong>Bulan:</strong> {{ date('F', mktime(0, 0, 0, $month, 1)) }}</p>
        @endif
        @if($category)
            <p><strong>Kategori:</strong> {{ $category == 'pnbp' ? 'PNBP' : 'Non-PNBP' }}</p>
        @endif
        @if(!$search && !$year && !$month && !$category)
            <p><em>Semua data (tanpa filter)</em></p>
        @endif
    </div>

    <div class="summary">
        <h3>Ringkasan Laporan</h3>
        <div class="summary-grid">
            <div class="summary-row">
                <div class="summary-cell summary-label">Total Arsip:</div>
                <div class="summary-cell">{{ $totalArchives }}</div>
            </div>
            <div class="summary-row">
                <div class="summary-cell summary-label">Total PNBP:</div>
                <div class="summary-cell">{{ $totalPnbp }}</div>
            </div>
            <div class="summary-row">
                <div class="summary-cell summary-label">Total Non-PNBP:</div>
                <div class="summary-cell">{{ $totalNonPnbp }}</div>
            </div>
            <div class="summary-row">
                <div class="summary-cell summary-label">Total Nominal PNBP:</div>
                <div class="summary-cell">Rp {{ number_format($totalAmount, 0, ',', '.') }}</div>
            </div>
            <div class="summary-row">
                <div class="summary-cell summary-label">Tanggal Laporan:</div>
                <div class="summary-cell">{{ now()->format('d/m/Y H:i:s') }}</div>
            </div>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 5%;">No</th>
                <th style="width: 15%;">No. Surat</th>
                <th style="width: 20%;">Nama User/Instansi</th>
                <th style="width: 15%;">Kategori Data</th>
                <th style="width: 15%;">Tanggal Arsip</th>
                <th style="width: 15%;">Nominal</th>
                <th style="width: 15%;">Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($allArchives as $index => $archive)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td>{{ $archive->submission_number ?? 'SUB-' . str_pad($archive->id, 4, '0', STR_PAD_LEFT) }}</td>
                    <td>{{ $archive->user->name ?? 'N/A' }}</td>
                    <td class="text-center">
                        @if($archive->guideline && $archive->guideline->type == 'pnbp')
                            <span class="category-badge category-pnbp">PNBP</span>
                        @elseif($archive->guideline && $archive->guideline->type == 'non_pnbp')
                            <span class="category-badge category-non-pnbp">Non-PNBP</span>
                        @else
                            <span class="category-badge">-</span>
                        @endif
                    </td>
                    <td>{{ $archive->updated_at ? $archive->updated_at->format('d/m/Y') : 'N/A' }}</td>
                    <td class="text-right">
                        @if($archive->guideline && $archive->guideline->type == 'pnbp' && $archive->payment)
                            Rp {{ number_format($archive->payment->amount, 0, ',', '.') }}
                        @else
                            -
                        @endif
                    </td>
                    <td class="text-center">
                        @if($archive->status == 'completed')
                            <span style="color: #28a745; font-weight: bold;">Selesai</span>
                        @else
                            <span style="color: #6c757d;">{{ ucfirst($archive->status ?? 'Unknown') }}</span>
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        <p>Laporan ini dibuat secara otomatis oleh Sistem Manajemen Pengajuan BMKG</p>
        <p>Dicetak pada: {{ now()->format('d F Y, H:i:s') }}</p>
    </div>
</body>
</html>
