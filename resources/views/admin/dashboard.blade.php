<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin - BMKG Pontianak</title>
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
            background: linear-gradient(135deg, #1f2937 0%, #374151 100%);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
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

        .card-hover:hover {
            transform: translateY(-4px);
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
            transition: all 0.3s ease;
        }

        .modal-overlay {
            backdrop-filter: blur(8px);
        }

        .gradient-bg {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }

        .loading-spinner {
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            from {
                transform: rotate(0deg);
            }

            to {
                transform: rotate(360deg);
            }
        }
    </style>
</head>

<body class="flex min-h-screen">
    <!-- Sidebar -->
    <div class="sidebar w-72 flex flex-col shadow-2xl">
        <div class="flex items-center justify-center h-20 border-b border-white border-opacity-20">
            <div class="text-center">
                <h1 class="text-white text-xl font-bold">BMKG STAMAR</h1>
                <p class="text-white text-sm opacity-80">Admin Portal</p>
            </div>
        </div>

        <nav class="flex-1 px-6 py-8 space-y-3">
            <a href="#dashboard" id="nav-dashboard"
                class="flex items-center px-4 py-3 text-white bg-white bg-opacity-20 rounded-xl hover:bg-opacity-30 transition-all duration-200 backdrop-blur-sm">
                <svg class="w-6 h-6 mr-4" fill="currentColor" viewBox="0 0 20 20">
                    <path
                        d="M3 4a1 1 0 011-1h12a1 1 0 011 1v2a1 1 0 01-1 1H4a1 1 0 01-1-1V4zM3 10a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H4a1 1 0 01-1-1v-6zM14 9a1 1 0 00-1 1v6a1 1 0 001 1h2a1 1 0 001-1v-6a1 1 0 00-1-1h-2z">
                    </path>
                </svg>
                <span class="font-medium">Dashboard</span>
            </a>

            <a href="#submissions" id="nav-submissions"
                class="flex items-center px-4 py-3 text-white hover:bg-white hover:bg-opacity-20 rounded-xl transition-all duration-200">
                <svg class="w-6 h-6 mr-4" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd"
                        d="M3 4a1 1 0 011-1h4a1 1 0 010 2H6.414l2.293 2.293a1 1 0 01-1.414 1.414L5 6.414V8a1 1 0 01-2 0V4zm9 1a1 1 0 010-2h4a1 1 0 011 1v4a1 1 0 01-2 0V6.414l-2.293 2.293a1 1 0 11-1.414-1.414L13.586 5H12zm-9 7a1 1 0 012 0v1.586l2.293-2.293a1 1 0 111.414 1.414L6.414 15H8a1 1 0 010 2H4a1 1 0 01-1-1v-4zm13-1a1 1 0 011 1v4a1 1 0 01-1 1h-4a1 1 0 010-2h1.586l-2.293-2.293a1 1 0 111.414-1.414L15.586 13H14a1 1 0 01-1-1z"
                        clip-rule="evenodd"></path>
                </svg>
                <span class="font-medium">Manajemen Pengajuan</span>
            </a>

            <a href="#payments" id="nav-payments"
                class="flex items-center px-4 py-3 text-white hover:bg-white hover:bg-opacity-20 rounded-xl transition-all duration-200">
                <svg class="w-6 h-6 mr-4" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M4 4a2 2 0 00-2 2v1h16V6a2 2 0 00-2-2H4z"></path>
                    <path fill-rule="evenodd"
                        d="M18 9H2v5a2 2 0 002 2h12a2 2 0 002-2V9zM4 13a1 1 0 011-1h1a1 1 0 110 2H5a1 1 0 01-1-1zm5-1a1 1 0 100 2h1a1 1 0 100-2H9z"
                        clip-rule="evenodd"></path>
                </svg>
                <span class="font-medium">Manajemen Pembayaran</span>
            </a>

            <a href="#documents" id="nav-documents"
                class="flex items-center px-4 py-3 text-white hover:bg-white hover:bg-opacity-20 rounded-xl transition-all duration-200">
                <svg class="w-6 h-6 mr-4" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd"
                        d="M3 17a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm3.293-7.707a1 1 0 011.414 0L9 10.586V3a1 1 0 112 0v7.586l1.293-1.293a1 1 0 111.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z"
                        clip-rule="evenodd"></path>
                </svg>
                <span class="font-medium">Upload Dokumen</span>
            </a>

            <a href="#guidelines" id="nav-guidelines"
                class="flex items-center px-4 py-3 text-white hover:bg-white hover:bg-opacity-20 rounded-xl transition-all duration-200">
                <svg class="w-6 h-6 mr-4" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <span class="font-medium">Manajemen Panduan</span>
            </a>

            <a href="#users" id="nav-users"
                class="flex items-center px-4 py-3 text-white hover:bg-white hover:bg-opacity-20 rounded-xl transition-all duration-200">
                <svg class="w-6 h-6 mr-4" fill="currentColor" viewBox="0 0 20 20">
                    <path
                        d="M9 6a3 3 0 11-6 0 3 3 0 016 0zM17 6a3 3 0 11-6 0 3 3 0 016 0zM12.93 17c.046-.327.07-.66.07-1a6.97 6.97 0 00-1.5-4.33A5 5 0 0119 16v1zM6 11a5 5 0 015 5v1H1v-1a5 5 0 015-5z">
                    </path>
                </svg>
                <span class="font-medium">Manajemen Pengguna</span>
            </a>
        </nav>

        <div class="px-6 py-6 border-t border-white border-opacity-20">
            <div class="flex items-center mb-6">
                <div
                    class="w-12 h-12 bg-white bg-opacity-20 rounded-full flex items-center justify-center text-white font-bold text-lg backdrop-blur-sm">
                    {{ substr(Auth::user()->name ?? 'A', 0, 1) }}
                </div>
                <div class="ml-4">
                    <p class="text-white font-semibold">{{ Auth::user()->name ?? 'Admin' }}</p>
                    <p class="text-white text-sm opacity-80">Administrator</p>
                </div>
            </div>

            <form action="{{ route('logout') }}" method="POST" class="w-full">
                @csrf
                <button type="submit"
                    class="w-full bg-white bg-opacity-20 hover:bg-opacity-30 text-white font-semibold py-3 px-4 rounded-xl transition-all duration-200 backdrop-blur-sm border border-white border-opacity-20">
                    <svg class="w-5 h-5 inline mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd"
                            d="M3 3a1 1 0 00-1 1v12a1 1 0 102 0V4a1 1 0 00-1-1zm10.293 9.293a1 1 0 001.414 1.414l3-3a1 1 0 000-1.414l-3-3a1 1 0 10-1.414 1.414L14.586 9H7a1 1 0 100 2h7.586l-1.293 1.293z"
                            clip-rule="evenodd"></path>
                    </svg>
                    Logout
                </button>
            </form>
        </div>
    </div>

    <!-- Main Content -->
    <div class="flex-1 main-content">
        <!-- Header -->
        <header class="bg-white shadow-sm border-b border-gray-100 px-8 py-6">
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Dashboard Admin</h1>
                    <p class="text-gray-600 mt-2">Selamat datang, Admin BMKG STAMAR!</p>
                    <p class="text-sm text-gray-500 mt-1">{{ now()->format('l, d F Y') }}</p>
                </div>

                <div class="flex items-center space-x-4">
                    <button onclick="refreshCurrentSection()"
                        class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg font-medium transition-colors duration-200">
                        <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15">
                            </path>
                        </svg>
                        Refresh
                    </button>
                </div>
            </div>
        </header>

        <!-- Dashboard Content -->
        <div class="p-8">
            <!-- Dashboard Section -->
            <section id="dashboard" class="content-section">
                <!-- Statistics Cards -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 xl:grid-cols-5 gap-6 mb-8">
                    <div class="bg-white rounded-2xl shadow-lg p-6 card-hover border border-gray-100">
                        <div class="flex items-center">
                            <div class="p-4 rounded-xl bg-yellow-100">
                                <svg class="w-8 h-8 text-yellow-600" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z"
                                        clip-rule="evenodd"></path>
                                </svg>
                            </div>
                            <div class="ml-5">
                                <p class="text-sm font-semibold text-gray-600 uppercase tracking-wide">Pending</p>
                                <p class="text-3xl font-bold text-gray-900">{{ $stats['pending_requests'] ?? 0 }}</p>
                                <p class="text-xs text-gray-500 mt-1">Menunggu review</p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white rounded-2xl shadow-lg p-6 card-hover border border-gray-100">
                        <div class="flex items-center">
                            <div class="p-4 rounded-xl bg-blue-100">
                                <svg class="w-8 h-8 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M4 4a2 2 0 00-2 2v1h16V6a2 2 0 00-2-2H4z"></path>
                                    <path fill-rule="evenodd"
                                        d="M18 9H2v5a2 2 0 002 2h12a2 2 0 002-2V9zM4 13a1 1 0 011-1h1a1 1 0 110 2H5a1 1 0 01-1-1zm5-1a1 1 0 100 2h1a1 1 0 100-2H9z"
                                        clip-rule="evenodd"></path>
                                </svg>
                            </div>
                            <div class="ml-5">
                                <p class="text-sm font-semibold text-gray-600 uppercase tracking-wide">Pembayaran</p>
                                <p class="text-3xl font-bold text-gray-900">{{ $stats['pending_payments'] ?? 0 }}</p>
                                <p class="text-xs text-gray-500 mt-1">Menunggu verifikasi</p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white rounded-2xl shadow-lg p-6 card-hover border border-gray-100">
                        <div class="flex items-center">
                            <div class="p-4 rounded-xl bg-indigo-100">
                                <svg class="w-8 h-8 text-indigo-600" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M4 2a1 1 0 011 1v2.101a7.002 7.002 0 0111.601 2.566 1 1 0 11-1.885.666A5.002 5.002 0 005.999 7H9a1 1 0 010 2H4a1 1 0 01-1-1V3a1 1 0 011-1zm.008 9.057a1 1 0 011.276.61A5.002 5.002 0 0014.001 13H11a1 1 0 110-2h5a1 1 0 011 1v5a1 1 0 11-2 0v-2.101a7.002 7.002 0 01-11.601-2.566 1 1 0 01.61-1.276z"
                                        clip-rule="evenodd"></path>
                                </svg>
                            </div>
                            <div class="ml-5">
                                <p class="text-sm font-semibold text-gray-600 uppercase tracking-wide">Diproses</p>
                                <p class="text-3xl font-bold text-gray-900">{{ $stats['processing'] ?? 0 }}</p>
                                <p class="text-xs text-gray-500 mt-1">Sedang diproses</p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white rounded-2xl shadow-lg p-6 card-hover border border-gray-100">
                        <div class="flex items-center">
                            <div class="p-4 rounded-xl bg-green-100">
                                <svg class="w-8 h-8 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                        clip-rule="evenodd"></path>
                                </svg>
                            </div>
                            <div class="ml-5">
                                <p class="text-sm font-semibold text-gray-600 uppercase tracking-wide">Selesai</p>
                                <p class="text-3xl font-bold text-gray-900">{{ $stats['completed'] ?? 0 }}</p>
                                <p class="text-xs text-gray-500 mt-1">Telah diselesaikan</p>
                            </div>
                        </div>
                    </div>

                    <div
                        class="bg-gradient-to-br from-purple-500 to-indigo-600 rounded-2xl shadow-lg p-6 card-hover text-white">
                        <div class="flex items-center">
                            <div class="p-4 rounded-xl bg-white bg-opacity-20">
                                <svg class="w-8 h-8 text-white" fill="currentColor" viewBox="0 0 20 20">
                                    <path
                                        d="M9 6a3 3 0 11-6 0 3 3 0 016 0zM17 6a3 3 0 11-6 0 3 3 0 016 0zM12.93 17c.046-.327.07-.66.07-1a6.97 6.97 0 00-1.5-4.33A5 5 0 0119 16v1zM6 11a5 5 0 015 5v1H1v-1a5 5 0 015-5z">
                                    </path>
                                </svg>
                            </div>
                            <div class="ml-5">
                                <p class="text-sm font-semibold opacity-90 uppercase tracking-wide">Total Users</p>
                                <p class="text-3xl font-bold">{{ $stats['total_users'] ?? 0 }}</p>
                                <p class="text-xs opacity-80 mt-1">Pengguna terdaftar</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Recent Submissions -->
                <div class="bg-white rounded-2xl shadow-lg p-8 card-hover border border-gray-100">
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="text-2xl font-bold text-gray-900">Pengajuan Terbaru</h3>
                        <button onclick="showSection('submissions')"
                            class="text-indigo-600 hover:text-indigo-800 font-semibold transition-colors">
                            Lihat Semua →
                        </button>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th
                                        class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                        No. Surat</th>
                                    <th
                                        class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                        User</th>
                                    <th
                                        class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                        Jenis Data</th>
                                    <th
                                        class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                        Status</th>
                                    <th
                                        class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                        Tanggal</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse($recentSubmissions as $submission)
                                    <tr class="hover:bg-gray-50 transition-colors">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                            {{ $submission['submission_number'] }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $submission['user_name'] }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $submission['guideline_title'] }}
                                        </td>
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
                                                class="status-badge {{ $statusConfig[$submission['status']] ?? 'bg-gray-100 text-gray-800' }}">
                                                {{ $submission['status_label'] }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $submission['created_at_formatted'] }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="px-6 py-12 text-center text-gray-500">
                                            <div class="flex flex-col items-center">
                                                <svg class="w-12 h-12 text-gray-400 mb-4" fill="none"
                                                    viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                                </svg>
                                                <h3 class="text-lg font-medium text-gray-900 mb-2">Belum ada pengajuan
                                                </h3>
                                                <p class="text-gray-500">Pengajuan baru akan muncul di sini</p>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </section>

            <!-- Submissions Section -->
            <section id="submissions" class="content-section hidden">
                <div class="bg-white rounded-2xl shadow-lg p-8 card-hover border border-gray-100">
                    <div class="flex items-center justify-between mb-8">
                        <div>
                            <h2 class="text-3xl font-bold text-gray-900">Manajemen Pengajuan</h2>
                            <p class="text-gray-600 mt-2">Kelola semua pengajuan surat dan data</p>
                        </div>
                        <div class="flex items-center space-x-4">
                            <select id="statusFilter"
                                class="p-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500">
                                <option value="">Semua Status</option>
                                <option value="pending">Pending</option>
                                <option value="verified">Terverifikasi</option>
                                <option value="payment_pending">Menunggu Pembayaran</option>
                                <option value="paid">Sudah Bayar</option>
                                <option value="processing">Sedang Diproses</option>
                                <option value="completed">Selesai</option>
                                <option value="rejected">Ditolak</option>
                            </select>
                            <button onclick="loadSubmissions()"
                                class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-3 rounded-xl font-medium transition-colors">
                                <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15">
                                    </path>
                                </svg>
                                Refresh
                            </button>
                        </div>
                    </div>

                    <div class="overflow-hidden">
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th
                                            class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                            No. Surat</th>
                                        <th
                                            class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                            User</th>
                                        <th
                                            class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                            Jenis Data</th>
                                        <th
                                            class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                            Tipe</th>
                                        <th
                                            class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                            Status</th>
                                        <th
                                            class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                            Tanggal</th>
                                        <th
                                            class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                            Aksi</th>
                                    </tr>
                                </thead>
                                <tbody id="submissionsTableBody" class="bg-white divide-y divide-gray-200">
                                    <tr>
                                        <td colspan="7" class="px-6 py-8 text-center text-gray-500">
                                            <div class="flex items-center justify-center">
                                                <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-gray-400"
                                                    xmlns="http://www.w3.org/2000/svg" fill="none"
                                                    viewBox="0 0 24 24">
                                                    <circle class="opacity-25" cx="12" cy="12" r="10"
                                                        stroke="currentColor" stroke-width="4"></circle>
                                                    <path class="opacity-75" fill="currentColor"
                                                        d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                                    </path>
                                                </svg>
                                                Loading...
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Guidelines Section -->
            <section id="guidelines" class="content-section hidden">
                <div class="bg-white rounded-2xl shadow-lg p-8 card-hover border border-gray-100">
                    <div class="flex items-center justify-between mb-8">
                        <div>
                            <h2 class="text-3xl font-bold text-gray-900">Manajemen Panduan</h2>
                            <p class="text-gray-600 mt-2">Kelola panduan pengajuan surat dan data</p>
                        </div>
                        <div class="flex space-x-3">
                            <button onclick="showGuidelineModal()"
                                class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-xl font-medium transition-colors">
                                <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                </svg>
                                Tambah Panduan
                            </button>
                            <button onclick="loadGuidelines()"
                                class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-3 rounded-xl font-medium transition-colors">
                                Refresh
                            </button>
                        </div>
                    </div>

                    <div class="overflow-hidden">
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase">
                                            Judul</th>
                                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase">
                                            Tipe</th>
                                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase">
                                            Biaya</th>
                                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase">
                                            Status</th>
                                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase">
                                            Aksi</th>
                                    </tr>
                                </thead>
                                <tbody id="guidelinesTableBody" class="bg-white divide-y divide-gray-200">
                                    <tr>
                                        <td colspan="5" class="px-6 py-8 text-center text-gray-500">Loading...</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Payments Section -->
            <section id="payments" class="content-section hidden">
                <div class="bg-white rounded-2xl shadow-lg p-8 card-hover border border-gray-100">
                    <div class="flex items-center justify-between mb-8">
                        <div>
                            <h2 class="text-3xl font-bold text-gray-900">Manajemen Pembayaran</h2>
                            <p class="text-gray-600 mt-2">Kelola pembayaran dari pengajuan</p>
                        </div>
                        <button onclick="loadPayments()"
                            class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-3 rounded-xl font-medium transition-colors">
                            <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15">
                                </path>
                            </svg>
                            Refresh
                        </button>
                    </div>

                    <div class="overflow-hidden">
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th
                                            class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                            No. Surat</th>
                                        <th
                                            class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                            User</th>
                                        <th
                                            class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                            Jumlah</th>
                                        <th
                                            class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                            Metode</th>
                                        <th
                                            class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                            Status</th>
                                        <th
                                            class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                            Tanggal</th>
                                        <th
                                            class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                            Aksi</th>
                                    </tr>
                                </thead>
                                <tbody id="paymentsTableBody" class="bg-white divide-y divide-gray-200">
                                    <tr>
                                        <td colspan="7" class="px-6 py-8 text-center text-gray-500">
                                            <div class="flex items-center justify-center">
                                                <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-gray-400"
                                                    xmlns="http://www.w3.org/2000/svg" fill="none"
                                                    viewBox="0 0 24 24">
                                                    <circle class="opacity-25" cx="12" cy="12" r="10"
                                                        stroke="currentColor" stroke-width="4"></circle>
                                                    <path class="opacity-75" fill="currentColor"
                                                        d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                                    </path>
                                                </svg>
                                                Loading...
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Documents Section -->
            <section id="documents" class="content-section hidden">
                <div class="bg-white rounded-2xl shadow-lg p-8 card-hover border border-gray-100">
                    <div class="flex items-center justify-between mb-8">
                        <div>
                            <h2 class="text-3xl font-bold text-gray-900">Upload Dokumen</h2>
                            <p class="text-gray-600 mt-2">Kelola dokumen yang dihasilkan</p>
                        </div>
                        <button onclick="loadDocuments()"
                            class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-3 rounded-xl font-medium transition-colors">
                            <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15">
                                </path>
                            </svg>
                            Refresh
                        </button>
                    </div>

                    <div class="overflow-hidden">
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th
                                            class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                            No. Surat</th>
                                        <th
                                            class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                            User</th>
                                        <th
                                            class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                            Dokumen</th>
                                        <th
                                            class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                            Tipe</th>
                                        <th
                                            class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                            Ukuran</th>
                                        <th
                                            class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                            Tanggal</th>
                                        <th
                                            class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                            Aksi</th>
                                    </tr>
                                </thead>
                                <tbody id="documentsTableBody" class="bg-white divide-y divide-gray-200">
                                    <tr>
                                        <td colspan="7" class="px-6 py-8 text-center text-gray-500">
                                            <div class="flex items-center justify-center">
                                                <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-gray-400"
                                                    xmlns="http://www.w3.org/2000/svg" fill="none"
                                                    viewBox="0 0 24 24">
                                                    <circle class="opacity-25" cx="12" cy="12" r="10"
                                                        stroke="currentColor" stroke-width="4"></circle>
                                                    <path class="opacity-75" fill="currentColor"
                                                        d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                                    </path>
                                                </svg>
                                                Loading...
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Users Section -->
            <section id="users" class="content-section hidden">
                <div class="bg-white rounded-2xl shadow-lg p-8 card-hover border border-gray-100">
                    <div class="flex items-center justify-between mb-8">
                        <div>
                            <h2 class="text-3xl font-bold text-gray-900">Manajemen Pengguna</h2>
                            <p class="text-gray-600 mt-2">Kelola pengguna terdaftar</p>
                        </div>
                        <div class="flex space-x-3">
                            <button onclick="showUserModal()"
                                class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-xl font-medium transition-colors">
                                <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                </svg>
                                Tambah User
                            </button>
                            <button onclick="loadUsers()"
                                class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-3 rounded-xl font-medium transition-colors">
                                Refresh
                            </button>
                        </div>
                    </div>

                    <div class="overflow-hidden">
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase">
                                            Nama</th>
                                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase">
                                            Email</th>
                                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase">
                                            Telepon</th>
                                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase">
                                            Tanggal Daftar</th>
                                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase">
                                            Status</th>
                                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase">
                                            Aksi</th>
                                    </tr>
                                </thead>
                                <tbody id="usersTableBody" class="bg-white divide-y divide-gray-200">
                                    <tr>
                                        <td colspan="6" class="px-6 py-8 text-center text-gray-500">Loading...</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </div>

    <!-- Guidelines Modal -->
    <div id="guidelineModal"
        class="modal-overlay hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white rounded-2xl p-8 w-full max-w-2xl mx-4 max-h-[90vh] overflow-y-auto">
            <div class="flex justify-between items-center mb-6">
                <h3 id="guidelineModalTitle" class="text-2xl font-bold text-gray-900">Tambah Panduan Baru</h3>
                <button onclick="hideModal('guidelineModal')" class="text-gray-500 hover:text-gray-700 p-2">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            <form id="guidelineForm" class="space-y-6">
                @csrf
                <input type="hidden" id="guidelineId" value="">

                <div>
                    <label for="guidelineTitle" class="block text-sm font-medium text-gray-700 mb-2">Judul</label>
                    <input type="text" id="guidelineTitle" name="title" required
                        class="w-full p-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500">
                </div>

                <div>
                    <label for="guidelineDescription"
                        class="block text-sm font-medium text-gray-700 mb-2">Deskripsi</label>
                    <textarea id="guidelineDescription" name="description" rows="4" required
                        class="w-full p-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500"></textarea>
                </div>

                <div>
                    <label for="guidelineType" class="block text-sm font-medium text-gray-700 mb-2">Tipe</label>
                    <select id="guidelineType" name="type" required
                        class="w-full p-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500">
                        <option value="">Pilih Tipe</option>
                        <option value="pnbp">PNBP</option>
                        <option value="non_pnbp">Non-PNBP</option>
                    </select>
                </div>

                <div>
                    <label for="guidelineFee" class="block text-sm font-medium text-gray-700 mb-2">Biaya</label>
                    <input type="number" id="guidelineFee" name="fee" min="0" step="0.01" required
                        class="w-full p-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500">
                </div>

                <div>
                    <label for="guidelineDocuments" class="block text-sm font-medium text-gray-700 mb-2">Dokumen yang
                        Diperlukan</label>
                    <div id="documentsContainer">
                        <input type="text" name="required_documents[]" placeholder="Dokumen 1"
                            class="w-full p-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500 mb-2">
                    </div>
                    <button type="button" onclick="addDocumentField()"
                        class="text-indigo-600 hover:text-indigo-800 font-medium">
                        + Tambah Dokumen
                    </button>
                </div>

                <div class="flex justify-end space-x-4 pt-6 border-t border-gray-200">
                    <button type="button" onclick="hideModal('guidelineModal')"
                        class="bg-gray-200 hover:bg-gray-300 text-gray-800 font-bold py-3 px-6 rounded-xl transition-colors">
                        Batal
                    </button>
                    <button type="submit"
                        class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-3 px-6 rounded-xl transition-colors">
                        Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Detail Modal -->
    <div id="detailModal"
        class="modal-overlay hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white rounded-2xl p-8 w-full max-w-4xl mx-4 max-h-[90vh] overflow-y-auto">
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-2xl font-bold text-gray-900">Detail Pengajuan</h3>
                <button onclick="hideModal('detailModal')" class="text-gray-500 hover:text-gray-700 p-2">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            <div id="modalContent">
                <!-- Content will be populated by JavaScript -->
            </div>

            <div class="flex justify-end space-x-4 mt-6">
                <button onclick="rejectSubmission()"
                    class="bg-red-500 hover:bg-red-600 text-white font-bold py-3 px-6 rounded-xl transition-colors">
                    Tolak
                </button>
                <button onclick="approveSubmission()"
                    class="bg-green-500 hover:bg-green-600 text-white font-bold py-3 px-6 rounded-xl transition-colors">
                    Setujui
                </button>
            </div>
        </div>
    </div>

    <script>
        // Global variables
        let currentSubmissionId = null;
        let currentPaymentId = null;

        // Initialize dashboard
        document.addEventListener('DOMContentLoaded', function() {
            initializeNavigation();
            loadSubmissions();
            setupFormHandlers();
        });

        // Navigation functionality
        function initializeNavigation() {
            const navLinks = document.querySelectorAll('[id^="nav-"]');

            navLinks.forEach(link => {
                link.addEventListener('click', function(event) {
                    event.preventDefault();
                    const targetId = this.getAttribute('href').substring(1);
                    showSection(targetId);
                });
            });
        }

        function showSection(sectionId) {
            // Update navigation styles
            const navLinks = document.querySelectorAll('[id^="nav-"]');
            navLinks.forEach(link => {
                link.classList.remove('bg-white', 'bg-opacity-20');
                link.classList.add('hover:bg-white', 'hover:bg-opacity-20');
            });
            const targetNav = document.getElementById(`nav-${sectionId}`);
            if (targetNav) {
                targetNav.classList.remove('hover:bg-white', 'hover:bg-opacity-20');
                targetNav.classList.add('bg-white', 'bg-opacity-20');
            }

            // Show/hide sections
            const sections = ['dashboard', 'submissions', 'payments', 'documents', 'guidelines', 'users'];
            sections.forEach(section => {
                const element = document.getElementById(section);
                if (element) {
                    element.classList.add('hidden');
                }
            });

            const targetSection = document.getElementById(sectionId);
            if (targetSection) {
                targetSection.classList.remove('hidden');
            }

            // Load data for specific sections
            if (sectionId === 'submissions') {
                loadSubmissions();
            } else if (sectionId === 'guidelines') {
                loadGuidelines();
            }
        }

        // LOAD FUNCTIONS - INTEGRATED WITH AdminController
        async function loadSubmissions() {
            try {
                showLoadingState('submissionsTableBody', 7);

                const response = await fetch('/admin/submissions', {
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                            'content')
                    }
                });

                const result = await response.json();
                const tbody = document.getElementById('submissionsTableBody');

                if (result.success && result.data.length > 0) {
                    tbody.innerHTML = result.data.map(submission => {
                        const statusConfig = {
                            'pending': 'bg-yellow-100 text-yellow-800',
                            'verified': 'bg-blue-100 text-blue-800',
                            'payment_pending': 'bg-orange-100 text-orange-800',
                            'paid': 'bg-purple-100 text-purple-800',
                            'processing': 'bg-indigo-100 text-indigo-800',
                            'completed': 'bg-green-100 text-green-800',
                            'rejected': 'bg-red-100 text-red-800'
                        };

                        return `
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                    ${submission.submission_number}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    ${submission.user.name}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    ${submission.guideline.title}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="status-badge ${submission.guideline.type === 'pnbp' ? 'bg-yellow-100 text-yellow-800' : 'bg-green-100 text-green-800'}">
                                        ${submission.type_label}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="status-badge ${statusConfig[submission.status] || 'bg-gray-100 text-gray-800'}">
                                        ${submission.status_label}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    ${submission.created_at}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <button onclick="showDetail(${submission.id})" class="text-indigo-600 hover:text-indigo-900 transition-colors mr-3">
                                        Detail
                                    </button>
                                    ${submission.status === 'pending' ? `
                                                    <button onclick="showActions(${submission.id})" class="text-green-600 hover:text-green-900 transition-colors">
                                                        Review
                                                    </button>
                                                ` : ''}
                                </td>
                            </tr>
                        `;
                    }).join('');
                } else {
                    showEmptyState('submissionsTableBody', 7, 'Tidak ada pengajuan', result.message ||
                        'Belum ada pengajuan yang masuk');
                }
            } catch (error) {
                console.error('Error loading submissions:', error);
                showErrorState('submissionsTableBody', 7, 'Error loading submissions: ' + error.message);
            }
        }

        async function loadGuidelines() {
            try {
                showLoadingState('guidelinesTableBody', 5);

                const response = await fetch('/admin/guidelines', {
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                            'content')
                    }
                });

                const result = await response.json();
                const tbody = document.getElementById('guidelinesTableBody');

                if (result.success && result.data.length > 0) {
                    tbody.innerHTML = result.data.map(guideline => `
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                ${guideline.title}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                ${guideline.type.toUpperCase()}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                Rp ${new Intl.NumberFormat('id-ID').format(guideline.fee)}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="status-badge ${guideline.is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'}">
                                    ${guideline.is_active ? 'Aktif' : 'Tidak Aktif'}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-2">
                                <button onclick="editGuideline(${guideline.id})" class="text-indigo-600 hover:text-indigo-900 transition-colors">
                                    Edit
                                </button>
                                <button onclick="deleteGuideline(${guideline.id})" class="text-red-600 hover:text-red-900 transition-colors">
                                    Hapus
                                </button>
                            </td>
                        </tr>
                    `).join('');
                } else {
                    showEmptyState('guidelinesTableBody', 5, 'Tidak ada panduan', result.message ||
                        'Belum ada panduan yang dibuat');
                }
            } catch (error) {
                console.error('Error loading guidelines:', error);
                showErrorState('guidelinesTableBody', 5, 'Error loading guidelines: ' + error.message);
            }
        }

        // Load Payments - NEW
        async function loadPayments() {
            try {
                showLoadingState('paymentsTableBody', 7);

                const response = await fetch('/admin/payments', {
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                            'content')
                    }
                });

                const result = await response.json();
                const tbody = document.getElementById('paymentsTableBody');
                const statusConfig = {
                    'pending': 'bg-yellow-100 text-yellow-800',
                    'uploaded': 'bg-orange-100 text-orange-800',
                    'verified': 'bg-green-100 text-green-800',
                    'rejected': 'bg-red-100 text-red-800'
                };

                if (result.success && result.data.length > 0) {
                    tbody.innerHTML = result.data.map(payment => `
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                ${payment.submission_number}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                ${payment.user_name}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                Rp ${new Intl.NumberFormat('id-ID').format(payment.amount)}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                ${payment.payment_method}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="status-badge ${statusConfig[payment.status] || 'bg-gray-100 text-gray-800'}">
                                    ${payment.status_label}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                ${payment.created_at}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                ${payment.status === 'pending' || payment.status === 'uploaded' ? `
                                        <button onclick="verifyPayment(${payment.id})" class="text-green-600 hover:text-green-900 transition-colors mr-2">
                                            Verifikasi
                                        </button>
                                    ` : ''}
                                <button onclick="showPaymentDetail(${payment.id})" class="text-indigo-600 hover:text-indigo-900 transition-colors">
                                    Detail
                                </button>
                            </td>
                        </tr>
                    `).join('');
                } else {
                    showEmptyState('paymentsTableBody', 7, 'Tidak ada pembayaran', result.message ||
                        'Belum ada data pembayaran');
                }
            } catch (error) {
                console.error('Error loading payments:', error);
                showErrorState('paymentsTableBody', 7, 'Error loading payments: ' + error.message);
            }
        }

        // Load Documents - NEW
        async function loadDocuments() {
            try {
                showLoadingState('documentsTableBody', 7);

                const response = await fetch('/admin/documents', {
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                            'content')
                    }
                });

                const result = await response.json();
                const tbody = document.getElementById('documentsTableBody');

                if (result.success && result.data.length > 0) {
                    tbody.innerHTML = result.data.map(document => `
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                ${document.submission_number}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                ${document.user_name}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                ${document.document_name}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                ${document.document_type}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                ${document.file_size}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                ${document.created_at}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <a href="${document.url}" target="_blank" class="text-indigo-600 hover:text-indigo-900 transition-colors mr-2">
                                    Download
                                </a>
                                <button onclick="viewDocument('${document.url}')" class="text-blue-600 hover:text-blue-900 transition-colors">
                                    Lihat
                                </button>
                            </td>
                        </tr>
                    `).join('');
                } else {
                    showEmptyState('documentsTableBody', 7, 'Tidak ada dokumen', result.message ||
                        'Belum ada dokumen yang dihasilkan');
                }
            } catch (error) {
                console.error('Error loading documents:', error);
                showErrorState('documentsTableBody', 7, 'Error loading documents: ' + error.message);
            }
        }

        // Load Users - NEW
        async function loadUsers() {
            try {
                showLoadingState('usersTableBody', 6);

                const response = await fetch('/admin/users', {
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                            'content')
                    }
                });

                const result = await response.json();
                const tbody = document.getElementById('usersTableBody');

                if (result.success && result.data.length > 0) {
                    tbody.innerHTML = result.data.map(user => `
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                ${user.name}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                ${user.email}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                ${user.phone || 'N/A'}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                ${user.created_at}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="status-badge bg-green-100 text-green-800">
                                    Aktif
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-2">
                                <button onclick="editUser(${user.id})" class="text-indigo-600 hover:text-indigo-900 transition-colors">
                                    Edit
                                </button>
                                <button onclick="deleteUser(${user.id})" class="text-red-600 hover:text-red-900 transition-colors">
                                    Hapus
                                </button>
                            </td>
                        </tr>
                    `).join('');
                } else {
                    showEmptyState('usersTableBody', 6, 'Tidak ada pengguna', result.message ||
                        'Belum ada pengguna terdaftar');
                }
            } catch (error) {
                console.error('Error loading users:', error);
                showErrorState('usersTableBody', 6, 'Error loading users: ' + error.message);
            }
        }

        // Modal functions
        function showModal(modalId) {
            document.getElementById(modalId).classList.remove('hidden');
        }

        function hideModal(modalId) {
            document.getElementById(modalId).classList.add('hidden');
        }

        function showGuidelineModal(guidelineData = null) {
            if (guidelineData) {
                // Edit mode
                document.getElementById('guidelineModalTitle').textContent = 'Edit Panduan';
                document.getElementById('guidelineId').value = guidelineData.id;
                document.getElementById('guidelineTitle').value = guidelineData.title;
                document.getElementById('guidelineDescription').value = guidelineData.description;
                document.getElementById('guidelineType').value = guidelineData.type;
                document.getElementById('guidelineFee').value = guidelineData.fee;

                // Populate required documents
                const container = document.getElementById('documentsContainer');
                container.innerHTML = '';
                if (guidelineData.required_documents && guidelineData.required_documents.length > 0) {
                    guidelineData.required_documents.forEach((doc, index) => {
                        addDocumentField(doc);
                    });
                } else {
                    addDocumentField();
                }
            } else {
                // Add mode
                document.getElementById('guidelineModalTitle').textContent = 'Tambah Panduan Baru';
                document.getElementById('guidelineForm').reset();
                document.getElementById('guidelineId').value = '';

                // Reset documents container
                const container = document.getElementById('documentsContainer');
                container.innerHTML = '';
                addDocumentField();
            }
            showModal('guidelineModal');
        }

        // Form handlers
        function setupFormHandlers() {
            const guidelineForm = document.getElementById('guidelineForm');
            if (guidelineForm) {
                guidelineForm.addEventListener('submit', async function(e) {
                    e.preventDefault();
                    await saveGuideline();
                });
            }

            const statusFilter = document.getElementById('statusFilter');
            if (statusFilter) {
                statusFilter.addEventListener('change', function() {
                    loadSubmissions();
                });
            }
        }

        async function saveGuideline() {
            try {
                const formData = new FormData(document.getElementById('guidelineForm'));
                const guidelineId = document.getElementById('guidelineId').value;

                // Convert required_documents to array
                const documents = [];
                formData.getAll('required_documents[]').forEach(doc => {
                    if (doc.trim()) {
                        documents.push(doc.trim());
                    }
                });

                const data = {
                    title: formData.get('title'),
                    description: formData.get('description'),
                    type: formData.get('type'),
                    fee: formData.get('fee'),
                    required_documents: documents
                };

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

                const result = await response.json();

                if (result.success) {
                    hideModal('guidelineModal');
                    loadGuidelines();
                    showNotification('success', 'Berhasil', 'Panduan berhasil disimpan!');
                } else {
                    showNotification('error', 'Gagal', 'Gagal menyimpan panduan: ' + (result.message ||
                        'Unknown error'));
                }
            } catch (error) {
                console.error('Error saving guideline:', error);
                showNotification('error', 'Error', 'Terjadi kesalahan saat menyimpan panduan');
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

                const result = await response.json();

                if (result.success) {
                    showGuidelineModal(result.data);
                } else {
                    showNotification('error', 'Gagal', 'Gagal memuat data panduan');
                }
            } catch (error) {
                console.error('Error loading guideline:', error);
                showNotification('error', 'Error', 'Gagal memuat data panduan');
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

                const result = await response.json();

                if (result.success) {
                    loadGuidelines();
                    showNotification('success', 'Berhasil', 'Panduan berhasil dihapus!');
                } else {
                    showNotification('error', 'Gagal', 'Gagal menghapus panduan: ' + (result.message ||
                        'Unknown error'));
                }
            } catch (error) {
                console.error('Error deleting guideline:', error);
                showNotification('error', 'Error', 'Terjadi kesalahan saat menghapus panduan');
            }
        }

        function addDocumentField(value = '') {
            const container = document.getElementById('documentsContainer');
            const div = document.createElement('div');
            div.className = 'flex items-center mb-2';
            div.innerHTML = `
                <input type="text" name="required_documents[]" placeholder="Dokumen ${container.children.length + 1}" 
                       value="${value}" 
                       class="flex-1 p-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500 mr-2">
                <button type="button" onclick="removeDocumentField(this)" class="text-red-600 hover:text-red-800 p-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                    </svg>
                </button>
            `;
            container.appendChild(div);
        }

        function removeDocumentField(button) {
            button.parentElement.remove();
        }

        function refreshCurrentSection() {
            const activeNav = document.querySelector('[id^="nav-"].bg-white.bg-opacity-20');
            if (activeNav) {
                const sectionId = activeNav.getAttribute('href').substring(1);
                if (sectionId === 'submissions') {
                    loadSubmissions();
                } else if (sectionId === 'guidelines') {
                    loadGuidelines();
                } else {
                    location.reload();
                }
            }
        }

        // Loading, Error, and Empty states
        function showLoadingState(tableBodyId, colspan = 5) {
            const tbody = document.getElementById(tableBodyId);
            if (tbody) {
                tbody.innerHTML = `
                    <tr>
                        <td colspan="${colspan}" class="px-6 py-8 text-center">
                            <div class="flex items-center justify-center">
                                <svg class="animate-spin -ml-1 mr-3 h-6 w-6 text-indigo-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                <span class="text-gray-500">Memuat data...</span>
                            </div>
                        </td>
                    </tr>
                `;
            }
        }

        function showErrorState(tableBodyId, colspan = 5, message = 'Error loading data') {
            const tbody = document.getElementById(tableBodyId);
            if (tbody) {
                tbody.innerHTML = `
                    <tr>
                        <td colspan="${colspan}" class="px-6 py-8 text-center">
                            <div class="flex items-center justify-center flex-col">
                                <svg class="h-12 w-12 text-red-400 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                                </svg>
                                <h3 class="text-sm font-medium text-gray-900 mb-1">Error</h3>
                                <p class="text-sm text-gray-500 mb-4">${message}</p>
                                <button onclick="refreshCurrentSection()" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                                    </svg>
                                    Coba Lagi
                                </button>
                            </div>
                        </td>
                    </tr>
                `;
            }
        }

        function showEmptyState(tableBodyId, colspan = 5, title = 'Tidak ada data', description =
            'Data akan muncul di sini') {
            const tbody = document.getElementById(tableBodyId);
            if (tbody) {
                tbody.innerHTML = `
                    <tr>
                        <td colspan="${colspan}" class="px-6 py-12 text-center">
                            <svg class="mx-auto h-12 w-12 text-gray-400 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            <h3 class="text-sm font-medium text-gray-900 mb-1">${title}</h3>
                            <p class="text-sm text-gray-500">${description}</p>
                        </td>
                    </tr>
                `;
            }
        }

        // Enhanced notification system
        function showNotification(type, title, message, duration = 5000) {
            const notification = document.createElement('div');
            notification.className =
                `fixed top-4 right-4 max-w-sm w-full bg-white shadow-lg rounded-lg pointer-events-auto transform transition-all duration-300 translate-x-full z-50`;

            const bgColor = type === 'success' ? 'border-l-4 border-green-400 bg-green-50' :
                type === 'error' ? 'border-l-4 border-red-400 bg-red-50' :
                'border-l-4 border-blue-400 bg-blue-50';
            const textColor = type === 'success' ? 'text-green-800' :
                type === 'error' ? 'text-red-800' :
                'text-blue-800';
            const iconColor = type === 'success' ? 'text-green-400' :
                type === 'error' ? 'text-red-400' :
                'text-blue-400';

            notification.innerHTML = `
                <div class="rounded-lg p-4 ${bgColor}">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            ${type === 'success' ? `
                                            <svg class="h-5 w-5 ${iconColor}" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                            </svg>
                                        ` : type === 'error' ? `
                                            <svg class="h-5 w-5 ${iconColor}" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                                            </svg>
                                        ` : `
                                            <svg class="h-5 w-5 ${iconColor}" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                                            </svg>
                                        `}
                        </div>
                        <div class="ml-3 flex-1">
                            <h4 class="text-sm font-bold ${textColor}">${title}</h4>
                            <p class="text-sm ${textColor} opacity-90">${message}</p>
                        </div>
                        <div class="ml-4 flex-shrink-0">
                            <button onclick="this.parentElement.parentElement.parentElement.remove()" class="rounded-md p-1 ${textColor} hover:opacity-75 focus:outline-none">
                                <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>
            `;

            document.body.appendChild(notification);

            // Show notification with animation
            setTimeout(() => {
                notification.classList.remove('translate-x-full');
            }, 100);

            // Auto remove notification
            setTimeout(() => {
                notification.classList.add('translate-x-full');
                setTimeout(() => {
                    if (notification.parentNode) {
                        notification.parentNode.removeChild(notification);
                    }
                }, 300);
            }, duration);
        }

        // Placeholder functions for future implementation
        function showDetail(submissionId) {
            currentSubmissionId = submissionId;
            console.log('Show detail for submission:', submissionId);
            // TODO: Implement detail modal
        }

        function showActions(submissionId) {
            currentSubmissionId = submissionId;
            console.log('Show actions for submission:', submissionId);
            // TODO: Implement actions modal
        }

        function approveSubmission() {
            console.log('Approve submission:', currentSubmissionId);
            // TODO: Implement approval logic
        }

        function rejectSubmission() {
            console.log('Reject submission:', currentSubmissionId);
            // TODO: Implement rejection logic
        }

        // Keyboard shortcuts
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                const modals = document.querySelectorAll('.modal-overlay:not(.hidden)');
                modals.forEach(modal => {
                    modal.classList.add('hidden');
                });
            }
        });
    </script>
</body>

</html>
