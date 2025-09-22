<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dasbor Admin BMKG Pontianak</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f3f4f6;
            color: #1f2937;
        }

        .sidebar {
            background-color: #1f2937;
            border-right: 1px solid #374151;
        }

        .main-content {
            background-color: #f9fafb;
        }

        .status-badge {
            display: inline-block;
            padding: 0.25rem 0.75rem;
            font-size: 0.875rem;
            line-height: 1.25rem;
            font-weight: 500;
            border-radius: 9999px;
        }

        .modal-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 50;
        }

        .modal-content {
            background-color: #ffffff;
            padding: 2rem;
            border-radius: 0.75rem;
            width: 90%;
            max-width: 768px;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
            max-height: 90vh;
            overflow-y: auto;
        }
    </style>
</head>

<body class="bg-gray-100 flex h-screen">
    <!-- Sidebar -->
    <div class="w-64 sidebar text-white p-6 flex flex-col items-center">
        <div class="flex items-center space-x-2 mb-8">
            <svg class="w-8 h-8 text-indigo-400" fill="currentColor" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6">
                </path>
            </svg>
            <span class="text-xl font-semibold">BMKG Pontianak</span>
        </div>

        <nav class="mt-8 space-y-4 w-full flex-grow">
            <a href="#dashboard"
                class="flex items-center px-4 py-2 rounded-lg bg-indigo-700 hover:bg-indigo-600 transition-colors">
                <svg class="w-5 h-5 mr-3" fill="currentColor" viewBox="0 0 20 20">
                    <path
                        d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293-.293a1 1 0 000-1.414l-7-7z">
                    </path>
                </svg>
                <span>Dasbor Admin</span>
            </a>

            <a href="#requests"
                class="flex items-center px-4 py-2 rounded-lg text-gray-300 hover:bg-gray-800 transition-colors">
                <svg class="w-5 h-5 mr-3" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd"
                        d="M9 2a1 1 0 00-1 1v1H5a2 2 0 00-2 2v2a2 2 0 002 2h2.223a3 3 0 01.996-2h.858a3 3 0 01.996 2H15a2 2 0 002-2V6a2 2 0 00-2-2h-3V3a1 1 0 00-2 0v1h-3V3a1 1 0 00-1-1zm1 14a1 1 0 100-2 1 1 0 000 2z"
                        clip-rule="evenodd"></path>
                </svg>
                <span>Manajemen Permintaan</span>
            </a>

            <a href="#billing"
                class="flex items-center px-4 py-2 rounded-lg text-gray-300 hover:bg-gray-800 transition-colors">
                <svg class="w-5 h-5 mr-3" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M4 4a2 2 0 00-2 2v6a2 2 0 002 2h2v4l4-4h5a2 2 0 002-2V6a2 2 0 00-2-2H4z"></path>
                </svg>
                <span>Manajemen Pembayaran</span>
            </a>

            <a href="#documents"
                class="flex items-center px-4 py-2 rounded-lg text-gray-300 hover:bg-gray-800 transition-colors">
                <svg class="w-5 h-5 mr-3" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd"
                        d="M3 17a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm3.293-7.707a1 1 0 011.414 0L9 10.586V3a1 1 0 112 0v7.586l1.293-1.293a1 1 0 111.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z"
                        clip-rule="evenodd"></path>
                </svg>
                <span>Upload Dokumen</span>
            </a>

            <a href="#guidelines"
                class="flex items-center px-4 py-2 rounded-lg text-gray-300 hover:bg-gray-800 transition-colors">
                <svg class="w-5 h-5 mr-3" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd"
                        d="M4 4a2 2 0 00-2 2v2a2 2 0 002 2h2v2H4a2 2 0 00-2 2v2a2 2 0 002 2h12a2 2 0 002-2v-2a2 2 0 00-2-2h-2v-2h2a2 2 0 002-2V6a2 2 0 00-2-2H4z"
                        clip-rule="evenodd"></path>
                </svg>
                <span>Manajemen Panduan</span>
            </a>

            <a href="#archives"
                class="flex items-center px-4 py-2 rounded-lg text-gray-300 hover:bg-gray-800 transition-colors">
                <svg class="w-5 h-5 mr-3" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M4 3a2 2 0 100 4h12a2 2 0 100-4H4z"></path>
                    <path fill-rule="evenodd"
                        d="M3 8h14v7a2 2 0 01-2 2H5a2 2 0 01-2-2V8zm5 3a1 1 0 011-1h2a1 1 0 110 2H9a1 1 0 01-1-1z"
                        clip-rule="evenodd"></path>
                </svg>
                <span>Manajemen Arsip</span>
            </a>

            <a href="#users"
                class="flex items-center px-4 py-2 rounded-lg text-gray-300 hover:bg-gray-800 transition-colors">
                <svg class="w-5 h-5 mr-3" fill="currentColor" viewBox="0 0 20 20">
                    <path
                        d="M9 6a3 3 0 11-6 0 3 3 0 016 0zM17 6a3 3 0 11-6 0 3 3 0 016 0zM12.93 17c.046-.327.07-.66.07-1a6.97 6.97 0 00-1.5-4.33A5 5 0 0119 16v1h-6.07zM6 11a5 5 0 015 5v1H1v-1a5 5 0 015-5z">
                    </path>
                </svg>
                <span>Manajemen Pengguna</span>
            </a>
        </nav>

        <div class="mt-auto w-full">
            <form action="{{ route('logout') }}" method="POST" class="w-full">
                @csrf
                <button type="submit"
                    class="w-full bg-red-500 hover:bg-red-600 text-white font-bold py-3 px-6 rounded-lg transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2">
                    Logout
                </button>
            </form>
        </div>
    </div>

    <!-- Main Content -->
    <main class="flex-1 p-8 main-content overflow-y-auto">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-3xl font-bold text-gray-800">Dasbor Admin</h1>
            <div class="flex items-center space-x-4">
                <div class="text-gray-600">Selamat datang, {{ Auth::user()->name }}!</div>
                <div class="bg-blue-600 text-white px-4 py-2 rounded-full font-semibold">Admin</div>
            </div>
        </div>

        @if (session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-6"
                role="alert">
                <span class="block sm:inline">{{ session('success') }}</span>
            </div>
        @endif

        <!-- Dashboard Section -->
        <div id="dashboard" class="space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="bg-white rounded-xl shadow-lg p-6">
                    <h3 class="text-lg font-semibold text-gray-700 mb-2">Total Permintaan</h3>
                    <p class="text-4xl font-bold text-gray-900">
                        {{ $stats['pending_requests'] + $stats['processing'] + $stats['completed'] }}</p>
                    <p class="text-sm text-gray-500 mt-1">Permintaan data total.</p>
                </div>
                <div class="bg-white rounded-xl shadow-lg p-6">
                    <h3 class="text-lg font-semibold text-gray-700 mb-2">Permintaan Menunggu</h3>
                    <p class="text-4xl font-bold text-gray-900">{{ $stats['pending_requests'] }}</p>
                    <p class="text-sm text-gray-500 mt-1">Permintaan yang perlu diverifikasi.</p>
                </div>
                <div class="bg-white rounded-xl shadow-lg p-6">
                    <h3 class="text-lg font-semibold text-gray-700 mb-2">Pembayaran Tertunda</h3>
                    <p class="text-4xl font-bold text-gray-900">{{ $stats['pending_payments'] }}</p>
                    <p class="text-sm text-gray-500 mt-1">Pembayaran yang perlu dikonfirmasi.</p>
                </div>
            </div>

            <!-- Recent Applications Table -->
            <div class="bg-white rounded-xl shadow-lg p-6">
                <h3 class="text-lg font-semibold text-gray-700 mb-4">Aplikasi Terbaru</h3>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Application Number</th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    User</th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Type</th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Status</th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Date</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($recent_applications as $app)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                        {{ $app->application_number }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $app->user->name }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span
                                            class="inline-flex px-2 py-1 text-xs font-semibold rounded {{ $app->type === 'pnbp' ? 'bg-blue-100 text-blue-800' : 'bg-green-100 text-green-800' }}">
                                            {{ strtoupper($app->type) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @switch($app->status)
                                            @case('pending')
                                                <span class="status-badge bg-yellow-100 text-yellow-800">Menunggu</span>
                                            @break

                                            @case('verified')
                                                <span class="status-badge bg-blue-100 text-blue-800">Terverifikasi</span>
                                            @break

                                            @case('completed')
                                                <span class="status-badge bg-green-100 text-green-800">Selesai</span>
                                            @break

                                            @case('rejected')
                                                <span class="status-badge bg-red-100 text-red-800">Ditolak</span>
                                            @break

                                            @default
                                                <span
                                                    class="status-badge bg-gray-100 text-gray-800">{{ ucfirst($app->status) }}</span>
                                        @endswitch
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $app->created_at->format('d/m/Y') }}</td>
                                </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="px-6 py-4 text-center text-gray-500">No recent
                                            applications</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Requests Section -->
            <div id="requests" class="space-y-6 hidden">
                <h2 class="text-2xl font-bold mb-4">Manajemen Permintaan</h2>
                <div class="bg-white p-6 rounded-xl shadow-lg">
                    <div class="flex justify-between items-center mb-4">
                        <h2 class="text-xl font-semibold">Daftar Pengajuan</h2>
                        <button onclick="loadRequests()"
                            class="bg-indigo-500 hover:bg-indigo-600 text-white font-bold py-2 px-4 rounded-lg">
                            <i class="fas fa-refresh mr-2"></i>Refresh
                        </button>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        NOMOR PENGAJUAN</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        JENIS SURAT</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        PEMOHON</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        TANGGAL PENGAJUAN</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        STATUS SURAT</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        PEMBAYARAN</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        AKSI</th>
                                </tr>
                            </thead>
                            <tbody id="requestsTableBody" class="bg-white divide-y divide-gray-200">
                                <tr>
                                    <td colspan="7" class="px-6 py-4 text-center text-gray-500">
                                        <i class="fas fa-spinner fa-spin mr-2"></i>Loading...
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Billing Section -->
            <div id="billing" class="space-y-6 hidden">
                <h2 class="text-2xl font-bold mb-4">Manajemen Pembayaran</h2>
                <div class="bg-white rounded-xl shadow-lg p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h2 class="text-xl font-semibold">Daftar Pembayaran</h2>
                        <button onclick="loadPayments()"
                            class="bg-indigo-500 hover:bg-indigo-600 text-white font-bold py-2 px-4 rounded-lg">
                            <i class="fas fa-refresh mr-2"></i>Refresh
                        </button>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        No. Surat</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Tanggal Pengajuan</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Jenis Data</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Pemohon</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Jumlah</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Status</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Aksi</th>
                                </tr>
                            </thead>
                            <tbody id="paymentsTableBody" class="bg-white divide-y divide-gray-200">
                                <tr>
                                    <td colspan="7" class="px-6 py-4 text-center text-gray-500">
                                        <i class="fas fa-spinner fa-spin mr-2"></i>Loading...
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Documents Section -->
            <div id="documents" class="space-y-6 hidden">
                <h2 class="text-2xl font-bold mb-4">Manajemen Upload Dokumen</h2>
                <div class="bg-white rounded-xl shadow-lg p-6">
                    <h3 class="text-lg font-semibold mb-4">Aplikasi yang Perlu Dokumen</h3>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        No. Aplikasi</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        User</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Jenis Layanan</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Status</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Aksi</th>
                                </tr>
                            </thead>
                            <tbody id="documentsTableBody" class="bg-white divide-y divide-gray-200">
                                <tr>
                                    <td colspan="5" class="px-6 py-4 text-center text-gray-500">Loading...</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Guidelines Section -->
            <div id="guidelines" class="space-y-6 hidden">
                <h2 class="text-2xl font-bold mb-4">Manajemen Panduan</h2>
                <div class="bg-white rounded-xl shadow-lg p-6">
                    <button onclick="showGuidelineModal('add')"
                        class="bg-indigo-500 hover:bg-indigo-600 text-white font-bold py-2 px-4 rounded-lg mb-4">
                        + Tambah Panduan Baru
                    </button>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Judul Panduan</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Tipe</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Biaya</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Status</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Aksi</th>
                                </tr>
                            </thead>
                            <tbody id="guidelinesTableBody" class="bg-white divide-y divide-gray-200">
                                <tr>
                                    <td colspan="5" class="px-6 py-4 text-center text-gray-500">Loading...</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Archives Section -->
            <div id="archives" class="space-y-6 hidden">
                <h2 class="text-2xl font-bold mb-4">Manajemen Arsip</h2>
                <div class="bg-white rounded-xl shadow-lg p-6">
                    <div class="mb-4 flex space-x-4">
                        <select id="archiveMonth" class="border border-gray-300 rounded-lg px-3 py-2"
                            onchange="loadArchives()">
                            <option value="">Semua Bulan</option>
                            <option value="1">Januari</option>
                            <option value="2">Februari</option>
                            <option value="3">Maret</option>
                            <option value="4">April</option>
                            <option value="5">Mei</option>
                            <option value="6">Juni</option>
                            <option value="7">Juli</option>
                            <option value="8">Agustus</option>
                            <option value="9">September</option>
                            <option value="10">Oktober</option>
                            <option value="11">November</option>
                            <option value="12">Desember</option>
                        </select>
                        <select id="archiveYear" class="border border-gray-300 rounded-lg px-3 py-2"
                            onchange="loadArchives()">
                            <option value="">Semua Tahun</option>
                            <option value="2024">2024</option>
                            <option value="2025">2025</option>
                        </select>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        No. Aplikasi</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        User</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Jenis Layanan</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Tipe</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Tanggal Arsip</th>
                                </tr>
                            </thead>
                            <tbody id="archivesTableBody" class="bg-white divide-y divide-gray-200">
                                <tr>
                                    <td colspan="5" class="px-6 py-4 text-center text-gray-500">Loading...</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Users Section -->
            <div id="users" class="space-y-6 hidden">
                <h2 class="text-2xl font-bold mb-4">Manajemen Pengguna</h2>
                <div class="bg-white rounded-xl shadow-lg p-6">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        ID Pengguna</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Nama Lengkap</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Email</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        No. HP</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Tanggal Bergabung</th>
                                </tr>
                            </thead>
                            <tbody id="usersTableBody" class="bg-white divide-y divide-gray-200">
                                <tr>
                                    <td colspan="5" class="px-6 py-4 text-center text-gray-500">Loading...</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </main>

        <!-- Modals -->
        <!-- Detail Modal -->
        <div id="detailModal" class="modal-overlay hidden">
            <div class="modal-content">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-2xl font-bold text-gray-900">Detail Permintaan</h3>
                    <button onclick="hideModal('detailModal')" class="text-gray-500 hover:text-gray-700">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
                <div class="space-y-6">
                    <div>
                        <h4 class="text-lg font-semibold text-gray-700 mb-2">Informasi Pemohon</h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-gray-600">
                            <div>
                                <p class="font-medium">Nomor Surat:</p>
                                <p id="modalNomorSurat" class="text-gray-900"></p>
                            </div>
                            <div>
                                <p class="font-medium">Pemohon:</p>
                                <p id="modalPemohon" class="text-gray-900"></p>
                            </div>
                            <div>
                                <p class="font-medium">Email:</p>
                                <p id="modalEmail" class="text-gray-900"></p>
                            </div>
                            <div>
                                <p class="font-medium">Tipe Pengguna:</p>
                                <p id="modalTipePengguna" class="text-gray-900"></p>
                            </div>
                        </div>
                    </div>
                    <div>
                        <h4 class="text-lg font-semibold text-gray-700 mb-2">Detail Permintaan</h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-gray-600">
                            <div>
                                <p class="font-medium">Jenis Data:</p>
                                <p id="modalJenisData" class="text-gray-900"></p>
                            </div>
                            <div>
                                <p class="font-medium">Tanggal Pengajuan:</p>
                                <p id="modalTanggalPengajuan" class="text-gray-900"></p>
                            </div>
                            <div>
                                <p class="font-medium">Periode Data:</p>
                                <p id="modalPeriodeData" class="text-gray-900"></p>
                            </div>
                            <div>
                                <p class="font-medium">Keperluan Penggunaan:</p>
                                <p id="modalKeperluan" class="text-gray-900"></p>
                            </div>
                        </div>
                    </div>
                    <div>
                        <h4 class="text-lg font-semibold text-gray-700 mb-2">Dokumen Terlampir</h4>
                        <a href="#" id="modalFileLink" target="_blank"
                            class="text-indigo-600 hover:underline flex items-center">
                            <svg class="w-5 h-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M14.752 11.123A4.5 4.5 0 0115 15a4.5 4.5 0 01-4.5 4.5M15 15h1m-1 0a2.5 2.5 0 01-5 0m-4-10l-4 4m0 0l4 4m-4-4h18a2 2 0 012 2v10a2 2 0 01-2-2V6a2 2 0 012-2z">
                                </path>
                            </svg>
                            <span id="modalFileNama">nama_file.pdf</span>
                        </a>
                    </div>
                </div>
                <div class="mt-8 flex justify-end space-x-4">
                    <button onclick="rejectRequest()"
                        class="bg-red-500 hover:bg-red-600 text-white font-bold py-2 px-4 rounded-lg">Tolak</button>
                    <button onclick="approveRequest()"
                        class="bg-green-500 hover:bg-green-600 text-white font-bold py-2 px-4 rounded-lg">Verifikasi</button>
                </div>
            </div>
        </div>

        <!-- Payment Modal -->
        <div id="paymentModal" class="modal-overlay hidden">
            <div class="modal-content">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-2xl font-bold text-gray-900">Verifikasi Pembayaran</h3>
                    <button onclick="hideModal('paymentModal')" class="text-gray-500 hover:text-gray-700">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
                <div class="space-y-6">
                    <div>
                        <h4 class="text-lg font-semibold text-gray-700 mb-2">Informasi Pembayaran</h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-gray-600">
                            <div>
                                <p class="font-medium">Nomor Surat:</p>
                                <p id="paymentNomorSurat" class="text-gray-900"></p>
                            </div>
                            <div>
                                <p class="font-medium">Pemohon:</p>
                                <p id="paymentPemohon" class="text-gray-900"></p>
                            </div>
                            <div>
                                <p class="font-medium">Jumlah:</p>
                                <p id="paymentAmount" class="text-gray-900"></p>
                            </div>
                            <div>
                                <p class="font-medium">Status:</p>
                                <p id="paymentStatus" class="text-yellow-800"></p>
                            </div>
                        </div>
                    </div>
                    <div>
                        <h4 class="text-lg font-semibold text-gray-700 mb-2">Bukti Pembayaran</h4>
                        <div class="mt-4 border border-gray-300 rounded-lg overflow-hidden">
                            <img id="paymentProofImage" src="" alt="Bukti Pembayaran"
                                class="w-full h-auto object-cover">
                        </div>
                    </div>
                </div>
                <div class="mt-8 flex justify-end space-x-4">
                    <button onclick="rejectPayment()"
                        class="bg-red-500 hover:bg-red-600 text-white font-bold py-2 px-4 rounded-lg">Tolak</button>
                    <button onclick="approvePayment()"
                        class="bg-green-500 hover:bg-green-600 text-white font-bold py-2 px-4 rounded-lg">Verifikasi
                        Pembayaran</button>
                </div>
            </div>
        </div>

        <!-- Guideline Modal -->
        <div id="guidelineModal" class="modal-overlay hidden">
            <div class="modal-content">
                <div class="flex justify-between items-center mb-4">
                    <h3 id="guidelineModalTitle" class="text-2xl font-bold text-gray-900">Tambah Panduan Baru</h3>
                    <button onclick="hideModal('guidelineModal')" class="text-gray-500 hover:text-gray-700">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
                <form id="guidelineForm" onsubmit="saveGuideline(event)" class="space-y-4">
                    <input type="hidden" id="guidelineId">
                    <div>
                        <label for="guidelineTitle" class="block text-sm font-medium text-gray-700">Judul Panduan</label>
                        <input type="text" id="guidelineTitle"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                            required>
                    </div>
                    <div>
                        <label for="guidelineDescription"
                            class="block text-sm font-medium text-gray-700">Deskripsi</label>
                        <textarea id="guidelineDescription" rows="3"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                            required></textarea>
                    </div>
                    <div>
                        <label for="guidelineType" class="block text-sm font-medium text-gray-700">Tipe</label>
                        <select id="guidelineType"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                            required>
                            <option value="">Pilih Tipe</option>
                            <option value="pnbp">PNBP (Umum/Instansi)</option>
                            <option value="non_pnbp">Non-PNBP (Mahasiswa)</option>
                        </select>
                    </div>
                    <div>
                        <label for="guidelineFee" class="block text-sm font-medium text-gray-700">Biaya</label>
                        <input type="number" id="guidelineFee"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                            min="0" required>
                    </div>
                    <div>
                        <label for="guidelineRequirements" class="block text-sm font-medium text-gray-700">Dokumen yang
                            Diperlukan (satu per baris)</label>
                        <textarea id="guidelineRequirements" rows="4"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                            placeholder="Surat pengantar dari universitas&#10;KTP pemohon&#10;Formulir permohonan"></textarea>
                    </div>
                    <div class="flex justify-end space-x-4">
                        <button type="button" onclick="hideModal('guidelineModal')"
                            class="bg-gray-200 hover:bg-gray-300 text-gray-800 font-bold py-2 px-4 rounded-lg">Batal</button>
                        <button type="submit"
                            class="bg-indigo-500 hover:bg-indigo-600 text-white font-bold py-2 px-4 rounded-lg">Simpan</button>
                    </div>
                </form>
            </div>
        </div>

        <script>
            // Global variables
            let currentRequestId = null;
            let currentPaymentId = null;

            document.addEventListener('DOMContentLoaded', () => {
                const navLinks = document.querySelectorAll('div.sidebar nav a');
                const sections = {
                    'dashboard': document.getElementById('dashboard'),
                    'requests': document.getElementById('requests'),
                    'billing': document.getElementById('billing'),
                    'documents': document.getElementById('documents'),
                    'guidelines': document.getElementById('guidelines'),
                    'archives': document.getElementById('archives'),
                    'users': document.getElementById('users')
                };

                navLinks.forEach(link => {
                    link.addEventListener('click', function(event) {
                        event.preventDefault();
                        const targetId = this.getAttribute('href').substring(1);

                        // Update navigation styles
                        navLinks.forEach(l => {
                            l.classList.remove('bg-indigo-700');
                            l.classList.add('text-gray-300', 'hover:bg-gray-800');
                        });

                        this.classList.remove('text-gray-300', 'hover:bg-gray-800');
                        this.classList.add('bg-indigo-700');

                        // Show/hide sections
                        for (const sectionId in sections) {
                            if (sections[sectionId]) {
                                sections[sectionId].classList.add('hidden');
                            }
                        }

                        if (sections[targetId]) {
                            sections[targetId].classList.remove('hidden');

                            // Load data when switching tabs
                            switch (targetId) {
                                case 'requests':
                                    loadRequests();
                                    break;
                                case 'billing':
                                    loadPayments();
                                    break;
                                case 'documents':
                                    loadDocuments();
                                    break;
                                case 'guidelines':
                                    loadGuidelines();
                                    break;
                                case 'archives':
                                    loadArchives();
                                    break;
                                case 'users':
                                    loadUsers();
                                    break;
                            }
                        }
                    });
                });

                // Load initial data
                loadRequests();
                loadPayments();
                loadGuidelines();
                loadUsers();
            });

            // Load Functions
            async function loadRequests() {
                try {
                    const response = await fetch('/admin/requests', {
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                                'content')
                        }
                    });

                    const data = await response.json();
                    const tbody = document.getElementById('requestsTableBody');

                    if (data.data && data.data.length > 0) {
                        tbody.innerHTML = data.data.map(app => `
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">${app.application_number}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">${app.guideline.title}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">${app.user.name}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">${new Date(app.created_at).toLocaleDateString('id-ID')}</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="status-badge ${getStatusColor(app.status)}">
                                    ${formatStatus(app.status)}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="status-badge ${app.payment ? getPaymentStatusColor(app.payment.status) : 'bg-gray-100 text-gray-800'}">
                                    ${app.payment ? formatStatus(app.payment.status) : 'Tidak ada'}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <button onclick="showDetailModal(${JSON.stringify(app).replace(/"/g, '&quot;')})" class="text-indigo-600 hover:text-indigo-900 mr-2">Detail</button>
                                ${app.status === 'pending' ? `
                                            <button onclick="openVerifyModal(${app.id})" class="text-green-600 hover:text-green-900">Verifikasi</button>
                                        ` : ''}
                            </td>
                        </tr>
                    `).join('');
                    } else {
                        tbody.innerHTML =
                            '<tr><td colspan="7" class="px-6 py-4 text-center text-gray-500">Tidak ada pengajuan surat.</td></tr>';
                    }
                } catch (error) {
                    console.error('Error loading requests:', error);
                    document.getElementById('requestsTableBody').innerHTML =
                        '<tr><td colspan="7" class="px-6 py-4 text-center text-red-500">Error loading data</td></tr>';
                }
            }

            async function loadPayments() {
                try {
                    const response = await fetch('/admin/payments', {
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                                'content')
                        }
                    });

                    const data = await response.json();
                    const tbody = document.getElementById('paymentsTableBody');

                    if (data.data && data.data.length > 0) {
                        tbody.innerHTML = data.data.map(payment => `
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">${payment.application.application_number}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">${new Date(payment.application.created_at).toLocaleDateString('id-ID')}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">${payment.application.guideline.title}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">${payment.application.user.name}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">Rp ${formatNumber(payment.amount)}</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="status-badge ${getPaymentStatusColor(payment.status)}">
                                    ${formatStatus(payment.status)}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                ${payment.status === 'pending' && payment.payment_proof ? `
                                            <button onclick="showPaymentModal({
                                                id: ${payment.id},
                                                nomorSurat: '${payment.application.application_number}',
                                                pemohon: '${payment.application.user.name}',
                                                jenisData: '${payment.application.guideline.title}',
                                                amount: ${payment.amount},
                                                status: '${payment.status}',
                                                buktiPembayaran: '${payment.payment_proof}'
                                            })" class="text-green-600 hover:text-green-900">Konfirmasi Pembayaran</button>
                                        ` : '-'}
                            </td>
                        </tr>
                    `).join('');
                    } else {
                        tbody.innerHTML =
                            '<tr><td colspan="7" class="px-6 py-4 text-center text-gray-500">Tidak ada pengajuan pembayaran.</td></tr>';
                    }
                } catch (error) {
                    console.error('Error loading payments:', error);
                    document.getElementById('paymentsTableBody').innerHTML =
                        '<tr><td colspan="7" class="px-6 py-4 text-center text-red-500">Error loading data</td></tr>';
                }
            }

            async function loadGuidelines() {
                try {
                    const response = await fetch('/admin/guidelines', {
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                                'content')
                        }
                    });

                    const data = await response.json();
                    const tbody = document.getElementById('guidelinesTableBody');

                    if (data && data.length > 0) {
                        tbody.innerHTML = data.map(guideline => `
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">${guideline.title}</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="status-badge ${guideline.type === 'pnbp' ? 'bg-blue-100 text-blue-800' : 'bg-green-100 text-green-800'}">
                                    ${guideline.type.toUpperCase()}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">Rp ${formatNumber(guideline.fee)}</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="status-badge ${guideline.is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'}">
                                    ${guideline.is_active ? 'Aktif' : 'Nonaktif'}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-2">
                                <button onclick="editGuideline(${guideline.id})" class="text-indigo-600 hover:text-indigo-900">Edit</button>
                                <button onclick="deleteGuideline(${guideline.id})" class="text-red-600 hover:text-red-900">Hapus</button>
                            </td>
                        </tr>
                    `).join('');
                    } else {
                        tbody.innerHTML =
                            '<tr><td colspan="5" class="px-6 py-4 text-center text-gray-500">Tidak ada panduan.</td></tr>';
                    }
                } catch (error) {
                    console.error('Error loading guidelines:', error);
                    document.getElementById('guidelinesTableBody').innerHTML =
                        '<tr><td colspan="5" class="px-6 py-4 text-center text-red-500">Error loading data</td></tr>';
                }
            }

            async function loadUsers() {
                try {
                    const response = await fetch('/admin/users', {
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                                'content')
                        }
                    });

                    const data = await response.json();
                    const tbody = document.getElementById('usersTableBody');

                    if (data.data && data.data.length > 0) {
                        tbody.innerHTML = data.data.map(user => `
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">${user.id}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">${user.name}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">${user.email}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">${user.phone || '-'}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">${new Date(user.created_at).toLocaleDateString('id-ID')}</td>
                        </tr>
                    `).join('');
                    } else {
                        tbody.innerHTML =
                            '<tr><td colspan="5" class="px-6 py-4 text-center text-gray-500">Tidak ada pengguna terdaftar.</td></tr>';
                    }
                } catch (error) {
                    console.error('Error loading users:', error);
                    document.getElementById('usersTableBody').innerHTML =
                        '<tr><td colspan="5" class="px-6 py-4 text-center text-red-500">Error loading data</td></tr>';
                }
            }

            async function loadDocuments() {
                // Load documents that need to be uploaded
                const tbody = document.getElementById('documentsTableBody');
                tbody.innerHTML =
                    '<tr><td colspan="5" class="px-6 py-4 text-center text-gray-500">Fitur upload dokumen akan segera tersedia</td></tr>';
            }

            async function loadArchives() {
                const month = document.getElementById('archiveMonth')?.value || '';
                const year = document.getElementById('archiveYear')?.value || '';

                // Load archives with filter
                const tbody = document.getElementById('archivesTableBody');
                tbody.innerHTML =
                    '<tr><td colspan="5" class="px-6 py-4 text-center text-gray-500">Fitur arsip akan segera tersedia</td></tr>';
            }

            // Modal Functions
            function showDetailModal(submission) {
                document.getElementById('modalNomorSurat').textContent = submission.application_number;
                document.getElementById('modalPemohon').textContent = submission.user.name;
                document.getElementById('modalEmail').textContent = submission.user.email;
                document.getElementById('modalTipePengguna').textContent = submission.user.role;
                document.getElementById('modalJenisData').textContent = submission.guideline.title;
                document.getElementById('modalTanggalPengajuan').textContent = new Date(submission.created_at)
                    .toLocaleDateString('id-ID');
                document.getElementById('modalPeriodeData').textContent = 'Data permintaan';
                document.getElementById('modalKeperluan').textContent = submission.notes || 'Tidak ada catatan';
                document.getElementById('modalFileNama').textContent = 'Lihat dokumen';

                currentRequestId = submission.id;
                document.getElementById('detailModal').classList.remove('hidden');
            }

            function showPaymentModal(data) {
                document.getElementById('paymentNomorSurat').textContent = data.nomorSurat;
                document.getElementById('paymentPemohon').textContent = data.pemohon;
                document.getElementById('paymentAmount').textContent = 'Rp ' + formatNumber(data.amount);
                document.getElementById('paymentStatus').textContent = formatStatus(data.status);

                if (data.buktiPembayaran) {
                    document.getElementById('paymentProofImage').src = `/storage/${data.buktiPembayaran}`;
                }

                currentPaymentId = data.id;
                document.getElementById('paymentModal').classList.remove('hidden');
            }

            function showGuidelineModal(mode, data = {}) {
                const title = document.getElementById('guidelineModalTitle');
                const form = document.getElementById('guidelineForm');

                form.reset();

                if (mode === 'add') {
                    title.textContent = 'Tambah Panduan Baru';
                    document.getElementById('guidelineId').value = '';
                } else if (mode === 'edit') {
                    title.textContent = 'Edit Panduan';
                    document.getElementById('guidelineId').value = data.id;
                    document.getElementById('guidelineTitle').value = data.title;
                    document.getElementById('guidelineDescription').value = data.description;
                    document.getElementById('guidelineType').value = data.type;
                    document.getElementById('guidelineFee').value = data.fee;

                    if (data.required_documents) {
                        document.getElementById('guidelineRequirements').value = data.required_documents.join('\n');
                    }
                }

                document.getElementById('guidelineModal').classList.remove('hidden');
            }

            function hideModal(modalId) {
                const modal = document.getElementById(modalId);
                if (modal) {
                    modal.classList.add('hidden');
                }
            }

            // Action Functions
            async function approveRequest() {
                if (!currentRequestId) return;

                try {
                    const response = await fetch(`/admin/requests/${currentRequestId}/verify`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                                'content')
                        },
                        body: JSON.stringify({
                            action: 'approve',
                            notes: ''
                        })
                    });

                    if (response.ok) {
                        hideModal('detailModal');
                        loadRequests();
                        alert('Request berhasil diverifikasi!');
                    }
                } catch (error) {
                    console.error('Error approving request:', error);
                    alert('Terjadi kesalahan saat memverifikasi request');
                }
            }

            async function rejectRequest() {
                if (!currentRequestId) return;

                try {
                    const response = await fetch(`/admin/requests/${currentRequestId}/verify`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                                'content')
                        },
                        body: JSON.stringify({
                            action: 'reject',
                            notes: 'Ditolak oleh admin'
                        })
                    });

                    if (response.ok) {
                        hideModal('detailModal');
                        loadRequests();
                        alert('Request berhasil ditolak!');
                    }
                } catch (error) {
                    console.error('Error rejecting request:', error);
                    alert('Terjadi kesalahan saat menolak request');
                }
            }

            async function approvePayment() {
                if (!currentPaymentId) return;

                try {
                    const response = await fetch(`/admin/payments/${currentPaymentId}/verify`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                                'content')
                        },
                        body: JSON.stringify({
                            action: 'approve'
                        })
                    });

                    if (response.ok) {
                        hideModal('paymentModal');
                        loadPayments();
                        alert('Payment berhasil diverifikasi!');
                    }
                } catch (error) {
                    console.error('Error approving payment:', error);
                    alert('Terjadi kesalahan saat memverifikasi payment');
                }
            }

            async function rejectPayment() {
                if (!currentPaymentId) return;

                try {
                    const response = await fetch(`/admin/payments/${currentPaymentId}/verify`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                                'content')
                        },
                        body: JSON.stringify({
                            action: 'reject'
                        })
                    });

                    if (response.ok) {
                        hideModal('paymentModal');
                        loadPayments();
                        alert('Payment berhasil ditolak!');
                    }
                } catch (error) {
                    console.error('Error rejecting payment:', error);
                    alert('Terjadi kesalahan saat menolak payment');
                }
            }

            async function saveGuideline(event) {
                event.preventDefault();

                const id = document.getElementById('guidelineId').value;
                const title = document.getElementById('guidelineTitle').value;
                const description = document.getElementById('guidelineDescription').value;
                const type = document.getElementById('guidelineType').value;
                const fee = document.getElementById('guidelineFee').value;
                const requirements = document.getElementById('guidelineRequirements').value.split('\n').filter(req => req
                    .trim());

                const data = {
                    title,
                    description,
                    type,
                    fee: parseFloat(fee),
                    required_documents: requirements
                };

                try {
                    const url = id ? `/admin/guidelines/${id}` : '/admin/guidelines';
                    const method = id ? 'PUT' : 'POST';

                    const response = await fetch(url, {
                        method: method,
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                                'content')
                        },
                        body: JSON.stringify(data)
                    });

                    if (response.ok) {
                        hideModal('guidelineModal');
                        loadGuidelines();
                        alert('Panduan berhasil disimpan!');
                    }
                } catch (error) {
                    console.error('Error saving guideline:', error);
                    alert('Terjadi kesalahan saat menyimpan panduan');
                }
            }

            async function editGuideline(id) {
                try {
                    const response = await fetch(`/admin/guidelines/${id}`, {
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                                'content')
                        }
                    });
                    const guideline = await response.json();
                    showGuidelineModal('edit', guideline);
                } catch (error) {
                    console.error('Error loading guideline:', error);
                }
            }

            async function deleteGuideline(id) {
                if (confirm('Apakah Anda yakin ingin menghapus panduan ini?')) {
                    try {
                        const response = await fetch(`/admin/guidelines/${id}`, {
                            method: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                                    'content')
                            }
                        });

                        if (response.ok) {
                            loadGuidelines();
                            alert('Panduan berhasil dihapus!');
                        }
                    } catch (error) {
                        console.error('Error deleting guideline:', error);
                        alert('Terjadi kesalahan saat menghapus panduan');
                    }
                }
            }

            // Utility Functions
            function getStatusColor(status) {
                const colors = {
                    'pending': 'bg-yellow-100 text-yellow-800',
                    'verified': 'bg-blue-100 text-blue-800',
                    'payment_pending': 'bg-orange-100 text-orange-800',
                    'paid': 'bg-purple-100 text-purple-800',
                    'processing': 'bg-indigo-100 text-indigo-800',
                    'completed': 'bg-green-100 text-green-800',
                    'rejected': 'bg-red-100 text-red-800'
                };
                return colors[status] || 'bg-gray-100 text-gray-800';
            }

            function getPaymentStatusColor(status) {
                const colors = {
                    'pending': 'bg-yellow-100 text-yellow-800',
                    'verified': 'bg-green-100 text-green-800',
                    'rejected': 'bg-red-100 text-red-800'
                };
                return colors[status] || 'bg-gray-100 text-gray-800';
            }

            function formatStatus(status) {
                const statuses = {
                    'pending': 'Menunggu',
                    'verified': 'Terverifikasi',
                    'payment_pending': 'Menunggu Bayar',
                    'paid': 'Sudah Bayar',
                    'processing': 'Diproses',
                    'completed': 'Selesai',
                    'rejected': 'Ditolak'
                };
                return statuses[status] || status;
            }

            function formatNumber(number) {
                return new Intl.NumberFormat('id-ID').format(number);
            }
        </script>
    </body>

    </html>
