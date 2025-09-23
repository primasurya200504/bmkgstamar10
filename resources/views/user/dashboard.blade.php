<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard User - BMKG Maritim Pontianak</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f3f4f6;
        }

        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0, 0, 0, 0.5);
            justify-content: center;
            align-items: center;
        }

        .modal-content {
            background-color: #fefefe;
            margin: auto;
            padding: 24px;
            border-radius: 12px;
            width: 90%;
            max-width: 700px;
            max-height: 90vh;
            overflow-y: auto;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
            cursor: pointer;
        }

        .close:hover,
        .close:focus {
            color: black;
            text-decoration: none;
        }

        .content-section {
            display: none;
        }

        .content-section.active {
            display: block;
        }

        .category-info {
            display: block;
        }

        .category-info.hidden {
            display: none;
        }

        .loading {
            opacity: 0.6;
            pointer-events: none;
        }

        .spinner {
            border: 4px solid #f3f4f6;
            border-top: 4px solid #3b82f6;
            border-radius: 50%;
            width: 20px;
            height: 20px;
            animation: spin 1s linear infinite;
            display: inline-block;
            margin-left: 8px;
        }

        @keyframes spin {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
        }

        .nav-active {
            background-color: #4f46e5 !important;
            color: white !important;
        }

        .file-drop-area {
            border: 2px dashed #d1d5db;
            border-radius: 8px;
            padding: 20px;
            text-align: center;
            transition: all 0.3s ease;
            background-color: #fafafa;
        }

        .file-drop-area:hover {
            border-color: #4f46e5;
            background-color: #f0f9ff;
        }

        .status-badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 9999px;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
        }

        .preset-button {
            transition: all 0.2s ease;
        }

        .preset-button:hover {
            transform: translateY(-1px);
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        /* ADDED: Custom date input untuk free selection */
        .free-date-input {
            position: relative;
        }

        .free-date-input input[type="date"] {
            /* Remove browser restrictions for date input */
            appearance: none;
            -webkit-appearance: none;
            -moz-appearance: none;
        }

        .date-info-badge {
            display: inline-block;
            padding: 2px 6px;
            border-radius: 4px;
            font-size: 0.625rem;
            font-weight: 500;
        }

        .historical-data {
            background-color: #eff6ff;
            color: #1d4ed8;
        }

        .recent-data {
            background-color: #f0fdf4;
            color: #16a34a;
        }

        .future-projection {
            background-color: #fefce8;
            color: #ca8a04;
        }
    </style>
</head>

<body class="flex min-h-screen">
    <!-- Sidebar -->
    <aside class="w-64 bg-white shadow-lg p-6 flex flex-col justify-between rounded-r-2xl">
        <div>
            <div class="flex items-center mb-10">
                <div class="w-10 h-10 bg-indigo-600 rounded-full flex items-center justify-center mr-3">
                    <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <h1 class="text-xl font-bold text-gray-800">BMKG Pontianak</h1>
            </div>

            <nav class="space-y-4">
                <a href="#dashboard" id="nav-dashboard"
                    class="flex items-center p-3 text-gray-600 hover:text-white hover:bg-indigo-600 rounded-lg transition-colors duration-200 nav-active">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2-2m-2 2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6">
                        </path>
                    </svg>
                    Dasbor
                </a>

                <a href="#pengajuan" id="nav-pengajuan"
                    class="flex items-center p-3 text-gray-600 hover:text-white hover:bg-indigo-600 rounded-lg transition-colors duration-200">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 13h6m-3-3v6m5 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                        </path>
                    </svg>
                    Pengajuan Surat
                </a>

                <a href="#panduan" id="nav-panduan"
                    class="flex items-center p-3 text-gray-600 hover:text-white hover:bg-indigo-600 rounded-lg transition-colors duration-200">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 6.253v13m0-13C10.832 5.468 9.587 5.097 8.323 5.097a2.796 2.796 0 00-.777.106V5.344a.796.796 0 00-.518-.755C6.012 4.382 4.67 4.195 3.328 4.195A2.796 2.796 0 00.552 4.41l.019.019V6.44c.54.496 1.15.828 1.83 1.012.68.184 1.41.282 2.16.282 1.342 0 2.684-.187 4.026-.563a.796.796 0 00.518-.755V5.344a.796.796 0 00.518-.755z">
                        </path>
                    </svg>
                    Panduan Surat/Data
                </a>

                <a href="#profile" id="nav-profile"
                    class="flex items-center p-3 text-gray-600 hover:text-white hover:bg-indigo-600 rounded-lg transition-colors duration-200">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                    </svg>
                    Profil
                </a>
            </nav>
        </div>

        <div class="mt-auto">
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit"
                    class="flex items-center w-full p-3 text-white bg-red-600 hover:bg-red-700 rounded-lg transition-colors duration-200">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1">
                        </path>
                    </svg>
                    Logout
                </button>
            </form>
        </div>
    </aside>

    <!-- Main Content -->
    <main class="flex-1 p-8 overflow-y-auto">
        <header class="flex justify-between items-center mb-8">
            <h2 class="text-3xl font-bold text-gray-800">Selamat datang, {{ Auth::user()->name }}!</h2>
            <div class="flex items-center space-x-4">
                <span class="text-gray-600">{{ ucfirst(Auth::user()->role) }}</span>
                <div class="w-10 h-10 bg-indigo-600 rounded-full flex items-center justify-center text-white font-bold">
                    {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                </div>
            </div>
        </header>

        @if (session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-6"
                role="alert">
                <span class="block sm:inline">{{ session('success') }}</span>
            </div>
        @endif

        <!-- Dashboard Section -->
        <section id="dashboard" class="content-section active">
            <div class="bg-white p-8 rounded-lg shadow-md">
                <h3 class="text-2xl font-bold mb-4">Dasbor</h3>
                <p class="text-gray-600 mb-6">Berikut adalah riwayat pengajuan surat/data Anda.</p>

                <!-- Stats Cards -->
                <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
                    <div class="bg-yellow-50 p-4 rounded-lg border border-yellow-200">
                        <div class="flex items-center">
                            <div class="w-8 h-8 bg-yellow-100 rounded-full flex items-center justify-center mr-3">
                                <svg class="w-4 h-4 text-yellow-600" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z"
                                        clip-rule="evenodd" />
                                </svg>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-yellow-800">Menunggu</p>
                                <p class="text-2xl font-bold text-yellow-900">{{ $stats['pending'] }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-blue-50 p-4 rounded-lg border border-blue-200">
                        <div class="flex items-center">
                            <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center mr-3">
                                <svg class="w-4 h-4 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M4 2a1 1 0 011 1v2.101a7.002 7.002 0 0111.601 2.566 1 1 0 11-1.885.666A5.002 5.002 0 005.999 7H9a1 1 0 010 2H4a1 1 0 01-1-1V3a1 1 0 011-1zm.008 9.057a1 1 0 011.276.61A5.002 5.002 0 0014.001 13H11a1 1 0 110-2h5a1 1 0 011 1v5a1 1 0 11-2 0v-2.101a7.002 7.002 0 01-11.601-2.566 1 1 0 01.61-1.276z"
                                        clip-rule="evenodd" />
                                </svg>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-blue-800">Diproses</p>
                                <p class="text-2xl font-bold text-blue-900">{{ $stats['in_process'] }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-green-50 p-4 rounded-lg border border-green-200">
                        <div class="flex items-center">
                            <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center mr-3">
                                <svg class="w-4 h-4 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                        clip-rule="evenodd" />
                                </svg>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-green-800">Selesai</p>
                                <p class="text-2xl font-bold text-green-900">{{ $stats['completed'] }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-red-50 p-4 rounded-lg border border-red-200">
                        <div class="flex items-center">
                            <div class="w-8 h-8 bg-red-100 rounded-full flex items-center justify-center mr-3">
                                <svg class="w-4 h-4 text-red-600" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                                        clip-rule="evenodd" />
                                </svg>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-red-800">Ditolak</p>
                                <p class="text-2xl font-bold text-red-900">{{ $stats['rejected'] }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Applications Table -->
                <div class="overflow-x-auto">
                    <table class="min-w-full bg-white rounded-lg shadow-sm">
                        <thead>
                            <tr class="bg-gray-200 text-left text-sm font-semibold text-gray-700">
                                <th class="py-3 px-4 rounded-tl-lg">No. Surat</th>
                                <th class="py-3 px-4">Tanggal Pengajuan</th>
                                <th class="py-3 px-4">Jenis Data</th>
                                <th class="py-3 px-4">Kategori</th>
                                <th class="py-3 px-4">Status</th>
                                <th class="py-3 px-4 rounded-tr-lg">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="text-gray-600 text-sm font-normal">
                            @forelse ($applications as $application)
                                <tr class="border-b border-gray-200 hover:bg-gray-50">
                                    <td class="py-3 px-4 font-medium">{{ $application->application_number }}</td>
                                    <td class="py-3 px-4">{{ $application->created_at->format('d/m/Y H:i') }}</td>
                                    <td class="py-3 px-4">{{ $application->guideline->title }}</td>
                                    <td class="py-3 px-4">
                                        <span
                                            class="px-2 py-1 text-xs font-semibold rounded-full {{ $application->type === 'pnbp' ? 'bg-purple-100 text-purple-800' : 'bg-blue-100 text-blue-800' }}">
                                            {{ $application->type === 'pnbp' ? 'PNBP' : 'Non-PNBP' }}
                                        </span>
                                    </td>
                                    <td class="py-3 px-4">
                                        @php
                                            $statusConfig = [
                                                'pending' => [
                                                    'class' => 'bg-yellow-100 text-yellow-700',
                                                    'text' => 'Menunggu',
                                                ],
                                                'verified' => [
                                                    'class' => 'bg-blue-100 text-blue-700',
                                                    'text' => 'Terverifikasi',
                                                ],
                                                'payment_pending' => [
                                                    'class' => 'bg-orange-100 text-orange-700',
                                                    'text' => 'Menunggu Pembayaran',
                                                ],
                                                'paid' => [
                                                    'class' => 'bg-purple-100 text-purple-700',
                                                    'text' => 'Sudah Bayar',
                                                ],
                                                'processing' => [
                                                    'class' => 'bg-indigo-100 text-indigo-700',
                                                    'text' => 'Diproses',
                                                ],
                                                'completed' => [
                                                    'class' => 'bg-green-100 text-green-700',
                                                    'text' => 'Selesai',
                                                ],
                                                'rejected' => [
                                                    'class' => 'bg-red-100 text-red-700',
                                                    'text' => 'Ditolak',
                                                ],
                                            ];
                                            $config = $statusConfig[$application->status] ?? [
                                                'class' => 'bg-gray-100 text-gray-700',
                                                'text' => ucfirst($application->status),
                                            ];
                                        @endphp
                                        <span
                                            class="{{ $config['class'] }} font-medium py-1 px-3 rounded-full">{{ $config['text'] }}</span>
                                    </td>
                                    <td class="py-3 px-4 space-x-2">
                                        @if ($application->status === 'completed' && $application->generatedDocuments->count() > 0)
                                            @foreach ($application->generatedDocuments as $document)
                                                <a href="{{ route('user.download-document', $document->id) }}"
                                                    class="inline-flex items-center px-3 py-1 bg-green-500 hover:bg-green-600 text-white text-xs rounded-lg transition-colors">
                                                    📥 Unduh Data
                                                </a>
                                            @endforeach
                                        @elseif (
                                            $application->status === 'payment_pending' &&
                                                $application->type === 'pnbp' &&
                                                (!$application->payment || !$application->payment->payment_proof))
                                            <button onclick="showPaymentModal({{ $application->id }})"
                                                class="inline-flex items-center px-3 py-1 bg-orange-500 hover:bg-orange-600 text-white text-xs rounded-lg transition-colors">
                                                💳 Upload Bayar
                                            </button>
                                        @elseif ($application->status === 'rejected')
                                            <button onclick="showEditModal({{ json_encode($application) }})"
                                                class="inline-flex items-center px-3 py-1 bg-gray-500 hover:bg-gray-600 text-white text-xs rounded-lg transition-colors">
                                                ✏️ Edit
                                            </button>
                                        @else
                                            <span class="text-gray-400 text-xs">-</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="py-8 px-4 text-center text-gray-500">
                                        <div class="flex flex-col items-center">
                                            <svg class="w-12 h-12 text-gray-300 mb-2" fill="none"
                                                stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M9 12h6m-6-4h6m2 5l-5 5-5-5 5-5z"></path>
                                            </svg>
                                            <p>Anda belum memiliki riwayat pengajuan.</p>
                                            <p class="text-sm">Mulai dengan mengajukan permintaan data pertama Anda.
                                            </p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </section>

        <!-- Pengajuan Section -->
        <section id="pengajuan" class="content-section">
            <div class="bg-white p-8 rounded-lg shadow-md">
                <h3 class="text-2xl font-bold mb-4">Formulir Pengajuan Surat/Data</h3>

                <!-- UPDATED: Enhanced Info Periode Data -->
                <div class="mb-6 p-4 bg-blue-50 border border-blue-200 rounded-lg">
                    <h4 class="font-medium text-blue-800 mb-2">📅 Info Pemilihan Tanggal:</h4>
                    <ul class="text-sm text-blue-700 space-y-1">
                        <li>• <strong>Bebas memilih tanggal:</strong> Dari tahun 2000 hingga 2100</li>
                        <li>• <strong>Data Historis:</strong> Tersedia dari tahun 1990 (data observasi)</li>
                        <li>• <strong>Data Real-time:</strong> Data terkini sampai hari ini</li>
                        <li>• <strong>Proyeksi Data:</strong> Untuk keperluan penelitian dan modeling</li>
                        <li>• <strong>Catatan:</strong> Ketersediaan data akan dikonfirmasi oleh admin</li>
                    </ul>
                </div>

                <p class="text-sm text-gray-500 mb-4">
                    Tidak punya surat pengantar? Unduh contohnya di sini:
                </p>

                <!-- Template Downloads -->
                <div class="mb-6">
                    <h4 class="font-semibold mb-2">Tabel Unduhan Surat Pengantar</h4>
                    <div class="overflow-x-auto">
                        <table class="min-w-full bg-white border border-gray-200 rounded-lg">
                            <thead>
                                <tr class="bg-gray-100 text-sm">
                                    <th class="py-2 px-4 border-b text-left">Jenis Surat</th>
                                    <th class="py-2 px-4 border-b text-left">Keterangan</th>
                                    <th class="py-2 px-4 border-b text-left">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr class="hover:bg-gray-50">
                                    <td class="py-2 px-4 border-b">Surat Pengantar Umum</td>
                                    <td class="py-2 px-4 border-b">Untuk pengajuan data secara umum atau keperluan
                                        pribadi.</td>
                                    <td class="py-2 px-4 border-b">
                                        <a href="#" class="text-blue-600 hover:underline font-medium">Unduh
                                            .docx</a>
                                    </td>
                                </tr>
                                <tr class="hover:bg-gray-50">
                                    <td class="py-2 px-4 border-b">Surat Pengantar Penelitian</td>
                                    <td class="py-2 px-4 border-b">Khusus untuk mahasiswa atau peneliti.</td>
                                    <td class="py-2 px-4 border-b">
                                        <a href="#" class="text-blue-600 hover:underline font-medium">Unduh
                                            .docx</a>
                                    </td>
                                </tr>
                                <tr class="hover:bg-gray-50">
                                    <td class="py-2 px-4 border-b">Surat Pengantar Instansi</td>
                                    <td class="py-2 px-4 border-b">Untuk pengajuan resmi dari instansi
                                        pemerintah/swasta.</td>
                                    <td class="py-2 px-4 border-b">
                                        <a href="#" class="text-blue-600 hover:underline font-medium">Unduh
                                            .docx</a>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Category Selection -->
                <div class="mb-6">
                    <h4 class="font-semibold text-gray-700 mb-2">Kategori Pengajuan</h4>
                    <div class="flex space-x-4">
                        <button type="button" id="btn-pnbp"
                            class="px-6 py-2 rounded-lg bg-indigo-600 text-white font-semibold shadow-md transition-colors duration-200 hover:bg-indigo-700">
                            PNBP
                            <span class="ml-2 text-xs bg-white bg-opacity-20 px-2 py-1 rounded">(1 Dokumen)</span>
                        </button>
                        <button type="button" id="btn-nonpnbp"
                            class="px-6 py-2 rounded-lg bg-gray-200 text-gray-700 font-semibold shadow-md transition-colors duration-200 hover:bg-gray-300">
                            Non-PNBP
                            <span class="ml-2 text-xs bg-gray-400 bg-opacity-50 px-2 py-1 rounded">(4 Dokumen)</span>
                        </button>
                    </div>

                    <!-- Category Information -->
                    <div id="category-info" class="mt-4 p-4 bg-gray-50 rounded-lg">
                        <div id="pnbp-info" class="category-info">
                            <h5 class="font-semibold text-gray-700 mb-2">Persyaratan PNBP:</h5>
                            <ul class="text-sm text-gray-600 list-disc list-inside space-y-1">
                                <li><strong>1 Dokumen:</strong> Surat Pengantar dari Instansi/Organisasi</li>
                                <li><strong>Jenis Data:</strong> Semua jenis data meteorologi yang tersedia</li>
                                <li><strong>Biaya:</strong> Sesuai tarif PNBP yang berlaku</li>
                                <li><strong>Penggunaan:</strong> Keperluan umum atau komersial</li>
                            </ul>
                        </div>

                        <div id="nonpnbp-info" class="category-info hidden">
                            <h5 class="font-semibold text-gray-700 mb-2">Persyaratan Non-PNBP:</h5>
                            <ul class="text-sm text-gray-600 list-disc list-inside space-y-1">
                                <li><strong>Jenis Data:</strong> Semua jenis data meteorologi yang tersedia (sama dengan
                                    PNBP)</li>
                                <li><strong>4 Dokumen yang diperlukan:</strong></li>
                                <li class="ml-4">1. Surat Pengantar dari Universitas/Institusi Penelitian</li>
                                <li class="ml-4">2. Dokumen Proposal/Karya Ilmiah</li>
                                <li class="ml-4">3. Dokumen Pendukung Penelitian</li>
                                <li class="ml-4">4. Dokumen Pendukung Tambahan (opsional)</li>
                                <li><strong>Biaya:</strong> Gratis untuk penelitian/akademik</li>
                                <li><strong>Penggunaan:</strong> Khusus penelitian dan akademik</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Application Form -->
                <form id="submission-form" action="{{ route('user.submit-application') }}" method="POST"
                    enctype="multipart/form-data" class="space-y-6">
                    @csrf
                    <input type="hidden" name="type" id="kategori_input" value="pnbp">
                    <input type="hidden" name="guideline_id" id="guideline_id_input">

                    <div>
                        <label for="jenis_data" class="block text-gray-700 font-semibold mb-2">Jenis Data yang
                            Diajukan</label>
                        <select id="jenis_data" name="jenis_data"
                            class="w-full p-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500"
                            required>
                            <option value="">Pilih Jenis Data</option>
                        </select>
                    </div>

                    <!-- UPDATED: FREE DATE SELECTION -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="free-date-input">
                            <label for="tanggal_mulai" class="block text-gray-700 font-semibold mb-2">
                                Tanggal Mulai Data <span class="text-red-500">*</span>
                            </label>
                            <input type="date" id="tanggal_mulai" name="tanggal_mulai"
                                class="w-full p-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500"
                                required>
                            <div class="flex items-center justify-between mt-1">
                                <p class="text-xs text-gray-500">Bebas pilih tanggal kapan saja</p>
                                <span id="start-date-type" class="date-info-badge"></span>
                            </div>
                        </div>
                        <div class="free-date-input">
                            <label for="tanggal_selesai" class="block text-gray-700 font-semibold mb-2">
                                Tanggal Selesai Data <span class="text-red-500">*</span>
                            </label>
                            <input type="date" id="tanggal_selesai" name="tanggal_selesai"
                                class="w-full p-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500"
                                required>
                            <div class="flex items-center justify-between mt-1">
                                <p class="text-xs text-gray-500">Harus sama atau setelah tanggal mulai</p>
                                <span id="end-date-type" class="date-info-badge"></span>
                            </div>
                        </div>
                    </div>

                    <!-- UPDATED: Enhanced Date Presets -->
                    <div id="date-presets" class="flex flex-wrap gap-2 mb-4">
                        <p class="w-full text-sm text-gray-600 mb-2">Preset tanggal populer:</p>
                        <button type="button" onclick="setDatePreset('yesterday')"
                            class="preset-button px-3 py-1 text-xs bg-green-200 hover:bg-green-300 rounded-lg transition-colors">
                            Kemarin
                        </button>
                        <button type="button" onclick="setDatePreset('last-week')"
                            class="preset-button px-3 py-1 text-xs bg-green-200 hover:bg-green-300 rounded-lg transition-colors">
                            Minggu Lalu
                        </button>
                        <button type="button" onclick="setDatePreset('last-month')"
                            class="preset-button px-3 py-1 text-xs bg-green-200 hover:bg-green-300 rounded-lg transition-colors">
                            Bulan Lalu
                        </button>
                        <button type="button" onclick="setDatePreset('last-year')"
                            class="preset-button px-3 py-1 text-xs bg-gray-200 hover:bg-gray-300 rounded-lg transition-colors">
                            Tahun Lalu
                        </button>
                        <button type="button" onclick="setDatePreset('last-5-years')"
                            class="preset-button px-3 py-1 text-xs bg-gray-200 hover:bg-gray-300 rounded-lg transition-colors">
                            5 Tahun Terakhir
                        </button>
                        <button type="button" onclick="setDatePreset('last-10-years')"
                            class="preset-button px-3 py-1 text-xs bg-gray-200 hover:bg-gray-300 rounded-lg transition-colors">
                            10 Tahun Terakhir
                        </button>
                        <button type="button" onclick="setDatePreset('historical')"
                            class="preset-button px-3 py-1 text-xs bg-blue-200 hover:bg-blue-300 rounded-lg transition-colors">
                            Data Historis (Sejak 2000)
                        </button>
                        <button type="button" onclick="setDatePreset('custom-historical')"
                            class="preset-button px-3 py-1 text-xs bg-purple-200 hover:bg-purple-300 rounded-lg transition-colors">
                            Custom Historical
                        </button>
                    </div>

                    <div>
                        <label for="keperluan" class="block text-gray-700 font-semibold mb-2">Keperluan Penggunaan
                            Data</label>
                        <textarea id="keperluan" name="keperluan" rows="4"
                            placeholder="Jelaskan secara detail keperluan dan tujuan penggunaan data meteorologi yang diminta..."
                            class="w-full p-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500"
                            required></textarea>
                    </div>

                    <!-- Upload Documents Section -->
                    <div id="file-upload-section">
                        <h4 class="font-semibold text-gray-700 mb-4">Upload Dokumen Persyaratan</h4>
                        <div id="document-uploads">
                            <!-- Documents will be dynamically generated here based on category -->
                        </div>
                    </div>

                    <button type="submit" id="submit-btn"
                        class="w-full p-3 bg-indigo-600 text-white font-bold rounded-lg hover:bg-indigo-700 transition-colors duration-200 flex items-center justify-center">
                        <span id="submit-text">Ajukan Surat</span>
                        <div id="submit-spinner" class="spinner hidden"></div>
                    </button>
                </form>
            </div>
        </section>

        <!-- Panduan Section -->
        <section id="panduan" class="content-section">
            <div class="bg-white p-8 rounded-lg shadow-md">
                <h3 class="text-2xl font-bold mb-4">Panduan Pengajuan Surat/Data</h3>
                <p class="text-gray-600 mb-6">Klik pada jenis data di bawah ini untuk melihat detail, contoh, dan
                    syarat pengajuannya.</p>

                <div id="accordion-container" class="space-y-4">
                    <!-- Guidelines will be loaded here -->
                </div>
            </div>
        </section>

        <!-- Profile Section -->
        <section id="profile" class="content-section">
            <div class="bg-white p-8 rounded-lg shadow-md">
                <h3 class="text-2xl font-bold mb-4">Profil Pengguna</h3>
                <form id="profile-form" class="space-y-6">
                    @csrf
                    <div>
                        <label for="profile_name" class="block text-gray-700 font-semibold mb-2">Nama Lengkap</label>
                        <input type="text" id="profile_name" name="name" value="{{ Auth::user()->name }}"
                            class="w-full p-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500"
                            required>
                    </div>
                    <div>
                        <label for="profile_email" class="block text-gray-700 font-semibold mb-2">Email</label>
                        <input type="email" id="profile_email" name="email" value="{{ Auth::user()->email }}"
                            class="w-full p-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500"
                            required>
                    </div>
                    <div>
                        <label for="profile_phone" class="block text-gray-700 font-semibold mb-2">Nomor
                            Telepon</label>
                        <input type="text" id="profile_phone" name="phone"
                            value="{{ Auth::user()->phone ?? '' }}"
                            class="w-full p-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    </div>
                    <button type="submit"
                        class="w-full p-3 bg-indigo-600 text-white font-bold rounded-lg hover:bg-indigo-700 transition-colors duration-200">
                        Update Profil
                    </button>
                </form>
            </div>
        </section>
    </main>

    <!-- Payment Modal -->
    <div id="paymentModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closePaymentModal()">&times;</span>
            <h4 class="text-xl font-bold mb-4">Upload Bukti Pembayaran</h4>
            <form id="paymentForm" class="space-y-4" method="POST" enctype="multipart/form-data">
                @csrf
                <input type="hidden" id="payment_application_id" name="application_id">
                <div>
                    <label for="payment_proof" class="block text-gray-700 font-semibold mb-2">Bukti Pembayaran</label>
                    <input type="file" id="payment_proof" name="payment_proof" accept="image/*,application/pdf"
                        class="w-full p-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500"
                        required>
                    <p class="text-sm text-gray-500 mt-1">Format: JPG, PNG, PDF (Max: 2MB)</p>
                </div>
                <button type="submit"
                    class="w-full p-3 bg-indigo-600 text-white font-bold rounded-lg hover:bg-indigo-700 transition-colors duration-200">
                    Upload Bukti Pembayaran
                </button>
            </form>
        </div>
    </div>

    <!-- Edit Modal -->
    <div id="editModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeEditModal()">&times;</span>
            <h4 class="text-xl font-bold mb-4">Edit Pengajuan</h4>
            <form id="editForm" class="space-y-4" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <input type="hidden" id="edit_application_id" name="application_id">
                <div>
                    <label for="edit_no_surat" class="block text-gray-700 font-semibold mb-2">No. Surat</label>
                    <input type="text" id="edit_no_surat"
                        class="w-full p-3 border border-gray-300 rounded-lg bg-gray-100" readonly>
                </div>
                <div>
                    <label for="edit_jenis_data" class="block text-gray-700 font-semibold mb-2">Jenis Data</label>
                    <select id="edit_jenis_data" name="jenis_data"
                        class="w-full p-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        <!-- Options will be populated -->
                    </select>
                </div>
                <div>
                    <label for="edit_keperluan" class="block text-gray-700 font-semibold mb-2">Keperluan</label>
                    <textarea id="edit_keperluan" name="keperluan" rows="4"
                        class="w-full p-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500"></textarea>
                </div>
                <button type="submit"
                    class="w-full p-3 bg-indigo-600 text-white font-bold rounded-lg hover:bg-indigo-700 transition-colors duration-200">
                    Simpan Perubahan
                </button>
            </form>
        </div>
    </div>

    <script>
        let currentGuidelines = [];

        document.addEventListener('DOMContentLoaded', () => {
            const navLinks = document.querySelectorAll('a[id^="nav-"]');
            const sections = document.querySelectorAll('.content-section');

            // Function to show the correct section and update the active link
            const showSection = (id) => {
                sections.forEach(section => {
                    section.classList.remove('active');
                });
                const targetSection = document.getElementById(id);
                if (targetSection) {
                    targetSection.classList.add('active');
                }

                navLinks.forEach(link => {
                    link.classList.remove('nav-active');
                });
                const targetNavLink = document.getElementById(`nav-${id}`);
                if (targetNavLink) {
                    targetNavLink.classList.add('nav-active');
                }
            };

            // Event listener for navigation links
            navLinks.forEach(link => {
                link.addEventListener('click', (e) => {
                    e.preventDefault();
                    const targetId = e.currentTarget.getAttribute('href').substring(1);
                    showSection(targetId);

                    // Load specific content for certain sections
                    if (targetId === 'panduan') {
                        loadGuidelines();
                    }
                });
            });

            // Initial section display
            showSection('dashboard');

            // Initialize form handlers
            initializeFormHandlers();
            loadGuidelinesForForm();

            // UPDATED: Remove date restrictions - let users pick ANY date
            const tanggalMulai = document.getElementById('tanggal_mulai');
            const tanggalSelesai = document.getElementById('tanggal_selesai');

            // Remove min/max restrictions to allow free selection
            if (tanggalMulai) {
                // Allow any date from 1900 to 2100
                tanggalMulai.removeAttribute('min');
                tanggalMulai.removeAttribute('max');
            }

            if (tanggalSelesai) {
                tanggalSelesai.removeAttribute('min');
                tanggalSelesai.removeAttribute('max');
            }

            // Update end date minimum when start date changes (logical constraint only)
            if (tanggalMulai) {
                tanggalMulai.addEventListener('change', function() {
                    validateDateRange();
                    updateDateTypeBadge();
                });
            }

            if (tanggalSelesai) {
                tanggalSelesai.addEventListener('change', function() {
                    validateDateRange();
                    updateDateTypeBadge();
                });
            }
        });

        // UPDATED: Enhanced date validation (warning only, not blocking)
        function validateDateRange() {
            const tanggalMulai = document.getElementById('tanggal_mulai');
            const tanggalSelesai = document.getElementById('tanggal_selesai');

            const startDate = tanggalMulai ? tanggalMulai.value : null;
            const endDate = tanggalSelesai ? tanggalSelesai.value : null;

            if (startDate && endDate) {
                const start = new Date(startDate);
                const end = new Date(endDate);
                const diffDays = Math.ceil((end - start) / (1000 * 60 * 60 * 24));

                // Remove existing warnings
                const existingWarning = document.getElementById('date-range-warning');
                if (existingWarning) existingWarning.remove();

                // Logical validation: end date must be >= start date
                if (end < start) {
                    showDateWarning('error', 'Tanggal selesai harus sama atau setelah tanggal mulai!');
                    return false;
                }

                // Show info for very long ranges (informational only)
                if (diffDays > 3650) { // 10 years
                    showDateWarning('info',
                        `Rentang waktu sangat panjang (${Math.ceil(diffDays/365)} tahun). Proses pengolahan data mungkin memerlukan waktu lebih lama.`
                        );
                }

                // Show info for future dates
                const today = new Date();
                if (start > today || end > today) {
                    showDateWarning('info',
                        'Anda memilih tanggal masa depan. Admin akan mengkonfirmasi ketersediaan data proyeksi.');
                }
            }

            return true;
        }

        function showDateWarning(type, message) {
            const warningDiv = document.createElement('div');
            warningDiv.id = 'date-range-warning';

            if (type === 'error') {
                warningDiv.className = 'mt-2 p-3 bg-red-50 border border-red-200 rounded-lg';
                warningDiv.innerHTML = `
                    <p class="text-red-700 text-sm">
                        <svg class="w-4 h-4 inline mr-1" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                        </svg>
                        ${message}
                    </p>
                `;
            } else {
                warningDiv.className = 'mt-2 p-3 bg-yellow-50 border border-yellow-200 rounded-lg';
                warningDiv.innerHTML = `
                    <p class="text-yellow-700 text-sm">
                        <svg class="w-4 h-4 inline mr-1" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                        </svg>
                        ${message}
                    </p>
                `;
            }

            // Insert after date inputs
            const tanggalSelesai = document.getElementById('tanggal_selesai');
            if (tanggalSelesai && tanggalSelesai.parentNode && tanggalSelesai.parentNode.parentNode) {
                tanggalSelesai.parentNode.parentNode.appendChild(warningDiv);
            }
        }

        // ADDED: Update date type badges
        function updateDateTypeBadge() {
            const tanggalMulai = document.getElementById('tanggal_mulai');
            const tanggalSelesai = document.getElementById('tanggal_selesai');
            const startBadge = document.getElementById('start-date-type');
            const endBadge = document.getElementById('end-date-type');

            if (!tanggalMulai || !tanggalSelesai || !startBadge || !endBadge) return;

            const today = new Date();
            const startDate = tanggalMulai.value ? new Date(tanggalMulai.value) : null;
            const endDate = tanggalSelesai.value ? new Date(tanggalSelesai.value) : null;

            // Update start date badge
            if (startDate) {
                if (startDate > today) {
                    startBadge.textContent = 'Proyeksi';
                    startBadge.className = 'date-info-badge future-projection';
                } else if (startDate >= new Date('1990-01-01')) {
                    startBadge.textContent = 'Data Tersedia';
                    startBadge.className = 'date-info-badge recent-data';
                } else {
                    startBadge.textContent = 'Data Historis';
                    startBadge.className = 'date-info-badge historical-data';
                }
            }

            // Update end date badge
            if (endDate) {
                if (endDate > today) {
                    endBadge.textContent = 'Proyeksi';
                    endBadge.className = 'date-info-badge future-projection';
                } else if (endDate >= new Date('1990-01-01')) {
                    endBadge.textContent = 'Data Tersedia';
                    endBadge.className = 'date-info-badge recent-data';
                } else {
                    endBadge.textContent = 'Data Historis';
                    endBadge.className = 'date-info-badge historical-data';
                }
            }
        }

        // UPDATED: Enhanced preset functions with more options
        function setDatePreset(preset) {
            const tanggalMulai = document.getElementById('tanggal_mulai');
            const tanggalSelesai = document.getElementById('tanggal_selesai');
            const today = new Date();

            let startDate, endDate;

            switch (preset) {
                case 'yesterday':
                    startDate = new Date(today);
                    startDate.setDate(today.getDate() - 1);
                    endDate = new Date(startDate);
                    break;
                case 'last-week':
                    endDate = new Date(today);
                    endDate.setDate(today.getDate() - 1);
                    startDate = new Date(endDate);
                    startDate.setDate(endDate.getDate() - 6);
                    break;
                case 'last-month':
                    endDate = new Date(today);
                    endDate.setDate(today.getDate() - 1);
                    startDate = new Date(endDate);
                    startDate.setMonth(endDate.getMonth() - 1);
                    break;
                case 'last-year':
                    startDate = new Date(today.getFullYear() - 1, 0, 1); // 1 Jan tahun lalu
                    endDate = new Date(today.getFullYear() - 1, 11, 31); // 31 Des tahun lalu
                    break;
                case 'last-5-years':
                    startDate = new Date(today.getFullYear() - 5, 0, 1);
                    endDate = new Date(today.getFullYear() - 1, 11, 31);
                    break;
                case 'last-10-years':
                    startDate = new Date(today.getFullYear() - 10, 0, 1);
                    endDate = new Date(today.getFullYear() - 1, 11, 31);
                    break;
                case 'historical':
                    startDate = new Date(2000, 0, 1); // 1 Jan 2000
                    endDate = new Date(2023, 11, 31); // 31 Des 2023
                    break;
                case 'custom-historical':
                    // Open custom picker
                    const userStart = prompt('Masukkan tanggal mulai (YYYY-MM-DD):', '1990-01-01');
                    const userEnd = prompt('Masukkan tanggal selesai (YYYY-MM-DD):', '2000-12-31');
                    if (userStart && userEnd) {
                        startDate = new Date(userStart);
                        endDate = new Date(userEnd);
                    } else {
                        return;
                    }
                    break;
                default:
                    return;
            }

            if (tanggalMulai && tanggalSelesai) {
                tanggalMulai.value = startDate.toISOString().split('T')[0];
                tanggalSelesai.value = endDate.toISOString().split('T')[0];

                // Trigger change events
                tanggalMulai.dispatchEvent(new Event('change'));
                tanggalSelesai.dispatchEvent(new Event('change'));

                // Show confirmation
                const diffDays = Math.ceil((endDate - startDate) / (1000 * 60 * 60 * 24)) + 1;
                console.log(`Preset applied: ${preset} (${diffDays} hari)`);
            }
        }

        function initializeFormHandlers() {
            const pnbpBtn = document.getElementById('btn-pnbp');
            const nonpnbpBtn = document.getElementById('btn-nonpnbp');

            pnbpBtn.addEventListener('click', () => {
                setCategory('pnbp', pnbpBtn, nonpnbpBtn);
            });

            nonpnbpBtn.addEventListener('click', () => {
                setCategory('non_pnbp', nonpnbpBtn, pnbpBtn);
            });

            // Form submission
            document.getElementById('submission-form').addEventListener('submit', async (e) => {
                e.preventDefault();

                // Validate date range before submission
                if (!validateDateRange()) {
                    alert('Mohon perbaiki tanggal sebelum melanjutkan.');
                    return;
                }

                await submitApplication(e.target);
            });

            // Profile form submission
            document.getElementById('profile-form').addEventListener('submit', async (e) => {
                e.preventDefault();
                await updateProfile(e.target);
            });

            // Payment form submission
            document.getElementById('paymentForm').addEventListener('submit', async (e) => {
                e.preventDefault();
                await submitPayment(e.target);
            });

            // Set initial category
            setCategory('pnbp', pnbpBtn, nonpnbpBtn);
        }

        function setCategory(category, activeBtn, inactiveBtn) {
            document.getElementById('kategori_input').value = category;

            activeBtn.classList.add('bg-indigo-600', 'text-white');
            activeBtn.classList.remove('bg-gray-200', 'text-gray-700');
            inactiveBtn.classList.remove('bg-indigo-600', 'text-white');
            inactiveBtn.classList.add('bg-gray-200', 'text-gray-700');

            // Show/hide category info
            const pnbpInfo = document.getElementById('pnbp-info');
            const nonpnbpInfo = document.getElementById('nonpnbp-info');

            if (category === 'pnbp') {
                pnbpInfo.classList.remove('hidden');
                nonpnbpInfo.classList.add('hidden');
            } else {
                pnbpInfo.classList.add('hidden');
                nonpnbpInfo.classList.remove('hidden');
            }

            // Update document uploads based on category
            updateDocumentUploadsByCategory(category);

            // Update fee display for selected guideline if any
            updateFeeDisplayForCategory(category);
        }

        function updateDocumentUploadsByCategory(category) {
            const uploadsContainer = document.getElementById('document-uploads');
            uploadsContainer.innerHTML = '';

            if (category === 'pnbp') {
                // PNBP: Only 1 document required
                uploadsContainer.innerHTML = `
                    <div class="mb-4">
                        <label for="document_0" class="block text-gray-700 font-semibold mb-2">
                            <span class="text-red-500">*</span> Surat Pengantar dari Instansi/Organisasi
                        </label>
                        <div class="file-drop-area">
                            <input type="file" id="document_0" name="documents[0]" 
                                accept=".pdf,.jpg,.jpeg,.png,.doc,.docx"
                                class="w-full p-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500" required>
                        </div>
                        <p class="text-xs text-gray-500 mt-1">Format: PDF, DOC, DOCX, JPG, PNG (Max: 5MB)</p>
                    </div>
                `;
            } else {
                // Non-PNBP: 4 documents (3 required, 1 optional)
                const documents = [{
                        name: 'Surat Pengantar dari Universitas/Institusi Penelitian',
                        required: true
                    },
                    {
                        name: 'Dokumen Proposal/Karya Ilmiah',
                        required: true
                    },
                    {
                        name: 'Dokumen Pendukung Penelitian',
                        required: true
                    },
                    {
                        name: 'Dokumen Pendukung Tambahan',
                        required: false
                    }
                ];

                documents.forEach((doc, index) => {
                    uploadsContainer.innerHTML += `
                        <div class="mb-4">
                            <label for="document_${index}" class="block text-gray-700 font-semibold mb-2">
                                ${doc.required ? '<span class="text-red-500">*</span>' : '<span class="text-gray-400">(Opsional)</span>'} 
                                ${doc.name}
                            </label>
                            <div class="file-drop-area">
                                <input type="file" id="document_${index}" name="documents[${index}]" 
                                    accept=".pdf,.jpg,.jpeg,.png,.doc,.docx"
                                    class="w-full p-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500" 
                                    ${doc.required ? 'required' : ''}>
                            </div>
                            <p class="text-xs text-gray-500 mt-1">Format: PDF, DOC, DOCX, JPG, PNG (Max: 5MB)</p>
                        </div>
                    `;
                });
            }
        }

        async function loadGuidelinesForForm() {
            const jenisDataSelect = document.getElementById('jenis_data');

            try {
                const response = await fetch(`{{ route('user.guidelines') }}`, {
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                            'content')
                    }
                });
                const guidelines = await response.json();
                currentGuidelines = guidelines;

                jenisDataSelect.innerHTML = '<option value="">Pilih Jenis Data</option>';
                guidelines.forEach(guideline => {
                    jenisDataSelect.innerHTML +=
                        `<option value="${guideline.id}" data-fee="${guideline.fee}">${guideline.title}</option>`;
                });

                // Event listener untuk update fee berdasarkan kategori
                jenisDataSelect.addEventListener('change', (e) => {
                    const selectedId = e.target.value;
                    if (selectedId) {
                        document.getElementById('guideline_id_input').value = selectedId;
                        const guideline = guidelines.find(g => g.id == selectedId);
                        const category = document.getElementById('kategori_input').value;

                        // Show fee information based on category
                        updateFeeInformation(guideline, category);
                    }
                });

            } catch (error) {
                console.error('Error loading guidelines:', error);
                jenisDataSelect.innerHTML = '<option value="">Error loading data</option>';
            }
        }

        function updateFeeInformation(guideline, category) {
            // Remove existing fee info
            const existingFeeInfo = document.getElementById('fee-info');
            if (existingFeeInfo) {
                existingFeeInfo.remove();
            }

            const jenisDataSelect = document.getElementById('jenis_data');
            const feeInfo = document.createElement('div');
            feeInfo.id = 'fee-info';
            feeInfo.className = 'mt-2 p-3 rounded-lg';

            if (category === 'pnbp') {
                // PNBP: Ada biaya dari guideline
                feeInfo.className += ' bg-yellow-50 border border-yellow-200';
                feeInfo.innerHTML = `
                    <p class="text-sm text-yellow-800">
                        <svg class="w-4 h-4 inline mr-1" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                        </svg>
                        <strong>Biaya PNBP:</strong> Rp ${new Intl.NumberFormat('id-ID').format(guideline.fee)}
                    </p>
                    <p class="text-xs text-yellow-600 mt-1">Digunakan untuk keperluan umum atau komersial</p>
                `;
            } else {
                // Non-PNBP: Gratis untuk penelitian
                feeInfo.className += ' bg-green-50 border border-green-200';
                feeInfo.innerHTML = `
                    <p class="text-sm text-green-800">
                        <svg class="w-4 h-4 inline mr-1" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                        </svg>
                        <strong>Non-PNBP:</strong> Gratis untuk penelitian/akademik
                    </p>
                    <p class="text-xs text-green-600 mt-1">Khusus untuk keperluan penelitian dan akademik</p>
                `;
            }

            jenisDataSelect.parentNode.appendChild(feeInfo);
        }

        function updateFeeDisplayForCategory(category) {
            // Update fee info jika ada guideline yang sudah dipilih
            const jenisDataSelect = document.getElementById('jenis_data');
            const selectedId = jenisDataSelect.value;

            if (selectedId && currentGuidelines.length > 0) {
                const guideline = currentGuidelines.find(g => g.id == selectedId);
                if (guideline) {
                    updateFeeInformation(guideline, category);
                }
            }
        }

        async function loadGuidelines() {
            const container = document.getElementById('accordion-container');

            try {
                const response = await fetch('{{ route('user.guidelines') }}', {
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                            'content')
                    }
                });
                const guidelines = await response.json();

                container.innerHTML = '';
                guidelines.forEach(guideline => {
                    const requirementsList = guideline.required_documents ?
                        guideline.required_documents.map(req => `<li class="text-sm">${req}</li>`).join('') :
                        '';

                    container.innerHTML += `
                        <div class="border border-gray-200 rounded-lg overflow-hidden">
                            <button class="accordion-header w-full flex justify-between items-center p-4 bg-gray-50 hover:bg-gray-100 transition-colors duration-200"
                                    onclick="toggleAccordion(this)">
                                <span class="text-lg font-semibold text-gray-800">${guideline.title}</span>
                                <svg class="w-6 h-6 transform transition-transform duration-200" fill="none"
                                    stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 9l-7 7-7-7"></path>
                                </svg>
                            </button>
                            <div class="accordion-content hidden p-4 bg-white text-gray-700">
                                <p class="mb-4">${guideline.description}</p>
                                <div class="mb-4">
                                    <h4 class="font-bold mb-2">Biaya:</h4>
                                    <div class="grid grid-cols-2 gap-4 text-sm">
                                        <div class="bg-yellow-50 p-3 rounded border border-yellow-200">
                                            <p class="font-semibold text-yellow-800">PNBP:</p>
                                            <p class="text-yellow-700">Rp ${new Intl.NumberFormat('id-ID').format(guideline.fee)}</p>
                                        </div>
                                        <div class="bg-green-50 p-3 rounded border border-green-200">
                                            <p class="font-semibold text-green-800">Non-PNBP:</p>
                                            <p class="text-green-700">Gratis (Penelitian)</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="mb-4">
                                    <h4 class="font-bold mb-2">Persyaratan Dokumen:</h4>
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                                        <div class="bg-blue-50 p-3 rounded border border-blue-200">
                                            <p class="font-semibold text-blue-800">PNBP (1 Dokumen):</p>
                                            <ul class="text-blue-700 list-disc list-inside mt-1">
                                                <li>Surat Pengantar Instansi</li>
                                            </ul>
                                        </div>
                                        <div class="bg-purple-50 p-3 rounded border border-purple-200">
                                            <p class="font-semibold text-purple-800">Non-PNBP (4 Dokumen):</p>
                                            <ul class="text-purple-700 list-disc list-inside mt-1 text-xs">
                                                <li>Surat Pengantar Universitas</li>
                                                <li>Proposal/Karya Ilmiah</li>
                                                <li>Dokumen Pendukung</li>
                                                <li>Dokumen Tambahan (Opsional)</li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                                ${requirementsList ? `
                                        <h4 class="font-bold mb-2">Dokumen Teknis yang Diperlukan:</h4>
                                        <ul class="list-disc list-inside space-y-1 mb-4">
                                            ${requirementsList}
                                        </ul>
                                        ` : ''}
                            </div>
                        </div>
                    `;
                });

            } catch (error) {
                console.error('Error loading guidelines:', error);
                container.innerHTML = '<p class="text-center text-gray-500">Gagal memuat panduan</p>';
            }
        }

        function toggleAccordion(button) {
            const content = button.nextElementSibling;
            const svg = button.querySelector('svg');
            content.classList.toggle('hidden');
            svg.classList.toggle('rotate-180');
        }

        async function submitApplication(form) {
            const submitBtn = document.getElementById('submit-btn');
            const submitText = document.getElementById('submit-text');
            const submitSpinner = document.getElementById('submit-spinner');

            // Show loading state
            submitBtn.disabled = true;
            submitText.textContent = 'Mengirim...';
            submitSpinner.classList.remove('hidden');

            const formData = new FormData(form);

            try {
                const response = await fetch(form.action, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                            'content')
                    }
                });

                const result = await response.json();

                if (response.ok) {
                    alert(
                        `Pengajuan berhasil dikirim!\nNomor: ${result.application_number || 'N/A'}\nDokumen: ${result.documents_count || 0} file`);
                    form.reset();
                    document.getElementById('document-uploads').innerHTML = '';

                    // Remove fee info
                    const feeInfo = document.getElementById('fee-info');
                    if (feeInfo) feeInfo.remove();

                    // Remove warnings
                    const warning = document.getElementById('date-range-warning');
                    if (warning) warning.remove();

                    // Reload page to show updated data
                    location.reload();
                } else {
                    console.error('Server error:', result);

                    // Enhanced error handling
                    if (result.errors && typeof result.errors === 'object') {
                        const errorMessages = Object.values(result.errors).flat().join('\n');
                        alert('Validasi error:\n' + errorMessages);
                    } else {
                        alert('Terjadi kesalahan: ' + (result.message || result.error || 'Unknown error'));
                    }
                }
            } catch (error) {
                console.error('Error submitting application:', error);
                alert('Terjadi kesalahan saat mengirim pengajuan: ' + error.message);
            } finally {
                // Reset loading state
                submitBtn.disabled = false;
                submitText.textContent = 'Ajukan Surat';
                submitSpinner.classList.add('hidden');
            }
        }

        async function submitPayment(form) {
            const formData = new FormData(form);

            try {
                const response = await fetch(form.action, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                            'content')
                    }
                });

                const result = await response.json();

                if (response.ok) {
                    alert('Bukti pembayaran berhasil diupload!');
                    closePaymentModal();
                    location.reload();
                } else {
                    alert('Terjadi kesalahan: ' + (result.message || result.error || 'Unknown error'));
                }
            } catch (error) {
                console.error('Error uploading payment proof:', error);
                alert('Terjadi kesalahan saat mengupload bukti pembayaran');
            }
        }

        async function updateProfile(form) {
            const formData = new FormData(form);

            try {
                const response = await fetch('{{ route('user.profile.update') }}', {
                    method: 'PUT',
                    body: formData,
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                            'content')
                    }
                });

                const result = await response.json();

                if (response.ok) {
                    alert('Profil berhasil diperbarui!');
                } else {
                    alert('Terjadi kesalahan: ' + (result.message || result.error || 'Unknown error'));
                }
            } catch (error) {
                console.error('Error updating profile:', error);
                alert('Terjadi kesalahan saat memperbarui profil');
            }
        }

        // Modal functions
        function showPaymentModal(applicationId) {
            document.getElementById('payment_application_id').value = applicationId;
            document.getElementById('paymentForm').action = `/user/applications/${applicationId}/upload-payment`;
            document.getElementById('paymentModal').style.display = 'flex';
        }

        function closePaymentModal() {
            document.getElementById('paymentModal').style.display = 'none';
            document.getElementById('paymentForm').reset();
        }

        function showEditModal(applicationData) {
            document.getElementById('edit_application_id').value = applicationData.id;
            document.getElementById('edit_no_surat').value = applicationData.application_number;
            document.getElementById('editForm').action = `/user/applications/${applicationData.id}`;

            // Populate edit form with existing data
            if (applicationData.purpose) {
                document.getElementById('edit_keperluan').value = applicationData.purpose;
            }

            document.getElementById('editModal').style.display = 'flex';
        }

        function closeEditModal() {
            document.getElementById('editModal').style.display = 'none';
            document.getElementById('editForm').reset();
        }

        // Close modals when clicking outside
        window.addEventListener('click', function(event) {
            const paymentModal = document.getElementById('paymentModal');
            const editModal = document.getElementById('editModal');

            if (event.target === paymentModal) {
                closePaymentModal();
            }
            if (event.target === editModal) {
                closeEditModal();
            }
        });
    </script>
</body>

</html>
