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
            backdrop-filter: blur(4px);
        }

        .modal-content {
            max-height: 90vh;
            overflow-y: auto;
        }

        .hover-effect:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
            transition: all 0.2s ease;
        }
    </style>
</head>

<body class="flex min-h-screen">
    <!-- Sidebar -->
    <div class="sidebar w-64 flex flex-col">
        <div class="flex items-center justify-center h-16 bg-gray-900">
            <h1 class="text-white text-lg font-semibold">BMKG Pontianak</h1>
        </div>

        <nav class="flex-1 px-4 py-6 space-y-2">
            <a href="#dashboard" id="nav-dashboard"
                class="flex items-center px-4 py-3 text-gray-300 bg-indigo-700 rounded-lg hover:bg-gray-800 transition-colors duration-200">
                <svg class="w-5 h-5 mr-3" fill="currentColor" viewBox="0 0 20 20">
                    <path
                        d="M3 4a1 1 0 011-1h12a1 1 0 011 1v2a1 1 0 01-1 1H4a1 1 0 01-1-1V4zM3 10a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H4a1 1 0 01-1-1v-6zM14 9a1 1 0 00-1 1v6a1 1 0 001 1h2a1 1 0 001-1v-6a1 1 0 00-1-1h-2z">
                    </path>
                </svg>
                Dasbor Admin
            </a>

            <a href="#requests" id="nav-requests"
                class="flex items-center px-4 py-3 text-gray-300 hover:bg-gray-800 rounded-lg transition-colors duration-200">
                <svg class="w-5 h-5 mr-3" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd"
                        d="M3 4a1 1 0 011-1h4a1 1 0 010 2H6.414l2.293 2.293a1 1 0 01-1.414 1.414L5 6.414V8a1 1 0 01-2 0V4zm9 1a1 1 0 010-2h4a1 1 0 011 1v4a1 1 0 01-2 0V6.414l-2.293 2.293a1 1 0 11-1.414-1.414L13.586 5H12zm-9 7a1 1 0 012 0v1.586l2.293-2.293a1 1 0 111.414 1.414L6.414 15H8a1 1 0 010 2H4a1 1 0 01-1-1v-4zm13-1a1 1 0 011 1v4a1 1 0 01-1 1h-4a1 1 0 010-2h1.586l-2.293-2.293a1 1 0 111.414-1.414L15.586 13H14a1 1 0 01-1-1z"
                        clip-rule="evenodd"></path>
                </svg>
                Manajemen Permintaan
            </a>

            <a href="#billing" id="nav-billing"
                class="flex items-center px-4 py-3 text-gray-300 hover:bg-gray-800 rounded-lg transition-colors duration-200">
                <svg class="w-5 h-5 mr-3" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M4 4a2 2 0 00-2 2v1h16V6a2 2 0 00-2-2H4z"></path>
                    <path fill-rule="evenodd"
                        d="M18 9H2v5a2 2 0 002 2h12a2 2 0 002-2V9zM4 13a1 1 0 011-1h1a1 1 0 110 2H5a1 1 0 01-1-1zm5-1a1 1 0 100 2h1a1 1 0 100-2H9z"
                        clip-rule="evenodd"></path>
                </svg>
                Manajemen Pembayaran
            </a>

            <a href="#documents" id="nav-documents"
                class="flex items-center px-4 py-3 text-gray-300 hover:bg-gray-800 rounded-lg transition-colors duration-200">
                <svg class="w-5 h-5 mr-3" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd"
                        d="M3 17a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm3.293-7.707a1 1 0 011.414 0L9 10.586V3a1 1 0 112 0v7.586l1.293-1.293a1 1 0 111.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z"
                        clip-rule="evenodd"></path>
                </svg>
                Upload Dokumen
            </a>

            <a href="#guidelines" id="nav-guidelines"
                class="flex items-center px-4 py-3 text-gray-300 hover:bg-gray-800 rounded-lg transition-colors duration-200">
                <svg class="w-5 h-5 mr-3" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                Manajemen Panduan
            </a>

            <a href="#archives" id="nav-archives"
                class="flex items-center px-4 py-3 text-gray-300 hover:bg-gray-800 rounded-lg transition-colors duration-200">
                <svg class="w-5 h-5 mr-3" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M4 3a2 2 0 100 4h12a2 2 0 100-4H4z"></path>
                    <path fill-rule="evenodd"
                        d="M3 8h14v7a2 2 0 01-2 2H5a2 2 0 01-2-2V8zm5 3a1 1 0 011-1h2a1 1 0 110 2H9a1 1 0 01-1-1z"
                        clip-rule="evenodd"></path>
                </svg>
                Manajemen Arsip
            </a>

            <a href="#users" id="nav-users"
                class="flex items-center px-4 py-3 text-gray-300 hover:bg-gray-800 rounded-lg transition-colors duration-200">
                <svg class="w-5 h-5 mr-3" fill="currentColor" viewBox="0 0 20 20">
                    <path
                        d="M9 6a3 3 0 11-6 0 3 3 0 016 0zM17 6a3 3 0 11-6 0 3 3 0 016 0zM12.93 17c.046-.327.07-.66.07-1a6.97 6.97 0 00-1.5-4.33A5 5 0 0119 16v1zM6 11a5 5 0 015 5v1H1v-1a5 5 0 015-5z">
                    </path>
                </svg>
                Manajemen Pengguna
            </a>
        </nav>

        <div class="px-4 py-6">
            <div class="flex items-center mb-4">
                <div
                    class="w-8 h-8 bg-indigo-600 rounded-full flex items-center justify-center text-white font-bold text-sm">
                    {{ substr(Auth::user()->name, 0, 1) }}
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-white">{{ Auth::user()->name }}</p>
                    <p class="text-xs text-gray-400">Admin BMKG STAMAR</p>
                </div>
                <div class="ml-auto">
                    <span
                        class="inline-flex items-center px-2 py-1 text-xs font-medium bg-indigo-600 text-white rounded">
                        Admin
                    </span>
                </div>
            </div>

            <form action="{{ route('logout') }}" method="POST" class="w-full">
                @csrf
                <button type="submit"
                    class="w-full bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-4 rounded-lg transition-colors duration-200">
                    Logout
                </button>
            </form>
        </div>
    </div>

    <!-- Main Content -->
    <div class="flex-1 main-content">
        <!-- Header -->
        <header class="bg-white shadow-sm border-b border-gray-200 px-6 py-4">
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Dasbor Admin</h1>
                    <p class="text-gray-600 mt-1">Selamat datang, Admin BMKG STAMAR!</p>
                </div>
                <div class="flex items-center space-x-4">
                    <button id="refreshBtn" onclick="refreshCurrentSection()"
                        class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg font-medium transition-colors duration-200">
                        Refresh
                    </button>
                </div>
            </div>
        </header>

        <!-- Dashboard Content -->
        <div class="p-6">
            <!-- Dashboard Section -->
            <section id="dashboard" class="content-section">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-6 mb-8">
                    <div class="bg-white rounded-lg shadow-md p-6 hover-effect">
                        <div class="flex items-center">
                            <div class="p-3 rounded-full bg-yellow-100">
                                <svg class="w-6 h-6 text-yellow-600" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z"
                                        clip-rule="evenodd"></path>
                                </svg>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-600">Permintaan Pending</p>
                                <p class="text-2xl font-bold text-gray-900">{{ $stats['pending_requests'] }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white rounded-lg shadow-md p-6 hover-effect">
                        <div class="flex items-center">
                            <div class="p-3 rounded-full bg-blue-100">
                                <svg class="w-6 h-6 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M4 4a2 2 0 00-2 2v1h16V6a2 2 0 00-2-2H4z"></path>
                                    <path fill-rule="evenodd"
                                        d="M18 9H2v5a2 2 0 002 2h12a2 2 0 002-2V9zM4 13a1 1 0 011-1h1a1 1 0 110 2H5a1 1 0 01-1-1zm5-1a1 1 0 100 2h1a1 1 0 100-2H9z"
                                        clip-rule="evenodd"></path>
                                </svg>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-600">Pembayaran Pending</p>
                                <p class="text-2xl font-bold text-gray-900">{{ $stats['pending_payments'] }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white rounded-lg shadow-md p-6 hover-effect">
                        <div class="flex items-center">
                            <div class="p-3 rounded-full bg-indigo-100">
                                <svg class="w-6 h-6 text-indigo-600" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M4 2a1 1 0 011 1v2.101a7.002 7.002 0 0111.601 2.566 1 1 0 11-1.885.666A5.002 5.002 0 005.999 7H9a1 1 0 010 2H4a1 1 0 01-1-1V3a1 1 0 011-1zm.008 9.057a1 1 0 011.276.61A5.002 5.002 0 0014.001 13H11a1 1 0 110-2h5a1 1 0 011 1v5a1 1 0 11-2 0v-2.101a7.002 7.002 0 01-11.601-2.566 1 1 0 01.61-1.276z"
                                        clip-rule="evenodd"></path>
                                </svg>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-600">Sedang Diproses</p>
                                <p class="text-2xl font-bold text-gray-900">{{ $stats['processing'] }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white rounded-lg shadow-md p-6 hover-effect">
                        <div class="flex items-center">
                            <div class="p-3 rounded-full bg-green-100">
                                <svg class="w-6 h-6 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                        clip-rule="evenodd"></path>
                                </svg>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-600">Selesai</p>
                                <p class="text-2xl font-bold text-gray-900">{{ $stats['completed'] }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white rounded-lg shadow-md p-6 hover-effect">
                        <div class="flex items-center">
                            <div class="p-3 rounded-full bg-purple-100">
                                <svg class="w-6 h-6 text-purple-600" fill="currentColor" viewBox="0 0 20 20">
                                    <path
                                        d="M9 6a3 3 0 11-6 0 3 3 0 016 0zM17 6a3 3 0 11-6 0 3 3 0 016 0zM12.93 17c.046-.327.07-.66.07-1a6.97 6.97 0 00-1.5-4.33A5 5 0 0119 16v1zM6 11a5 5 0 015 5v1H1v-1a5 5 0 015-5z">
                                    </path>
                                </svg>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-600">Total Users</p>
                                <p class="text-2xl font-bold text-gray-900">{{ $stats['total_users'] }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Recent Applications -->
                <div class="bg-white rounded-lg shadow-md">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-900">Aplikasi Terbaru</h3>
                    </div>
                    <div class="p-6">
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
                                            Jenis Data</th>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Status</th>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Tanggal</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @forelse ($recent_applications as $application)
                                        <tr>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                                {{ $application->application_number }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                {{ $application->user->name }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                {{ $application->guideline->title }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                @php
                                                    $statusConfig = [
                                                        'pending' => 'bg-yellow-100 text-yellow-800',
                                                        'verified' => 'bg-blue-100 text-blue-800',
                                                        'payment_pending' => 'bg-orange-100 text-orange-800',
                                                        'paid' => 'bg-purple-100 text-purple-800',
                                                        'processing' => 'bg-indigo-100 text-indigo-800',
                                                        'completed' => 'bg-green-100 text-green-800',
                                                        'rejected' => 'bg-red-100 text-red-800',
                                                    ];
                                                @endphp
                                                <span
                                                    class="status-badge {{ $statusConfig[$application->status] ?? 'bg-gray-100 text-gray-800' }}">
                                                    {{ ucfirst($application->status) }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                {{ $application->created_at->format('d/m/Y') }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="px-6 py-4 text-center text-gray-500">Tidak ada
                                                aplikasi terbaru</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Manajemen Permintaan Section -->
            <section id="requests" class="content-section hidden">
                <div class="bg-white rounded-lg shadow-md">
                    <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
                        <h3 class="text-lg font-semibold text-gray-900">Manajemen Permintaan</h3>
                        <button onclick="loadRequests()"
                            class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg font-medium">
                            Refresh
                        </button>
                    </div>
                    <div class="p-6">
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">No.
                                            Aplikasi</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                                            User</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                                            Jenis Data</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                                            Status</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                                            Aksi</th>
                                    </tr>
                                </thead>
                                <tbody id="requestsTableBody" class="bg-white divide-y divide-gray-200">
                                    <tr>
                                        <td colspan="5" class="px-6 py-4 text-center text-gray-500">Loading...</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Manajemen Pembayaran Section -->
            <section id="billing" class="content-section hidden">
                <div class="bg-white rounded-lg shadow-md">
                    <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
                        <h3 class="text-lg font-semibold text-gray-900">Manajemen Pembayaran</h3>
                        <button onclick="loadPayments()"
                            class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg font-medium">
                            Refresh
                        </button>
                    </div>
                    <div class="p-6">
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">No.
                                            Surat</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                                            Tanggal Pengajuan</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                                            Jenis Data</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                                            Pemohon</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                                            Jumlah</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                                            Status</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                                            Aksi</th>
                                    </tr>
                                </thead>
                                <tbody id="paymentsTableBody" class="bg-white divide-y divide-gray-200">
                                    <tr>
                                        <td colspan="7" class="px-6 py-4 text-center text-gray-500">Loading...</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Upload Dokumen Section -->
            <section id="documents" class="content-section hidden">
                <div class="bg-white rounded-lg shadow-md">
                    <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900">Manajemen Upload Dokumen</h3>
                            <p class="text-sm text-gray-600 mt-1">Aplikasi yang Perlu Dokumen</p>
                        </div>
                        <button onclick="loadDocuments()"
                            class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg font-medium">
                            Refresh
                        </button>
                    </div>
                    <div class="p-6">
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">No.
                                            Aplikasi</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                                            User</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                                            Jenis Layanan</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                                            Status</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
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
            </section>

            <!-- Manajemen Panduan Section -->
            <section id="guidelines" class="content-section hidden">
                <div class="bg-white rounded-lg shadow-md">
                    <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
                        <h3 class="text-lg font-semibold text-gray-900">Manajemen Panduan</h3>
                        <div class="flex space-x-2">
                            <button onclick="showGuidelineModal()"
                                class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg font-medium">
                                Tambah Panduan
                            </button>
                            <button onclick="loadGuidelines()"
                                class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg font-medium">
                                Refresh
                            </button>
                        </div>
                    </div>
                    <div class="p-6">
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                                            Judul</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                                            Tipe</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                                            Biaya</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                                            Status</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
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
            </section>

            <!-- Manajemen Arsip Section -->
            <section id="archives" class="content-section hidden">
                <div class="bg-white rounded-lg shadow-md">
                    <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
                        <h3 class="text-lg font-semibold text-gray-900">Manajemen Arsip Lengkap</h3>
                        <div class="flex space-x-2">
                            <button onclick="showArchiveFilters()"
                                class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-medium">
                                Filter
                            </button>
                            <button onclick="loadArchives()"
                                class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg font-medium">
                                Refresh
                            </button>
                        </div>
                    </div>

                    <!-- Filter Section -->
                    <div id="archiveFilters" class="px-6 py-4 bg-gray-50 border-b border-gray-200 hidden">
                        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Tahun</label>
                                <select id="filterYear" class="w-full p-2 border border-gray-300 rounded-lg">
                                    <option value="">Semua Tahun</option>
                                    <option value="2025">2025</option>
                                    <option value="2024">2024</option>
                                    <option value="2023">2023</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Bulan</label>
                                <select id="filterMonth" class="w-full p-2 border border-gray-300 rounded-lg">
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
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Tipe</label>
                                <select id="filterType" class="w-full p-2 border border-gray-300 rounded-lg">
                                    <option value="">Semua Tipe</option>
                                    <option value="pnbp">PNBP</option>
                                    <option value="non_pnbp">Non-PNBP</option>
                                </select>
                            </div>
                            <div class="flex items-end">
                                <button onclick="applyArchiveFilters()"
                                    class="w-full bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg font-medium">
                                    Terapkan Filter
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="p-6">
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">No.
                                            Aplikasi</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                                            User</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                                            Jenis Data</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                                            Tipe</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                                            Tanggal Selesai</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                                            Durasi Proses</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                                            Aksi</th>
                                    </tr>
                                </thead>
                                <tbody id="archivesTableBody" class="bg-white divide-y divide-gray-200">
                                    <tr>
                                        <td colspan="7" class="px-6 py-4 text-center text-gray-500">Loading...</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Manajemen Pengguna Section -->
            <section id="users" class="content-section hidden">
                <div class="bg-white rounded-lg shadow-md">
                    <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
                        <h3 class="text-lg font-semibold text-gray-900">Manajemen Pengguna</h3>
                        <div class="flex space-x-2">
                            <button onclick="showUserModal()"
                                class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg font-medium">
                                Tambah User
                            </button>
                            <button onclick="loadUsers()"
                                class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg font-medium">
                                Refresh
                            </button>
                        </div>
                    </div>
                    <div class="p-6">
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                                            Nama</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                                            Email</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                                            Telepon</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                                            Tanggal Daftar</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                                            Aksi</th>
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
            </section>
        </div>
    </div>

    <!-- Modal untuk Request Details -->
    <div id="requestModal"
        class="modal-overlay hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="modal-content bg-white rounded-lg p-6 w-full max-w-2xl mx-4">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-2xl font-bold text-gray-900">Detail Permintaan</h3>
                <button onclick="hideModal('requestModal')" class="text-gray-500 hover:text-gray-700">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            <div id="requestDetails" class="space-y-4">
                <!-- Content will be populated by JavaScript -->
            </div>
            <div class="flex justify-end space-x-4 mt-6">
                <button onclick="rejectRequest()"
                    class="bg-red-500 hover:bg-red-600 text-white font-bold py-2 px-4 rounded-lg">
                    Tolak
                </button>
                <button onclick="approveRequest()"
                    class="bg-green-500 hover:bg-green-600 text-white font-bold py-2 px-4 rounded-lg">
                    Setujui
                </button>
            </div>
        </div>
    </div>

    <!-- Modal untuk Payment Details -->
    <div id="paymentModal"
        class="modal-overlay hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="modal-content bg-white rounded-lg p-6 w-full max-w-2xl mx-4">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-2xl font-bold text-gray-900">Detail Pembayaran</h3>
                <button onclick="hideModal('paymentModal')" class="text-gray-500 hover:text-gray-700">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            <div id="paymentDetails" class="space-y-4">
                <!-- Content will be populated by JavaScript -->
            </div>
            <div class="flex justify-end space-x-4 mt-6">
                <button onclick="rejectPayment()"
                    class="bg-red-500 hover:bg-red-600 text-white font-bold py-2 px-4 rounded-lg">
                    Tolak
                </button>
                <button onclick="approvePayment()"
                    class="bg-green-500 hover:bg-green-600 text-white font-bold py-2 px-4 rounded-lg">
                    Verifikasi
                </button>
            </div>
        </div>
    </div>

    <!-- Upload Document Modal -->
    <div id="uploadDocumentModal"
        class="modal-overlay hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="modal-content bg-white rounded-lg p-6 w-full max-w-lg mx-4">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-2xl font-bold text-gray-900">Upload Dokumen untuk User</h3>
                <button onclick="hideUploadModal()" class="text-gray-500 hover:text-gray-700">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            <div class="space-y-4 mb-6">
                <div class="bg-gray-50 p-4 rounded-lg">
                    <h4 class="font-semibold text-gray-700 mb-2">Informasi Aplikasi</h4>
                    <p><strong>No. Aplikasi:</strong> <span id="uploadApplicationNumber"></span></p>
                    <p><strong>Jenis Layanan:</strong> <span id="uploadServiceType"></span></p>
                </div>
            </div>

            <form id="uploadDocumentForm" class="space-y-4">
                <div>
                    <label for="document_name" class="block text-sm font-medium text-gray-700 mb-2">
                        Nama Dokumen
                    </label>
                    <input type="text" id="document_name" name="document_name"
                        class="w-full p-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500"
                        placeholder="Masukkan nama dokumen (contoh: Data Klimatologi 2025)" required>
                </div>

                <div>
                    <label for="upload_document" class="block text-sm font-medium text-gray-700 mb-2">
                        Upload File Dokumen
                    </label>
                    <input type="file" id="upload_document" name="document" accept=".pdf,.doc,.docx,.xls,.xlsx"
                        class="w-full p-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500"
                        required>
                    <p class="text-xs text-gray-500 mt-1">Format: PDF, DOC, DOCX, XLS, XLSX (Max: 10MB)</p>
                </div>

                <div class="flex justify-end space-x-4 mt-6">
                    <button type="button" onclick="hideUploadModal()"
                        class="bg-gray-200 hover:bg-gray-300 text-gray-800 font-bold py-2 px-4 rounded-lg">
                        Batal
                    </button>
                    <button type="button" onclick="uploadDocument()"
                        class="bg-indigo-500 hover:bg-indigo-600 text-white font-bold py-2 px-4 rounded-lg">
                        Upload Dokumen
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Timeline Detail Modal -->
    <div id="timelineModal"
        class="modal-overlay hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="modal-content bg-white rounded-lg p-6 w-full max-w-5xl mx-4 max-h-[90vh] overflow-y-auto">
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-2xl font-bold text-gray-900">Timeline Lengkap Pengajuan</h3>
                <button onclick="hideModal('timelineModal')" class="text-gray-500 hover:text-gray-700">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            <!-- Application Info -->
            <div id="applicationInfo" class="bg-gray-50 p-4 rounded-lg mb-6">
                <!-- Content will be populated by JavaScript -->
            </div>

            <!-- Timeline -->
            <div id="timelineContainer" class="space-y-4">
                <!-- Content will be populated by JavaScript -->
            </div>
        </div>
    </div>

    <!-- Modal untuk Guideline Form -->
    <div id="guidelineModal"
        class="modal-overlay hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="modal-content bg-white rounded-lg p-6 w-full max-w-2xl mx-4">
            <div class="flex justify-between items-center mb-4">
                <h3 id="guidelineModalTitle" class="text-2xl font-bold text-gray-900">Tambah Panduan Baru</h3>
                <button onclick="hideModal('guidelineModal')" class="text-gray-500 hover:text-gray-700">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            <form id="guidelineForm" class="space-y-4">
                <input type="hidden" id="guidelineId" value="">
                <div>
                    <label for="guidelineTitle" class="block text-sm font-medium text-gray-700 mb-2">Judul</label>
                    <input type="text" id="guidelineTitle" name="title" required
                        class="w-full p-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>
                <div>
                    <label for="guidelineDescription"
                        class="block text-sm font-medium text-gray-700 mb-2">Deskripsi</label>
                    <textarea id="guidelineDescription" name="description" rows="4" required
                        class="w-full p-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500"></textarea>
                </div>
                <div>
                    <label for="guidelineType" class="block text-sm font-medium text-gray-700 mb-2">Tipe</label>
                    <select id="guidelineType" name="type" required
                        class="w-full p-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        <option value="">Pilih Tipe</option>
                        <option value="pnbp">PNBP</option>
                        <option value="non_pnbp">Non-PNBP</option>
                    </select>
                </div>
                <div>
                    <label for="guidelineFee" class="block text-sm font-medium text-gray-700 mb-2">Biaya</label>
                    <input type="number" id="guidelineFee" name="fee" min="0" step="0.01" required
                        class="w-full p-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>
                <div>
                    <label for="guidelineDocuments" class="block text-sm font-medium text-gray-700 mb-2">Dokumen yang
                        Diperlukan</label>
                    <div id="documentsContainer">
                        <input type="text" name="required_documents[]" placeholder="Dokumen 1"
                            class="w-full p-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 mb-2">
                    </div>
                    <button type="button" onclick="addDocumentField()"
                        class="text-indigo-600 hover:text-indigo-800">+ Tambah Dokumen</button>
                </div>
                <div class="flex justify-end space-x-4 mt-6">
                    <button type="button" onclick="hideModal('guidelineModal')"
                        class="bg-gray-200 hover:bg-gray-300 text-gray-800 font-bold py-2 px-4 rounded-lg">
                        Batal
                    </button>
                    <button type="submit"
                        class="bg-indigo-500 hover:bg-indigo-600 text-white font-bold py-2 px-4 rounded-lg">
                        Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal untuk User Form -->
    <div id="userModal"
        class="modal-overlay hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="modal-content bg-white rounded-lg p-6 w-full max-w-lg mx-4">
            <div class="flex justify-between items-center mb-4">
                <h3 id="userModalTitle" class="text-2xl font-bold text-gray-900">Tambah User Baru</h3>
                <button onclick="hideModal('userModal')" class="text-gray-500 hover:text-gray-700">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            <form id="userForm" class="space-y-4">
                <input type="hidden" id="userId" value="">
                <div>
                    <label for="userName" class="block text-sm font-medium text-gray-700 mb-2">Nama</label>
                    <input type="text" id="userName" name="name" required
                        class="w-full p-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>
                <div>
                    <label for="userEmail" class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                    <input type="email" id="userEmail" name="email" required
                        class="w-full p-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>
                <div>
                    <label for="userPhone" class="block text-sm font-medium text-gray-700 mb-2">Telepon</label>
                    <input type="text" id="userPhone" name="phone"
                        class="w-full p-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>
                <div>
                    <label for="userRole" class="block text-sm font-medium text-gray-700 mb-2">Role</label>
                    <select id="userRole" name="role" required
                        class="w-full p-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        <option value="">Pilih Role</option>
                        <option value="user">User</option>
                        <option value="admin">Admin</option>
                    </select>
                </div>
                <div>
                    <label for="userPassword" class="block text-sm font-medium text-gray-700 mb-2">Password</label>
                    <input type="password" id="userPassword" name="password"
                        class="w-full p-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    <p class="text-xs text-gray-500 mt-1">Kosongkan jika tidak ingin mengubah password</p>
                </div>
                <div class="flex justify-end space-x-4 mt-6">
                    <button type="button" onclick="hideModal('userModal')"
                        class="bg-gray-200 hover:bg-gray-300 text-gray-800 font-bold py-2 px-4 rounded-lg">
                        Batal
                    </button>
                    <button type="submit"
                        class="bg-indigo-500 hover:bg-indigo-600 text-white font-bold py-2 px-4 rounded-lg">
                        Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Global variables
        let currentRequestId = null;
        let currentPaymentId = null;
        let currentDocumentUploadId = null;

        // Initialize dashboard
        document.addEventListener('DOMContentLoaded', function() {
            initializeNavigation();
            loadRequests();
        });

        // Navigation functionality
        function initializeNavigation() {
            const navLinks = document.querySelectorAll('[id^="nav-"]');
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
        }

        // Refresh current section
        function refreshCurrentSection() {
            const activeNav = document.querySelector('[id^="nav-"].bg-indigo-700');
            if (activeNav) {
                const sectionId = activeNav.getAttribute('href').substring(1);
                switch (sectionId) {
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
                    default:
                        location.reload();
                }
            }
        }

        // LOAD FUNCTIONS

        // Load requests
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
                    tbody.innerHTML = data.data.map(request => `
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">${request.application_number}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">${request.user.name}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">${request.guideline.title}</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="status-badge ${getStatusClass(request.status)}">${getStatusText(request.status)}</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                ${request.status === 'pending' ? 
                                    `<button onclick="showRequestModal(${request.id})" class="text-indigo-600 hover:text-indigo-900">Review</button>` : 
                                    '-'}
                            </td>
                        </tr>
                    `).join('');
                } else {
                    tbody.innerHTML =
                        '<tr><td colspan="5" class="px-6 py-4 text-center text-gray-500">Tidak ada permintaan</td></tr>';
                }
            } catch (error) {
                console.error('Error loading requests:', error);
                document.getElementById('requestsTableBody').innerHTML =
                    '<tr><td colspan="5" class="px-6 py-4 text-center text-red-500">Error loading data</td></tr>';
            }
        }

        // Load payments
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
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">Rp ${new Intl.NumberFormat('id-ID').format(payment.amount)}</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="status-badge ${getPaymentStatusClass(payment.status)}">${getPaymentStatusText(payment.status)}</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                ${payment.status === 'pending' ? 
                                    `<button onclick="showPaymentModal(${payment.id})" class="text-indigo-600 hover:text-indigo-900">Verifikasi</button>` : 
                                    'Terverifikasi'}
                            </td>
                        </tr>
                    `).join('');
                } else {
                    tbody.innerHTML =
                        '<tr><td colspan="7" class="px-6 py-4 text-center text-gray-500">Tidak ada pembayaran</td></tr>';
                }
            } catch (error) {
                console.error('Error loading payments:', error);
                document.getElementById('paymentsTableBody').innerHTML =
                    '<tr><td colspan="7" class="px-6 py-4 text-center text-red-500">Error loading data</td></tr>';
            }
        }

        // Load documents yang perlu upload
        async function loadDocuments() {
            try {
                const response = await fetch('/admin/documents', {
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                            'content')
                    }
                });

                const data = await response.json();
                const tbody = document.getElementById('documentsTableBody');

                if (data.data && data.data.length > 0) {
                    tbody.innerHTML = data.data.map(app => `
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">${app.application_number}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">${app.user.name}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">${app.guideline.title}</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="status-badge bg-green-100 text-green-800">Sudah Bayar</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <button onclick="showUploadModal(${app.id}, '${app.application_number}', '${app.guideline.title}')" 
                                        class="text-indigo-600 hover:text-indigo-900">Upload Dokumen</button>
                            </td>
                        </tr>
                    `).join('');
                } else {
                    tbody.innerHTML =
                        '<tr><td colspan="5" class="px-6 py-4 text-center text-gray-500">Tidak ada aplikasi yang perlu dokumen.</td></tr>';
                }
            } catch (error) {
                console.error('Error loading documents:', error);
                document.getElementById('documentsTableBody').innerHTML =
                    '<tr><td colspan="5" class="px-6 py-4 text-center text-red-500">Error loading data</td></tr>';
            }
        }

        // Load guidelines
        async function loadGuidelines() {
            try {
                const response = await fetch('/admin/guidelines', {
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                            'content')
                    }
                });

                const guidelines = await response.json();
                const tbody = document.getElementById('guidelinesTableBody');

                if (guidelines && guidelines.length > 0) {
                    tbody.innerHTML = guidelines.map(guideline => `
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">${guideline.title}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">${guideline.type.toUpperCase()}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">Rp ${new Intl.NumberFormat('id-ID').format(guideline.fee)}</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="status-badge ${guideline.is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'}">
                                    ${guideline.is_active ? 'Aktif' : 'Tidak Aktif'}
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
                        '<tr><td colspan="5" class="px-6 py-4 text-center text-gray-500">Tidak ada panduan</td></tr>';
                }
            } catch (error) {
                console.error('Error loading guidelines:', error);
                document.getElementById('guidelinesTableBody').innerHTML =
                    '<tr><td colspan="5" class="px-6 py-4 text-center text-red-500">Error loading data</td></tr>';
            }
        }

        // Enhanced loadArchives function
        async function loadArchives(filters = {}) {
            try {
                const params = new URLSearchParams(filters);
                const response = await fetch(`/admin/archives?${params}`, {
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                            'content')
                    }
                });

                const data = await response.json();
                const tbody = document.getElementById('archivesTableBody');

                if (data.data && data.data.length > 0) {
                    tbody.innerHTML = data.data.map(app => {
                        const completedHistory = app.histories?.find(h => h.action === 'completed');
                        const submittedHistory = app.histories?.find(h => h.action === 'submitted');
                        const processDays = submittedHistory && completedHistory ?
                            Math.ceil((new Date(completedHistory.created_at) - new Date(submittedHistory
                                .created_at)) / (1000 * 60 * 60 * 24)) : '-';

                        return `
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">${app.application_number}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">${app.user.name}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">${app.guideline.title}</td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="status-badge ${app.type === 'pnbp' ? 'bg-yellow-100 text-yellow-800' : 'bg-green-100 text-green-800'}">
                                        ${app.type.toUpperCase()}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    ${completedHistory ? new Date(completedHistory.created_at).toLocaleDateString('id-ID') : new Date(app.updated_at).toLocaleDateString('id-ID')}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">${processDays} hari</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <button onclick="showTimeline(${app.id})" class="text-indigo-600 hover:text-indigo-900 mr-3">
                                        Lihat Timeline
                                    </button>
                                    <button onclick="downloadArchive(${app.id})" class="text-green-600 hover:text-green-900">
                                        Download
                                    </button>
                                </td>
                            </tr>
                        `;
                    }).join('');
                } else {
                    tbody.innerHTML =
                        '<tr><td colspan="7" class="px-6 py-4 text-center text-gray-500">Tidak ada arsip</td></tr>';
                }
            } catch (error) {
                console.error('Error loading archives:', error);
                document.getElementById('archivesTableBody').innerHTML =
                    '<tr><td colspan="7" class="px-6 py-4 text-center text-red-500">Error loading data</td></tr>';
            }
        }

        // Load users
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
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">${user.name}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">${user.email}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">${user.phone || '-'}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">${new Date(user.created_at).toLocaleDateString('id-ID')}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-2">
                                <button onclick="editUser(${user.id})" class="text-indigo-600 hover:text-indigo-900">Edit</button>
                                <button onclick="deleteUser(${user.id})" class="text-red-600 hover:text-red-900">Hapus</button>
                            </td>
                        </tr>
                    `).join('');
                } else {
                    tbody.innerHTML =
                        '<tr><td colspan="5" class="px-6 py-4 text-center text-gray-500">Tidak ada pengguna</td></tr>';
                }
            } catch (error) {
                console.error('Error loading users:', error);
                document.getElementById('usersTableBody').innerHTML =
                    '<tr><td colspan="5" class="px-6 py-4 text-center text-red-500">Error loading data</td></tr>';
            }
        }

        // MODAL FUNCTIONS

        // Show/Hide Modal
        function showModal(modalId) {
            document.getElementById(modalId).classList.remove('hidden');
        }

        function hideModal(modalId) {
            document.getElementById(modalId).classList.add('hidden');
        }

        // Request Modal Functions
        async function showRequestModal(requestId) {
            currentRequestId = requestId;
            // Implementation for showing request details
            showModal('requestModal');
        }

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
                    hideModal('requestModal');
                    loadRequests();
                    alert('Permintaan berhasil disetujui!');
                } else {
                    alert('Gagal menyetujui permintaan');
                }
            } catch (error) {
                console.error('Error approving request:', error);
                alert('Terjadi kesalahan saat menyetujui permintaan');
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
                    hideModal('requestModal');
                    loadRequests();
                    alert('Permintaan berhasil ditolak!');
                }
            } catch (error) {
                console.error('Error rejecting request:', error);
                alert('Terjadi kesalahan saat menolak permintaan');
            }
        }

        // Payment Modal Functions
        async function showPaymentModal(paymentId) {
            currentPaymentId = paymentId;
            showModal('paymentModal');
        }

        // Update approvePayment function
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
                    // Reload documents setelah payment approval
                    loadDocuments();
                    alert('Payment berhasil diverifikasi! Aplikasi akan muncul di Upload Dokumen.');
                } else {
                    alert('Gagal memverifikasi payment');
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
                        action: 'reject',
                        notes: 'Ditolak oleh admin'
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

        // Upload Document Modal Functions
        function showUploadModal(applicationId, applicationNumber, serviceType) {
            currentDocumentUploadId = applicationId;

            // Set modal content
            document.getElementById('uploadApplicationNumber').textContent = applicationNumber;
            document.getElementById('uploadServiceType').textContent = serviceType;

            // Show modal
            showModal('uploadDocumentModal');
        }

        function hideUploadModal() {
            hideModal('uploadDocumentModal');
            document.getElementById('uploadDocumentForm').reset();
        }

        // Upload document function
        async function uploadDocument() {
            if (!currentDocumentUploadId) return;

            const form = document.getElementById('uploadDocumentForm');
            const formData = new FormData(form);

            try {
                const response = await fetch(`/admin/documents/${currentDocumentUploadId}/upload`, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                            'content')
                    }
                });

                const result = await response.json();

                if (response.ok) {
                    alert('Dokumen berhasil diupload! Status aplikasi berubah menjadi completed.');
                    hideUploadModal();
                    loadDocuments();
                } else {
                    alert('Gagal upload dokumen: ' + (result.message || result.error || 'Unknown error'));
                }
            } catch (error) {
                console.error('Error uploading document:', error);
                alert('Terjadi kesalahan saat upload dokumen');
            }
        }

        // Archive functions
        function showArchiveFilters() {
            const filtersDiv = document.getElementById('archiveFilters');
            filtersDiv.classList.toggle('hidden');
        }

        function applyArchiveFilters() {
            const filters = {
                year: document.getElementById('filterYear').value,
                month: document.getElementById('filterMonth').value,
                type: document.getElementById('filterType').value
            };

            // Remove empty filters
            Object.keys(filters).forEach(key => {
                if (!filters[key]) delete filters[key];
            });

            loadArchives(filters);
        }

        // Show timeline modal
        async function showTimeline(applicationId) {
            try {
                const response = await fetch(`/admin/applications/${applicationId}/timeline`, {
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                            'content')
                    }
                });

                const data = await response.json();

                // Populate application info
                const appInfo = document.getElementById('applicationInfo');
                const app = data.application;

                appInfo.innerHTML = `
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <h4 class="font-semibold text-gray-700 mb-2">Informasi Pengajuan</h4>
                            <p><strong>No. Aplikasi:</strong> ${app.application_number}</p>
                            <p><strong>User:</strong> ${app.user.name}</p>
                            <p><strong>Email:</strong> ${app.user.email}</p>
                            <p><strong>Jenis Data:</strong> ${app.guideline.title}</p>
                            <p><strong>Tipe:</strong> ${app.type.toUpperCase()}</p>
                        </div>
                        <div>
                            <h4 class="font-semibold text-gray-700 mb-2">Detail Proses</h4>
                            <p><strong>Tanggal Pengajuan:</strong> ${new Date(app.created_at).toLocaleDateString('id-ID')}</p>
                            <p><strong>Keperluan:</strong> ${app.purpose || '-'}</p>
                            <p><strong>Status:</strong> <span class="status-badge bg-green-100 text-green-800">Selesai</span></p>
                            ${app.payment ? `<p><strong>Biaya PNBP:</strong> Rp ${new Intl.NumberFormat('id-ID').format(app.payment.amount)}</p>` : ''}
                            ${app.generated_documents && app.generated_documents.length > 0 ? `<p><strong>Dokumen Diupload:</strong> ${app.generated_documents.length} file</p>` : ''}
                        </div>
                    </div>
                `;

                // Populate timeline
                const timelineContainer = document.getElementById('timelineContainer');
                timelineContainer.innerHTML = data.timeline.map(history => {
                    const actorName = history.actor ? history.actor.name : 'Sistem';
                    const actorType = history.actor_type === 'user' ? 'User' : history.actor_type === 'admin' ?
                        'Admin' : 'Sistem';

                    return `
                        <div class="flex items-start space-x-4 p-4 bg-white border border-gray-200 rounded-lg">
                            <div class="flex-shrink-0">
                                <div class="w-12 h-12 rounded-full ${getTimelineColor(history.action)} flex items-center justify-center">
                                    ${getTimelineIcon(history.action)}
                                </div>
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center justify-between">
                                    <h4 class="text-lg font-semibold text-gray-900">${history.title}</h4>
                                    <span class="text-sm text-gray-500">${new Date(history.created_at).toLocaleString('id-ID')}</span>
                                </div>
                                <p class="text-sm text-gray-600 mt-1">${history.description || '-'}</p>
                                <div class="mt-2">
                                    <span class="inline-flex items-center px-2 py-1 text-xs font-medium rounded-full ${getActorTypeClass(history.actor_type)}">
                                        ${actorType}: ${actorName}
                                    </span>
                                </div>
                                ${history.metadata ? `
                                        <div class="mt-3 p-3 bg-gray-50 rounded-lg">
                                            <h5 class="text-sm font-medium text-gray-700 mb-2">Detail:</h5>
                                            <div class="text-xs text-gray-600 space-y-1">
                                                ${Object.entries(history.metadata).map(([key, value]) => 
                                                    `<p><strong>${formatMetadataKey(key)}:</strong> ${formatMetadataValue(value)}</p>`
                                                ).join('')}
                                            </div>
                                        </div>
                                    ` : ''}
                            </div>
                        </div>
                    `;
                }).join('');

                showModal('timelineModal');
            } catch (error) {
                console.error('Error loading timeline:', error);
                alert('Gagal memuat timeline');
            }
        }

        // Download archive function
        async function downloadArchive(applicationId) {
            try {
                const response = await fetch(`/admin/applications/${applicationId}/download-archive`, {
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                            'content')
                    }
                });

                if (response.ok) {
                    const blob = await response.blob();
                    const url = window.URL.createObjectURL(blob);
                    const a = document.createElement('a');
                    a.style.display = 'none';
                    a.href = url;
                    a.download = `archive_${applicationId}.zip`;
                    document.body.appendChild(a);
                    a.click();
                    window.URL.revokeObjectURL(url);
                    document.body.removeChild(a);
                } else {
                    alert('Gagal mendownload arsip');
                }
            } catch (error) {
                console.error('Error downloading archive:', error);
                alert('Terjadi kesalahan saat mendownload arsip');
            }
        }

        // Guideline Modal Functions
        function showGuidelineModal(guidelineData = null) {
            if (guidelineData) {
                // Edit mode
                document.getElementById('guidelineModalTitle').textContent = 'Edit Panduan';
                document.getElementById('guidelineId').value = guidelineData.id;
                document.getElementById('guidelineTitle').value = guidelineData.title;
                document.getElementById('guidelineDescription').value = guidelineData.description;
                document.getElementById('guidelineType').value = guidelineData.type;
                document.getElementById('guidelineFee').value = guidelineData.fee;
            } else {
                // Add mode
                document.getElementById('guidelineModalTitle').textContent = 'Tambah Panduan Baru';
                document.getElementById('guidelineForm').reset();
                document.getElementById('guidelineId').value = '';
            }
            showModal('guidelineModal');
        }

        // User Modal Functions
        function showUserModal(userData = null) {
            if (userData) {
                // Edit mode
                document.getElementById('userModalTitle').textContent = 'Edit User';
                document.getElementById('userId').value = userData.id;
                document.getElementById('userName').value = userData.name;
                document.getElementById('userEmail').value = userData.email;
                document.getElementById('userPhone').value = userData.phone || '';
                document.getElementById('userRole').value = userData.role;
                document.getElementById('userPassword').required = false;
            } else {
                // Add mode
                document.getElementById('userModalTitle').textContent = 'Tambah User Baru';
                document.getElementById('userForm').reset();
                document.getElementById('userId').value = '';
                document.getElementById('userPassword').required = true;
            }
            showModal('userModal');
        }

        // CRUD Functions
        async function editGuideline(id) {
            try {
                const response = await fetch(`/admin/guidelines/${id}`, {
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                            'content')
                    }
                });
                const guideline = await response.json();
                showGuidelineModal(guideline);
            } catch (error) {
                console.error('Error loading guideline:', error);
                alert('Gagal memuat data panduan');
            }
        }

        async function deleteGuideline(id) {
            if (!confirm('Apakah Anda yakin ingin menghapus panduan ini?')) return;

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
                } else {
                    const result = await response.json();
                    alert('Gagal menghapus panduan: ' + (result.message || 'Unknown error'));
                }
            } catch (error) {
                console.error('Error deleting guideline:', error);
                alert('Terjadi kesalahan saat menghapus panduan');
            }
        }

        async function editUser(id) {
            // Implementation for editing user
            // Similar to editGuideline but for users
        }

        async function deleteUser(id) {
            if (!confirm('Apakah Anda yakin ingin menghapus user ini?')) return;

            try {
                const response = await fetch(`/admin/users/${id}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                            'content')
                    }
                });

                if (response.ok) {
                    loadUsers();
                    alert('User berhasil dihapus!');
                } else {
                    const result = await response.json();
                    alert('Gagal menghapus user: ' + (result.message || 'Unknown error'));
                }
            } catch (error) {
                console.error('Error deleting user:', error);
                alert('Terjadi kesalahan saat menghapus user');
            }
        }

        // Form submission handlers
        document.getElementById('guidelineForm').addEventListener('submit', async function(e) {
            e.preventDefault();

            const formData = new FormData(this);
            const guidelineId = document.getElementById('guidelineId').value;

            // Convert required_documents to array
            const documents = [];
            formData.getAll('required_documents[]').forEach(doc => {
                if (doc.trim()) documents.push(doc.trim());
            });

            const data = {
                title: formData.get('title'),
                description: formData.get('description'),
                type: formData.get('type'),
                fee: formData.get('fee'),
                required_documents: documents
            };

            try {
                const url = guidelineId ? `/admin/guidelines/${guidelineId}` : '/admin/guidelines';
                const method = guidelineId ? 'PUT' : 'POST';

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
                } else {
                    const result = await response.json();
                    alert('Gagal menyimpan panduan: ' + (result.message || 'Unknown error'));
                }
            } catch (error) {
                console.error('Error saving guideline:', error);
                alert('Terjadi kesalahan saat menyimpan panduan');
            }
        });

        document.getElementById('userForm').addEventListener('submit', async function(e) {
            e.preventDefault();

            const formData = new FormData(this);
            const userId = document.getElementById('userId').value;

            try {
                const url = userId ? `/admin/users/${userId}` : '/admin/users';
                const method = userId ? 'PUT' : 'POST';

                const response = await fetch(url, {
                    method: method,
                    body: formData,
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                            'content')
                    }
                });

                if (response.ok) {
                    hideModal('userModal');
                    loadUsers();
                    alert('User berhasil disimpan!');
                } else {
                    const result = await response.json();
                    alert('Gagal menyimpan user: ' + (result.message || 'Unknown error'));
                }
            } catch (error) {
                console.error('Error saving user:', error);
                alert('Terjadi kesalahan saat menyimpan user');
            }
        });

        // Helper functions
        function addDocumentField() {
            const container = document.getElementById('documentsContainer');
            const input = document.createElement('input');
            input.type = 'text';
            input.name = 'required_documents[]';
            input.placeholder = `Dokumen ${container.children.length + 1}`;
            input.className =
                'w-full p-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 mb-2';
            container.appendChild(input);
        }

        function getStatusClass(status) {
            const statusClasses = {
                'pending': 'bg-yellow-100 text-yellow-800',
                'verified': 'bg-blue-100 text-blue-800',
                'payment_pending': 'bg-orange-100 text-orange-800',
                'paid': 'bg-purple-100 text-purple-800',
                'processing': 'bg-indigo-100 text-indigo-800',
                'completed': 'bg-green-100 text-green-800',
                'rejected': 'bg-red-100 text-red-800'
            };
            return statusClasses[status] || 'bg-gray-100 text-gray-800';
        }

        function getStatusText(status) {
            const statusTexts = {
                'pending': 'Pending',
                'verified': 'Terverifikasi',
                'payment_pending': 'Menunggu Pembayaran',
                'paid': 'Sudah Bayar',
                'processing': 'Diproses',
                'completed': 'Selesai',
                'rejected': 'Ditolak'
            };
            return statusTexts[status] || status;
        }

        function getPaymentStatusClass(status) {
            const statusClasses = {
                'pending': 'bg-yellow-100 text-yellow-800',
                'verified': 'bg-green-100 text-green-800',
                'rejected': 'bg-red-100 text-red-800'
            };
            return statusClasses[status] || 'bg-gray-100 text-gray-800';
        }

        function getPaymentStatusText(status) {
            const statusTexts = {
                'pending': 'Menunggu Verifikasi',
                'verified': 'Terverifikasi',
                'rejected': 'Ditolak'
            };
            return statusTexts[status] || status;
        }

        // Timeline helper functions
        function getTimelineColor(action) {
            const colors = {
                'submitted': 'bg-blue-100 text-blue-600',
                'approved_with_payment': 'bg-green-100 text-green-600',
                'approved_no_payment': 'bg-green-100 text-green-600',
                'rejected': 'bg-red-100 text-red-600',
                'billing_generated': 'bg-yellow-100 text-yellow-600',
                'payment_uploaded': 'bg-purple-100 text-purple-600',
                'payment_verified': 'bg-emerald-100 text-emerald-600',
                'payment_rejected': 'bg-red-100 text-red-600',
                'document_uploaded': 'bg-indigo-100 text-indigo-600',
                'completed': 'bg-green-100 text-green-600',
                'archived': 'bg-gray-100 text-gray-600'
            };
            return colors[action] || 'bg-gray-100 text-gray-600';
        }

        function getTimelineIcon(action) {
            const icons = {
                'submitted': '<svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20"><path d="M9 13h6m-3-3v6m5 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>',
                'approved_with_payment': '<svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path></svg>',
                'rejected': '<svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path></svg>',
                'payment_uploaded': '<svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20"><path d="M4 4a2 2 0 00-2 2v1h16V6a2 2 0 00-2-2H4z"></path><path fill-rule="evenodd" d="M18 9H2v5a2 2 0 002 2h12a2 2 0 002-2V9zM4 13a1 1 0 011-1h1a1 1 0 110 2H5a1 1 0 01-1-1zm5-1a1 1 0 100 2h1a1 1 0 100-2H9z" clip-rule="evenodd"></path></svg>',
                'document_uploaded': '<svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M3 17a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm3.293-7.707a1 1 0 011.414 0L9 10.586V3a1 1 0 112 0v7.586l1.293-1.293a1 1 0 111.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z" clip-rule="evenodd"></path></svg>'
            };
            return icons[action] ||
                '<svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path></svg>';
        }

        function getActorTypeClass(actorType) {
            const classes = {
                'user': 'bg-blue-100 text-blue-800',
                'admin': 'bg-purple-100 text-purple-800',
                'system': 'bg-gray-100 text-gray-800'
            };
            return classes[actorType] || 'bg-gray-100 text-gray-800';
        }

        function formatMetadataKey(key) {
            const keyMappings = {
                'amount': 'Jumlah',
                'file_path': 'File Path',
                'document_name': 'Nama Dokumen',
                'fee_amount': 'Biaya',
                'admin_notes': 'Catatan Admin',
                'completion_date': 'Tanggal Selesai',
                'total_process_days': 'Total Hari Proses',
                'approved_by': 'Disetujui oleh',
                'uploaded_by': 'Diupload oleh',
                'verified_by': 'Diverifikasi oleh',
                'rejected_by': 'Ditolak oleh'
            };
            return keyMappings[key] || key.replace('_', ' ');
        }

        function formatMetadataValue(value) {
            if (typeof value === 'number' && value > 1000) {
                return 'Rp ' + new Intl.NumberFormat('id-ID').format(value);
            }
            if (typeof value === 'string' && value.includes('T') && value.includes(':')) {
                return new Date(value).toLocaleString('id-ID');
            }
            return value;
        }
    </script>
</body>

</html>
