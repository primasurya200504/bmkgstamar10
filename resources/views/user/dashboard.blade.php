<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard User - BMKG Pontianak</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>

    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f8fafc;
            color: #1f2937;
        }

        .sidebar {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
        }

        .main-content {
            background-color: #f8fafc;
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
    </style>
</head>

<body class="flex min-h-screen">
    <!-- Sidebar -->
    <div class="sidebar w-72 flex flex-col shadow-2xl">
        <div class="flex items-center justify-center h-20 border-b border-white border-opacity-20">
            <div class="text-center">
                <h1 class="text-white text-xl font-bold">BMKG STAMAR</h1>
                <p class="text-white text-sm opacity-80">Pontianak</p>
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

            <a href="#guidelines" id="nav-guidelines"
                class="flex items-center px-4 py-3 text-white hover:bg-white hover:bg-opacity-20 rounded-xl transition-all duration-200">
                <svg class="w-6 h-6 mr-4" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd"
                        d="M3 4a1 1 0 011-1h12a1 1 0 011 1v2a1 1 0 01-1 1H4a1 1 0 01-1-1V4zm0 4a1 1 0 011-1h12a1 1 0 011 1v2a1 1 0 01-1 1H4a1 1 0 01-1-1V8zm0 4a1 1 0 011-1h12a1 1 0 011 1v2a1 1 0 01-1 1H4a1 1 0 01-1-1v-2z"
                        clip-rule="evenodd"></path>
                </svg>
                <span class="font-medium">Panduan Pengajuan</span>
            </a>

            <a href="#submit" id="nav-submit"
                class="flex items-center px-4 py-3 text-white hover:bg-white hover:bg-opacity-20 rounded-xl transition-all duration-200">
                <svg class="w-6 h-6 mr-4" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd"
                        d="M6 2a2 2 0 00-2 2v12a2 2 0 002 2h8a2 2 0 002-2V7.414A2 2 0 0015.414 6L12 2.586A2 2 0 0010.586 2H6zm5 6a1 1 0 10-2 0v3.586l-1.293-1.293a1 1 0 10-1.414 1.414l3 3a1 1 0 001.414 0l3-3a1 1 0 00-1.414-1.414L11 11.586V8z"
                        clip-rule="evenodd"></path>
                </svg>
                <span class="font-medium">Ajukan Surat/Data</span>
            </a>

            <a href="#history" id="nav-history"
                class="flex items-center px-4 py-3 text-white hover:bg-white hover:bg-opacity-20 rounded-xl transition-all duration-200">
                <svg class="w-6 h-6 mr-4" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd"
                        d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z"
                        clip-rule="evenodd"></path>
                </svg>
                <span class="font-medium">Riwayat Pengajuan</span>
            </a>

            <a href="#profile" id="nav-profile"
                class="flex items-center px-4 py-3 text-white hover:bg-white hover:bg-opacity-20 rounded-xl transition-all duration-200">
                <svg class="w-6 h-6 mr-4" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z"
                        clip-rule="evenodd"></path>
                </svg>
                <span class="font-medium">Profil Saya</span>
            </a>
        </nav>

        <div class="px-6 py-6 border-t border-white border-opacity-20">
            <div class="flex items-center mb-6">
                <div
                    class="w-12 h-12 bg-white bg-opacity-20 rounded-full flex items-center justify-center text-white font-bold text-lg backdrop-blur-sm">
                    {{ substr(Auth::user()->name ?? 'U', 0, 1) }}
                </div>
                <div class="ml-4">
                    <p class="text-white font-semibold">{{ Auth::user()->name ?? 'User' }}</p>
                    <p class="text-white text-sm opacity-80">{{ Auth::user()->email ?? '' }}</p>
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
                    <h1 class="text-3xl font-bold text-gray-900">Selamat Datang!</h1>
                    <p class="text-gray-600 mt-2">Portal Pengajuan Data Meteorologi, Klimatologi & Geofisika</p>
                    <p class="text-sm text-gray-500 mt-1">{{ now()->format('l, d F Y') }}</p>
                </div>

                <div class="flex items-center space-x-4">
                    <div class="text-right">
                        <p class="text-sm text-gray-600">Total Pengajuan Anda</p>
                        <p class="text-2xl font-bold text-indigo-600">{{ $stats['total'] ?? 0 }}</p>
                    </div>
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
                                <p class="text-3xl font-bold text-gray-900">{{ $stats['pending'] ?? 0 }}</p>
                                <p class="text-xs text-gray-500 mt-1">Menunggu review</p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white rounded-2xl shadow-lg p-6 card-hover border border-gray-100">
                        <div class="flex items-center">
                            <div class="p-4 rounded-xl bg-blue-100">
                                <svg class="w-8 h-8 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M4 2a1 1 0 011 1v2.101a7.002 7.002 0 0111.601 2.566 1 1 0 11-1.885.666A5.002 5.002 0 005.999 7H9a1 1 0 010 2H4a1 1 0 01-1-1V3a1 1 0 011-1zm.008 9.057a1 1 0 011.276.61A5.002 5.002 0 0014.001 13H11a1 1 0 110-2h5a1 1 0 011 1v5a1 1 0 11-2 0v-2.101a7.002 7.002 0 01-11.601-2.566 1 1 0 01.61-1.276z"
                                        clip-rule="evenodd"></path>
                                </svg>
                            </div>
                            <div class="ml-5">
                                <p class="text-sm font-semibold text-gray-600 uppercase tracking-wide">Diproses</p>
                                <p class="text-3xl font-bold text-gray-900">{{ $stats['in_process'] ?? 0 }}</p>
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
                                <p class="text-xs text-gray-500 mt-1">Siap diambil</p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white rounded-2xl shadow-lg p-6 card-hover border border-gray-100">
                        <div class="flex items-center">
                            <div class="p-4 rounded-xl bg-red-100">
                                <svg class="w-8 h-8 text-red-600" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                                        clip-rule="evenodd"></path>
                                </svg>
                            </div>
                            <div class="ml-5">
                                <p class="text-sm font-semibold text-gray-600 uppercase tracking-wide">Ditolak</p>
                                <p class="text-3xl font-bold text-gray-900">{{ $stats['rejected'] ?? 0 }}</p>
                                <p class="text-xs text-gray-500 mt-1">Perlu revisi</p>
                            </div>
                        </div>
                    </div>

                    <div
                        class="bg-gradient-to-br from-indigo-500 to-purple-600 rounded-2xl shadow-lg p-6 card-hover text-white">
                        <div class="flex items-center">
                            <div class="p-4 rounded-xl bg-white bg-opacity-20">
                                <svg class="w-8 h-8 text-white" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M3 4a1 1 0 011-1h12a1 1 0 011 1v2a1 1 0 01-1 1H4a1 1 0 01-1-1V4zm0 4a1 1 0 011-1h12a1 1 0 011 1v8a1 1 0 01-1 1H4a1 1 0 01-1-1V8zm8 4a1 1 0 01-1-1v-1a1 1 0 00-1-1H8a1 1 0 01-1-1V9a1 1 0 011-1h1a1 1 0 001-1V6a1 1 0 112 0v1a1 1 0 001 1h1a1 1 0 110 2h-1a1 1 0 00-1 1v1a1 1 0 01-1 1z"
                                        clip-rule="evenodd"></path>
                                </svg>
                            </div>
                            <div class="ml-5">
                                <p class="text-sm font-semibold opacity-90 uppercase tracking-wide">Total</p>
                                <p class="text-3xl font-bold">{{ $stats['total'] ?? 0 }}</p>
                                <p class="text-xs opacity-80 mt-1">Seluruh pengajuan</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-8">
                    <div class="lg:col-span-2">
                        <div class="bg-white rounded-2xl shadow-lg p-8 card-hover border border-gray-100">
                            <div class="flex items-center justify-between mb-6">
                                <h3 class="text-2xl font-bold text-gray-900">Pengajuan Terbaru</h3>
                                <button onclick="showSection('history')"
                                    class="text-indigo-600 hover:text-indigo-800 font-semibold transition-colors">
                                    Lihat Semua →
                                </button>
                            </div>

                            <div class="space-y-4">
                                @if (isset($recentActivities) && count($recentActivities) > 0)
                                    @foreach ($recentActivities as $activity)
                                        <div
                                            class="flex items-center justify-between p-4 bg-gray-50 rounded-xl hover:bg-gray-100 transition-colors">
                                            <div class="flex items-center space-x-4">
                                                <div
                                                    class="w-12 h-12 rounded-full bg-indigo-100 flex items-center justify-center">
                                                    <svg class="w-6 h-6 text-indigo-600" fill="currentColor"
                                                        viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd"
                                                            d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4z"
                                                            clip-rule="evenodd"></path>
                                                    </svg>
                                                </div>
                                                <div>
                                                    <h4 class="font-semibold text-gray-900">
                                                        {{ $activity['guideline']['title'] ?? 'Data Request' }}</h4>
                                                    <p class="text-sm text-gray-500">
                                                        {{ $activity['submission_number'] ?? 'SUB-000' }} •
                                                        {{ isset($activity['created_at']) ? $activity['created_at']->diffForHumans() : 'Unknown' }}
                                                    </p>
                                                </div>
                                            </div>
                                            <div class="text-right">
                                                @php
                                                    $status = $activity['status'] ?? 'pending';
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
                                                    class="status-badge {{ $statusConfig[$status] ?? 'bg-gray-100 text-gray-800' }}">
                                                    {{ get_status_text($status) }}
                                                </span>
                                            </div>
                                        </div>
                                    @endforeach
                                @else
                                    <div class="text-center py-8">
                                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none"
                                            viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                        </svg>
                                        <h3 class="mt-2 text-sm font-medium text-gray-900">Belum ada pengajuan</h3>
                                        <p class="mt-1 text-sm text-gray-500">Mulai dengan mengajukan surat/data
                                            pertama Anda.</p>
                                        <div class="mt-6">
                                            <button onclick="showSection('submit')"
                                                class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700">
                                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                                </svg>
                                                Ajukan Sekarang
                                            </button>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="space-y-6">
                        <!-- Quick Submit Card -->
                        <div
                            class="bg-gradient-to-br from-blue-500 to-indigo-600 rounded-2xl shadow-lg p-6 text-white card-hover">
                            <div class="flex items-center justify-between mb-4">
                                <h3 class="text-xl font-bold">Ajukan Surat/Data</h3>
                                <svg class="w-8 h-8 opacity-80" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M6 2a2 2 0 00-2 2v12a2 2 0 002 2h8a2 2 0 002-2V7.414A2 2 0 0015.414 6L12 2.586A2 2 0 0010.586 2H6zm5 6a1 1 0 10-2 0v3.586l-1.293-1.293a1 1 0 10-1.414 1.414l3 3a1 1 0 001.414 0l3-3a1 1 0 00-1.414-1.414L11 11.586V8z"
                                        clip-rule="evenodd"></path>
                                </svg>
                            </div>
                            <p class="text-blue-100 mb-6">Ajukan permintaan data meteorologi, klimatologi, dan
                                geofisika dengan mudah dan cepat.</p>
                            <button onclick="showSection('submit')"
                                class="w-full bg-white bg-opacity-20 hover:bg-opacity-30 text-white font-semibold py-3 px-4 rounded-xl transition-all duration-200 backdrop-blur-sm border border-white border-opacity-20">
                                Mulai Pengajuan →
                            </button>
                        </div>

                        <!-- Help Card -->
                        <div class="bg-white rounded-2xl shadow-lg p-6 card-hover border border-gray-100">
                            <div class="flex items-center justify-between mb-4">
                                <h3 class="text-xl font-bold text-gray-900">Bantuan & Panduan</h3>
                                <svg class="w-8 h-8 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-8-3a1 1 0 00-.867.5 1 1 0 11-1.731-1A3 3 0 0113 8a3.001 3.001 0 01-2 2.83V11a1 1 0 11-2 0v-1a1 1 0 011-1 1 1 0 100-2zm0 8a1 1 0 100-2 1 1 0 000 2z"
                                        clip-rule="evenodd"></path>
                                </svg>
                            </div>
                            <p class="text-gray-600 mb-6">Pelajari panduan lengkap untuk mengajukan berbagai jenis data
                                dan surat.</p>
                            <button onclick="showSection('guidelines')"
                                class="w-full bg-green-50 hover:bg-green-100 text-green-700 font-semibold py-3 px-4 rounded-xl transition-all duration-200 border border-green-200">
                                Lihat Panduan →
                            </button>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Guidelines Section - FIXED -->
            <section id="guidelines" class="content-section hidden">
                <div class="bg-white rounded-2xl shadow-lg p-8 card-hover border border-gray-100">
                    <div class="flex items-center justify-between mb-8">
                        <div>
                            <h2 class="text-3xl font-bold text-gray-900">Panduan Pengajuan</h2>
                            <p class="text-gray-600 mt-2">Pilih jenis data atau surat yang ingin Anda ajukan</p>
                        </div>
                        <div class="text-right">
                            <p class="text-sm text-gray-500">{{ count($guidelines ?? []) }} jenis layanan tersedia</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6" id="guidelinesContainer">
                        @if (isset($guidelines) && count($guidelines) > 0)
                            @foreach ($guidelines as $guideline)
                                @php
                                    // Safe handling untuk required_documents
                                    $requiredDocs = safe_json_decode($guideline->required_documents, []);
                                @endphp
                                <div
                                    class="bg-gradient-to-br from-gray-50 to-white border border-gray-200 rounded-2xl p-6 card-hover group">
                                    <div class="flex items-center justify-between mb-4">
                                        <div
                                            class="p-3 rounded-xl bg-indigo-100 group-hover:bg-indigo-200 transition-colors">
                                            <svg class="w-8 h-8 text-indigo-600" fill="currentColor"
                                                viewBox="0 0 20 20">
                                                <path fill-rule="evenodd"
                                                    d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4z"
                                                    clip-rule="evenodd"></path>
                                            </svg>
                                        </div>
                                        <span
                                            class="text-xs font-semibold px-3 py-1 rounded-full {{ $guideline->type === 'pnbp' ? 'bg-yellow-100 text-yellow-800' : 'bg-green-100 text-green-800' }}">
                                            {{ $guideline->type === 'pnbp' ? 'PNBP' : 'GRATIS' }}
                                        </span>
                                    </div>

                                    <h3
                                        class="text-xl font-bold text-gray-900 mb-3 group-hover:text-indigo-600 transition-colors">
                                        {{ $guideline->title ?? 'Untitled' }}
                                    </h3>

                                    <p class="text-gray-600 mb-4 line-clamp-3">
                                        {{ $guideline->description ?? 'No description available' }}
                                    </p>

                                    <div class="space-y-3">
                                        <div class="flex items-center justify-between">
                                            <span class="text-sm font-medium text-gray-700">Biaya:</span>
                                            <span
                                                class="text-lg font-bold {{ ($guideline->fee ?? 0) > 0 ? 'text-orange-600' : 'text-green-600' }}">
                                                {{ ($guideline->fee ?? 0) > 0 ? 'Rp ' . format_currency($guideline->fee) : 'GRATIS' }}
                                            </span>
                                        </div>

                                        @if (count($requiredDocs) > 0)
                                            <div>
                                                <span class="text-sm font-medium text-gray-700">Dokumen yang
                                                    diperlukan:</span>
                                                <ul class="mt-2 space-y-1">
                                                    @foreach ($requiredDocs as $doc)
                                                        <li class="text-sm text-gray-600 flex items-center">
                                                            <svg class="w-4 h-4 text-green-500 mr-2"
                                                                fill="currentColor" viewBox="0 0 20 20">
                                                                <path fill-rule="evenodd"
                                                                    d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                                                    clip-rule="evenodd"></path>
                                                            </svg>
                                                            {{ is_string($doc) ? $doc : 'Document' }}
                                                        </li>
                                                    @endforeach
                                                </ul>
                                            </div>
                                        @endif

                                        <button onclick="selectGuideline({{ $guideline->id }})"
                                            class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-3 px-4 rounded-xl transition-all duration-200 group-hover:shadow-lg">
                                            Pilih Layanan Ini
                                        </button>
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <div class="col-span-full text-center py-12">
                                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                                <h3 class="mt-2 text-sm font-medium text-gray-900">Tidak ada panduan tersedia</h3>
                                <p class="mt-1 text-sm text-gray-500">Hubungi administrator untuk informasi lebih
                                    lanjut.</p>
                            </div>
                        @endif
                    </div>
                </div>
            </section>

            <!-- Submit Section -->
            <section id="submit" class="content-section hidden">
                <div class="max-w-4xl mx-auto">
                    <div class="bg-white rounded-2xl shadow-lg p-8 card-hover border border-gray-100">
                        <div class="text-center mb-8">
                            <div
                                class="w-20 h-20 mx-auto bg-indigo-100 rounded-full flex items-center justify-center mb-4">
                                <svg class="w-10 h-10 text-indigo-600" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M6 2a2 2 0 00-2 2v12a2 2 0 002 2h8a2 2 0 002-2V7.414A2 2 0 0015.414 6L12 2.586A2 2 0 0010.586 2H6zm5 6a1 1 0 10-2 0v3.586l-1.293-1.293a1 1 0 10-1.414 1.414l3 3a1 1 0 001.414 0l3-3a1 1 0 00-1.414-1.414L11 11.586V8z"
                                        clip-rule="evenodd"></path>
                                </svg>
                            </div>
                            <h2 class="text-3xl font-bold text-gray-900">Ajukan Surat/Data</h2>
                            <p class="text-gray-600 mt-2">Isi formulir di bawah untuk mengajukan permintaan data atau
                                surat</p>
                        </div>

                        <form id="submissionForm" class="space-y-8">
                            @csrf

                            <!-- Guideline Selection -->
                            <div>
                                <label for="guideline_id" class="block text-sm font-semibold text-gray-700 mb-3">Jenis
                                    Layanan</label>
                                <select id="guideline_id" name="guideline_id" required
                                    class="w-full p-4 border border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all duration-200">
                                    <option value="">Pilih jenis layanan yang diinginkan</option>
                                    @if (isset($guidelines))
                                        @foreach ($guidelines as $guideline)
                                            <option value="{{ $guideline->id }}" data-type="{{ $guideline->type }}"
                                                data-fee="{{ $guideline->fee ?? 0 }}"
                                                data-documents='{!! json_encode(safe_json_decode($guideline->required_documents, [])) !!}'>
                                                {{ $guideline->title ?? 'Untitled' }}
                                                @if (($guideline->fee ?? 0) > 0)
                                                    (Rp {{ format_currency($guideline->fee) }})
                                                @else
                                                    (GRATIS)
                                                @endif
                                            </option>
                                        @endforeach
                                    @endif
                                </select>
                                <div id="selectedGuidelineInfo"
                                    class="hidden mt-3 p-4 bg-blue-50 rounded-xl border border-blue-200">
                                    <!-- Will be populated by JavaScript -->
                                </div>
                            </div>

                            <!-- Purpose -->
                            <div>
                                <label for="purpose" class="block text-sm font-semibold text-gray-700 mb-3">Tujuan
                                    Penggunaan Data</label>
                                <textarea id="purpose" name="purpose" rows="4" required
                                    class="w-full p-4 border border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all duration-200"
                                    placeholder="Jelaskan secara detail untuk keperluan apa data ini akan digunakan..."></textarea>
                            </div>

                            <!-- Date Range -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label for="start_date"
                                        class="block text-sm font-semibold text-gray-700 mb-3">Tanggal Mulai
                                        Data</label>
                                    <input type="date" id="start_date" name="start_date" required
                                        class="w-full p-4 border border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all duration-200">
                                </div>
                                <div>
                                    <label for="end_date"
                                        class="block text-sm font-semibold text-gray-700 mb-3">Tanggal Akhir
                                        Data</label>
                                    <input type="date" id="end_date" name="end_date" required
                                        class="w-full p-4 border border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all duration-200">
                                </div>
                            </div>

                            <!-- Document Upload -->
                            <div id="documentsSection" class="hidden">
                                <label class="block text-sm font-semibold text-gray-700 mb-3">Upload Dokumen
                                    Pendukung</label>
                                <div id="documentFields" class="space-y-4">
                                    <!-- Will be populated by JavaScript -->
                                </div>
                            </div>

                            <!-- Submit Button -->
                            <div class="pt-6 border-t border-gray-200">
                                <button type="submit" id="submitBtn"
                                    class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-4 px-6 rounded-xl transition-all duration-200 shadow-lg hover:shadow-xl">
                                    <span id="submitBtnText">Ajukan Permohonan</span>
                                    <svg id="submitBtnLoader"
                                        class="hidden animate-spin -ml-1 mr-3 h-5 w-5 text-white inline"
                                        xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10"
                                            stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor"
                                            d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                        </path>
                                    </svg>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </section>

            <!-- History Section -->
            <section id="history" class="content-section hidden">
                <div class="bg-white rounded-2xl shadow-lg p-8 card-hover border border-gray-100">
                    <div class="flex items-center justify-between mb-8">
                        <div>
                            <h2 class="text-3xl font-bold text-gray-900">Riwayat Pengajuan</h2>
                            <p class="text-gray-600 mt-2">Daftar semua pengajuan surat dan data Anda</p>
                        </div>
                        <div class="flex items-center space-x-4">
                            <select id="statusFilter"
                                class="p-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500">
                                <option value="">Semua Status</option>
                                <option value="pending">Menunggu Review</option>
                                <option value="Diproses">Menunggu Upload e-Billing</option>
                                <option value="verified">Terverifikasi</option>
                                <option value="payment_pending">Menunggu Pembayaran</option>
                                <option value="paid">Sudah Bayar</option>
                                <option value="processing">Sedang Diproses</option>
                                <option value="completed">Selesai</option>
                                <option value="rejected">Ditolak</option>
                            </select>
                            <button onclick="loadSubmissions()"
                                class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-3 rounded-xl font-medium transition-colors">
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
                                        <td colspan="6" class="px-6 py-8 text-center text-gray-500">
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

            <!-- Profile Section -->
            <section id="profile" class="content-section hidden">
                <div class="max-w-2xl mx-auto">
                    <div class="bg-white rounded-2xl shadow-lg p-8 card-hover border border-gray-100">
                        <div class="text-center mb-8">
                            <div
                                class="w-24 h-24 mx-auto bg-indigo-100 rounded-full flex items-center justify-center mb-4">
                                <span
                                    class="text-3xl font-bold text-indigo-600">{{ substr(Auth::user()->name ?? 'U', 0, 1) }}</span>
                            </div>
                            <h2 class="text-3xl font-bold text-gray-900">Profil Saya</h2>
                            <p class="text-gray-600 mt-2">Kelola informasi akun Anda</p>
                        </div>

                        <form id="profileForm" class="space-y-6">
                            @csrf
                            @method('PUT')

                            <div>
                                <label for="name" class="block text-sm font-semibold text-gray-700 mb-3">Nama
                                    Lengkap</label>
                                <input type="text" id="name" name="name"
                                    value="{{ Auth::user()->name ?? '' }}" required
                                    class="w-full p-4 border border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all duration-200">
                            </div>

                            <div>
                                <label for="email"
                                    class="block text-sm font-semibold text-gray-700 mb-3">Email</label>
                                <input type="email" id="email" name="email"
                                    value="{{ Auth::user()->email ?? '' }}" required
                                    class="w-full p-4 border border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all duration-200">
                            </div>

                            <div>
                                <label for="phone" class="block text-sm font-semibold text-gray-700 mb-3">Nomor
                                    Telepon</label>
                                <input type="tel" id="phone" name="phone"
                                    value="{{ Auth::user()->phone ?? '' }}"
                                    class="w-full p-4 border border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all duration-200"
                                    placeholder="Contoh: 081234567890">
                            </div>

                            <div>
                                <label for="password" class="block text-sm font-semibold text-gray-700 mb-3">Password
                                    Baru (Opsional)</label>
                                <input type="password" id="password" name="password"
                                    class="w-full p-4 border border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all duration-200"
                                    placeholder="Kosongkan jika tidak ingin mengubah password">
                            </div>

                            <div>
                                <label for="password_confirmation"
                                    class="block text-sm font-semibold text-gray-700 mb-3">Konfirmasi Password</label>
                                <input type="password" id="password_confirmation" name="password_confirmation"
                                    class="w-full p-4 border border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all duration-200"
                                    placeholder="Konfirmasi password baru">
                            </div>

                            <div class="pt-6 border-t border-gray-200">
                                <button type="submit"
                                    class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-4 px-6 rounded-xl transition-all duration-200 shadow-lg hover:shadow-xl">
                                    Update Profil
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </section>
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
        </div>
    </div>

    <!-- Payment Modal -->
    <div id="paymentModal"
        class="modal-overlay hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white rounded-2xl p-8 w-full max-w-2xl mx-4">
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-2xl font-bold text-gray-900">Upload Bukti Pembayaran</h3>
                <button onclick="hideModal('paymentModal')" class="text-gray-500 hover:text-gray-700 p-2">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            <div id="paymentInfo" class="mb-6">
                <!-- Payment info will be populated by JavaScript -->
            </div>

            <form id="paymentForm" class="space-y-6">
                @csrf
                <input type="hidden" id="paymentSubmissionId" name="submission_id">

                <div>
                    <label for="payment_method" class="block text-sm font-semibold text-gray-700 mb-3">Metode
                        Pembayaran</label>
                    <select id="payment_method" name="payment_method" required
                        class="w-full p-4 border border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500">
                        <option value="">Pilih metode pembayaran</option>
                        <option value="Transfer Bank">Transfer Bank</option>
                        <option value="E-Wallet">E-Wallet</option>
                        <option value="Cash">Tunai</option>
                    </select>
                </div>

                <div>
                    <label for="payment_reference" class="block text-sm font-semibold text-gray-700 mb-3">Nomor
                        Referensi</label>
                    <input type="text" id="payment_reference" name="payment_reference"
                        class="w-full p-4 border border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500"
                        placeholder="Nomor transaksi/referensi pembayaran">
                </div>

                <div>
                    <label for="payment_proof" class="block text-sm font-semibold text-gray-700 mb-3">Bukti
                        Pembayaran</label>
                    <input type="file" id="payment_proof" name="payment_proof" accept="image/*" required
                        class="w-full p-4 border border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500">
                    <p class="text-sm text-gray-500 mt-2">Upload foto/scan bukti pembayaran (JPG, PNG, max 5MB)</p>
                </div>

                <div class="pt-6 border-t border-gray-200">
                    <button type="submit"
                        class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-4 px-6 rounded-xl transition-all duration-200">
                        Upload Bukti Pembayaran
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Upload Files Modal -->
    <div id="uploadModal"
        class="modal-overlay hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white rounded-2xl p-8 w-full max-w-2xl mx-4">
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-2xl font-bold text-gray-900">Upload File Tambahan</h3>
                <button onclick="hideModal('uploadModal')" class="text-gray-500 hover:text-gray-700 p-2">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            <div class="mb-6">
                <p class="text-sm text-gray-600">Upload file tambahan untuk pengajuan yang ditolak. Pastikan file sudah diperbaiki sesuai dengan alasan penolakan.</p>
            </div>

            <form id="uploadForm" class="space-y-6">
                @csrf
                <input type="hidden" id="uploadSubmissionId" name="submission_id">

                <div id="fileFields">
                    <!-- File fields will be added here by JavaScript -->
                </div>

                <div class="flex justify-between">
                    <button type="button" onclick="addFileField()" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                        Tambah File
                    </button>
                    <div class="space-x-2">
                        <button type="button" onclick="hideModal('uploadModal')" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                            Batal
                        </button>
                        <button type="submit" class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
                            Upload File
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Global variables
        let currentSubmissionId = null;
        let currentPaymentAmount = 0;

        // Initialize dashboard
        document.addEventListener('DOMContentLoaded', function() {
            initializeNavigation();
            loadSubmissions();
            setupFormHandlers();
        });

        // Navigation functionality
        function initializeNavigation() {
            const navLinks = document.querySelectorAll('[id^="nav-"]');
            const sections = {
                'dashboard': document.getElementById('dashboard'),
                'guidelines': document.getElementById('guidelines'),
                'submit': document.getElementById('submit'),
                'history': document.getElementById('history'),
                'profile': document.getElementById('profile')
            };

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
            const sections = ['dashboard', 'guidelines', 'submit', 'history', 'profile'];
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
            if (sectionId === 'history') {
                loadSubmissions();
            }
        }

        // Setup form handlers - FIXED
        function setupFormHandlers() {
            // Guideline selection handler
            const guidelineSelect = document.getElementById('guideline_id');
            if (guidelineSelect) {
                guidelineSelect.addEventListener('change', function() {
                    const selectedOption = this.options[this.selectedIndex];
                    const infoDiv = document.getElementById('selectedGuidelineInfo');
                    const documentsSection = document.getElementById('documentsSection');

                    if (selectedOption.value) {
                        const type = selectedOption.getAttribute('data-type');
                        const fee = selectedOption.getAttribute('data-fee');
                        const documentsData = selectedOption.getAttribute('data-documents');

                        // Safe JSON parse - FIXED
                        let documents = [];
                        try {
                            if (documentsData && documentsData !== 'null' && documentsData !== '' &&
                                documentsData !== '[]') {
                                documents = JSON.parse(documentsData);
                                if (!Array.isArray(documents)) {
                                    documents = [];
                                }
                            }
                        } catch (e) {
                            console.error('Error parsing documents JSON:', e);
                            documents = [];
                        }

                        // Show guideline info
                        infoDiv.innerHTML = `
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <h4 class="font-semibold text-blue-900">Tipe Layanan</h4>
                                    <p class="text-blue-700">${type === 'pnbp' ? 'PNBP (Berbayar)' : 'Non-PNBP (Gratis)'}</p>
                                </div>
                                <div>
                                    <h4 class="font-semibold text-blue-900">Biaya</h4>
                                    <p class="text-blue-700">${fee > 0 ? 'Rp ' + new Intl.NumberFormat('id-ID').format(fee) : 'GRATIS'}</p>
                                </div>
                            </div>
                        `;
                        infoDiv.classList.remove('hidden');

                        // Show document fields based on required_documents array
                        const requiredFilesCount = documents.length;
                        const documentFields = document.getElementById('documentFields');
                        if (requiredFilesCount > 0) {
                            documentFields.innerHTML = documents.map((docName, index) => `
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">${docName || `Dokumen ${index + 1}`}</label>
                                    <input type="file" name="files[]" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png" required
                                           class="w-full p-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500">
                                    <p class="text-xs text-gray-500 mt-1">Format: PDF, DOC, DOCX, JPG, PNG (Max 5MB)</p>
                                </div>
                            `).join('');
                            documentsSection.classList.remove('hidden');
                        } else {
                            documentsSection.classList.add('hidden');
                        }
                    } else {
                        infoDiv.classList.add('hidden');
                        documentsSection.classList.add('hidden');
                    }
                });
            }

            // Rest of the form handlers...
            const submissionForm = document.getElementById('submissionForm');
            if (submissionForm) {
                submissionForm.addEventListener('submit', async function(e) {
                    e.preventDefault();
                    await submitForm();
                });
            }

            const profileForm = document.getElementById('profileForm');
            if (profileForm) {
                profileForm.addEventListener('submit', async function(e) {
                    e.preventDefault();
                    await updateProfile();
                });
            }

            const paymentForm = document.getElementById('paymentForm');
            if (paymentForm) {
                paymentForm.addEventListener('submit', async function(e) {
                    e.preventDefault();
                    await uploadPayment();
                });
            }

            const statusFilter = document.getElementById('statusFilter');
            if (statusFilter) {
                statusFilter.addEventListener('change', function() {
                    loadSubmissions();
                });
            }
        }

        // REST OF THE JAVASCRIPT FUNCTIONS (same as before)
        async function submitForm() {
            const submitBtn = document.getElementById('submitBtn');
            const submitBtnText = document.getElementById('submitBtnText');
            const submitBtnLoader = document.getElementById('submitBtnLoader');

            // Show loading state
            submitBtn.disabled = true;
            submitBtnText.textContent = 'Mengirim...';
            submitBtnLoader.classList.remove('hidden');

            try {
                const formData = new FormData(document.getElementById('submissionForm'));

                const response = await fetch('/user/submissions', {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                            'content')
                    }
                });

                const result = await response.json();

                if (result.success) {
                    alert('Pengajuan berhasil dikirim! Silakan tunggu verifikasi dari admin.');
                    document.getElementById('submissionForm').reset();
                    document.getElementById('selectedGuidelineInfo').classList.add('hidden');
                    document.getElementById('documentsSection').classList.add('hidden');
                    showSection('history');
                    // Refresh data
                    loadSubmissions();
                } else {
                    alert('Gagal mengirim pengajuan: ' + (result.message || 'Terjadi kesalahan'));
                }
            } catch (error) {
                console.error('Error submitting form:', error);
                alert('Terjadi kesalahan saat mengirim pengajuan: ' + error.message);
            } finally {
                // Reset button state
                submitBtn.disabled = false;
                submitBtnText.textContent = 'Ajukan Permohonan';
                submitBtnLoader.classList.add('hidden');
            }
        }


        async function updateProfile() {
            try {
                const formData = new FormData(document.getElementById('profileForm'));

                const response = await fetch('/user/profile', {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                            'content')
                    }
                });

                const result = await response.json();

                if (result.success) {
                    alert('Profil berhasil diperbarui!');
                } else {
                    alert('Gagal memperbarui profil: ' + (result.message || 'Terjadi kesalahan'));
                }
            } catch (error) {
                console.error('Error updating profile:', error);
                alert('Terjadi kesalahan saat memperbarui profil');
            }
        }

        async function loadSubmissions() {
            try {
                const statusFilter = document.getElementById('statusFilter');
                const statusValue = statusFilter ? statusFilter.value : '';
                const url = statusValue ? `/user/submissions?status=${statusValue}` : '/user/submissions';

                const response = await fetch(url, {
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                            'content')
                    }
                });

                const data = await response.json();
                const tbody = document.getElementById('submissionsTableBody');

                if (data.success && data.data.length > 0) {
                    tbody.innerHTML = data.data.map(submission => {
                        const statusConfig = {
                            'pending': 'bg-yellow-100 text-yellow-800',
                            'Diproses': 'bg-indigo-100 text-indigo-800',
                            'verified': 'bg-blue-100 text-blue-800',
                            'payment_pending': 'bg-orange-100 text-orange-800',
                            'proof_uploaded': 'bg-blue-100 text-blue-800',
                            'paid': 'bg-purple-100 text-purple-800',
                            'processing': 'bg-indigo-100 text-indigo-800',
                            'completed': 'bg-green-100 text-green-800',
                            'rejected': 'bg-red-100 text-red-800'
                        };

                        return `
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                    ${submission.submission_number || 'SUB-' + String(submission.id).padStart(4, '0')}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    ${submission.guideline ? submission.guideline.title : 'N/A'}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="status-badge ${submission.type === 'pnbp' ? 'bg-yellow-100 text-yellow-800' : 'bg-green-100 text-green-800'}">
                                        ${submission.type === 'pnbp' ? 'PNBP' : 'Non-PNBP'}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="status-badge ${
                                        (() => {
                                            let statusText = getStatusText(submission.status);
                                            let statusClass = statusConfig[submission.status] || 'bg-gray-100 text-gray-800';
                                            if (submission.payment && submission.payment.rejection_reason) {
                                                statusText = 'Ditolak - Menunggu Upload Ulang';
                                                statusClass = 'bg-red-100 text-red-800';
                                            } else if (submission.status === 'payment_pending' && (!submission.payment || !submission.payment.e_billing_path)) {
                                                statusText = 'Menunggu Verifikasi';
                                                statusClass = 'bg-blue-100 text-blue-800';
                                            }
                                            return statusClass;
                                        })()
                                    }">
                                        ${
                                            (() => {
                                                let statusText = getStatusText(submission.status);
                                                if (submission.payment && submission.payment.rejection_reason) {
                                                    statusText = 'Ditolak - Menunggu Upload Ulang';
                                                } else if (submission.status === 'payment_pending' && (!submission.payment || !submission.payment.e_billing_path)) {
                                                    statusText = 'Menunggu Verifikasi';
                                                }
                                                return statusText;
                                            })()
                                        }
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    ${new Date(submission.created_at).toLocaleDateString('id-ID')}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <div class="flex space-x-2">
                                        <button onclick="showDetail(${submission.id})" class="text-indigo-600 hover:text-indigo-900 transition-colors">
                                            Detail
                                        </button>
                                        ${submission.status === 'rejected' ? `
                                                                    <button onclick="alert('Alasan Penolakan: ${submission.rejection_note}')" class="text-red-600 hover:text-red-900 transition-colors ml-2">
                                                                        Kesalahan
                                                                    </button>
                                                                    <button onclick="showUploadModal(${submission.id})" class="text-blue-600 hover:text-blue-900 transition-colors ml-2">
                                                                        Upload File
                                                                    </button>
                                                                    <button onclick="resubmitSubmission(${submission.id})" class="text-green-600 hover:text-green-900 transition-colors ml-2">
                                                                        Kirim Ulang
                                                                    </button>
                                                                ` : ''}
                                        ${submission.status === 'payment_pending' && submission.guideline && submission.payment && submission.payment.e_billing_path ? `
                                                                    <button onclick="showPaymentModal(${submission.id}, ${submission.guideline.fee || 0})" class="text-green-600 hover:text-green-900 transition-colors">
                                                                        Bayar
                                                                    </button>
                                                                ` : ''}
                                        ${submission.payment && submission.payment.rejection_reason ? `
                                                                    <button onclick="showPaymentModal(${submission.id}, ${submission.guideline.fee || 0})" class="text-green-600 hover:text-green-900 transition-colors ml-2">
                                                                        Upload Ulang Bukti
                                                                    </button>
                                                                ` : ''}
                                        ${submission.status !== 'completed' && submission.payment && submission.payment.e_billing_path ? `
                                                                    <a href="/storage/${submission.payment.e_billing_path}" target="_blank" class="text-blue-600 hover:text-blue-900 transition-colors">
                                                                        e-Billing
                                                                    </a>
                                                                ` : ''}
                                    </div>
                                </td>
                            </tr>
                        `;
                    }).join('');
                } else {
                    tbody.innerHTML = `
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                                <div class="flex flex-col items-center">
                                    <svg class="w-12 h-12 text-gray-400 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                    </svg>
                                    <h3 class="text-lg font-medium text-gray-900 mb-2">Belum ada pengajuan</h3>
                                    <p class="text-gray-500 mb-4">Mulai dengan mengajukan surat atau data pertama Anda.</p>
                                    <button onclick="showSection('submit')" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-xl font-medium transition-colors">
                                        Ajukan Sekarang
                                    </button>
                                </div>
                            </td>
                        </tr>
                    `;
                }
            } catch (error) {
                console.error('Error loading submissions:', error);
                const tbody = document.getElementById('submissionsTableBody');
                if (tbody) {
                    tbody.innerHTML = `
                        <tr>
                            <td colspan="6" class="px-6 py-4 text-center text-red-500">
                                Error loading data: ${error.message}
                            </td>
                        </tr>
                    `;
                }
            }
        }

        // Modal functions
        function showModal(modalId) {
            document.getElementById(modalId).classList.remove('hidden');
        }

        function hideModal(modalId) {
            document.getElementById(modalId).classList.add('hidden');
        }

        // Helper functions
        function getStatusText(status) {
            const statusTexts = {
                'pending': 'Menunggu Review',
                'Diproses': 'Menunggu Upload e-Billing',
                'verified': 'Terverifikasi',
                'payment_pending': 'Menunggu Pembayaran',
                'proof_uploaded': 'Bukti Pembayaran Diupload - Menunggu Verifikasi',
                'paid': 'Sudah Bayar',
                'processing': 'Sedang Diproses',
                'completed': 'Selesai',
                'rejected': 'Ditolak'
            };
            return statusTexts[status] || status;
        }

        function getStatusBadgeClass(status) {
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

        function selectGuideline(guidelineId) {
            showSection('submit');
            const select = document.getElementById('guideline_id');
            if (select) {
                select.value = guidelineId;
                select.dispatchEvent(new Event('change'));
            }
        }

        // Download file function
        function downloadFile(submissionId, fileId) {
            const link = document.createElement('a');
            link.href = `/user/submissions/${submissionId}/files/${fileId}/download`;
            link.download = ''; // This will use the filename from the server
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
        }

        // Show submission detail in modal
        async function showDetail(submissionId) {
            try {
                const response = await fetch(`/user/submissions/${submissionId}`, {
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                            'content')
                    }
                });

                const result = await response.json();

                if (result.success) {
                    const data = result.data;
                    const modalContent = document.getElementById('modalContent');

                    modalContent.innerHTML = `
                        <div class="space-y-6">
                            <!-- Header -->
                            <div class="border-b pb-4">
                                <h3 class="text-xl font-bold text-gray-900">Detail Pengajuan ${data.submission_number}</h3>
                                <p class="text-sm text-gray-500 mt-1">Dibuat pada ${data.created_at}</p>
                            </div>

                            <!-- Status -->
                            <div class="bg-gray-50 p-4 rounded-lg">
                                <div class="flex items-center justify-between">
                                    <span class="text-sm font-medium text-gray-700">Status:</span>
                                    <span class="px-3 py-1 text-sm font-semibold rounded-full ${getStatusBadgeClass(data.status)}">
                                        ${data.status_label}
                                    </span>
                                </div>
                            </div>

                            <!-- Rejection Reason (if rejected) -->
                            ${data.status === 'rejected' && data.rejection_note ? `
                                        <div class="bg-red-50 p-4 rounded-lg border border-red-200">
                                            <h4 class="text-sm font-semibold text-red-900 mb-2">Alasan Penolakan</h4>
                                            <p class="text-sm text-red-800 mb-3">${data.rejection_note}</p>
                                            <div class="bg-blue-50 p-3 rounded-lg border border-blue-200">
                                                <p class="text-sm text-blue-800 mb-3">
                                                    <strong>Petunjuk:</strong> Anda dapat mengupload file tambahan atau mengirim ulang pengajuan ini.
                                                </p>
                                                <div class="flex space-x-2">
                                                    <button onclick="showUploadModal(${data.id})" class="bg-green-600 hover:bg-green-700 text-white text-xs font-bold py-2 px-3 rounded">
                                                        Upload File Tambahan
                                                    </button>
                                                    <button onclick="resubmitSubmission(${data.id})" class="bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold py-2 px-3 rounded">
                                                        Kirim Ulang Pengajuan
                                                    </button>
                                                    <button onclick="showSection('submit')" class="bg-gray-600 hover:bg-gray-700 text-white text-xs font-bold py-2 px-3 rounded">
                                                        Buat Pengajuan Baru
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    ` : ''}

                            <!-- Guideline Info -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <h4 class="font-semibold text-gray-900 mb-2">Informasi Layanan</h4>
                                    <div class="space-y-2 text-sm">
                                        <p><span class="font-medium">Jenis:</span> ${data.guideline.title}</p>
                                        <p><span class="font-medium">Tipe:</span> ${data.guideline.type === 'pnbp' ? 'PNBP (Berbayar)' : 'Non-PNBP (Gratis)'}</p>
                                        ${data.guideline.fee > 0 ? `<p><span class="font-medium">Biaya:</span> Rp ${new Intl.NumberFormat('id-ID').format(data.guideline.fee)}</p>` : ''}
                                    </div>
                                </div>

                                <div>
                                    <h4 class="font-semibold text-gray-900 mb-2">Periode Data</h4>
                                    <div class="space-y-2 text-sm">
                                        <p><span class="font-medium">Mulai:</span> ${data.start_date ? new Date(data.start_date).toLocaleDateString('id-ID') : 'N/A'}</p>
                                        <p><span class="font-medium">Akhir:</span> ${data.end_date ? new Date(data.end_date).toLocaleDateString('id-ID') : 'N/A'}</p>
                                    </div>
                                </div>
                            </div>

                            <!-- Purpose -->
                            <div>
                                <h4 class="font-semibold text-gray-900 mb-2">Tujuan Penggunaan</h4>
                                <p class="text-sm text-gray-700 bg-gray-50 p-3 rounded-lg">${data.purpose}</p>
                            </div>

                            <!-- Payment Info (if exists) -->
                            ${data.payment ? `
                                        <div>
                                            <h4 class="font-semibold text-gray-900 mb-2">Informasi Pembayaran</h4>
                                            <div class="bg-blue-50 p-4 rounded-lg">
                                                <div class="grid grid-cols-2 gap-4 text-sm">
                                                    <div>
                                                        <p><span class="font-medium">Jumlah:</span> Rp ${new Intl.NumberFormat('id-ID').format(data.payment.amount)}</p>
                                                        <p><span class="font-medium">Status:</span> ${data.payment.status === 'verified' ? 'Terverifikasi' : data.payment.status === 'uploaded' ? 'Menunggu Verifikasi' : 'Pending'}</p>
                                                    </div>
                                                    <div>
                                                        ${data.payment.method ? `<p><span class="font-medium">Metode:</span> ${data.payment.method}</p>` : ''}
                                                        ${data.payment.reference ? `<p><span class="font-medium">Referensi:</span> ${data.payment.reference}</p>` : ''}
                                                        ${data.payment.paid_at ? `<p><span class="font-medium">Dibayar:</span> ${data.payment.paid_at}</p>` : ''}
                                                    </div>
                                                </div>
                                                ${data.payment.e_billing_path ? `
                                            <div class="mt-4 pt-4 border-t border-blue-200">
                                                <h5 class="font-medium text-blue-900 mb-2">File e-Billing dari Admin</h5>
                                                <div class="flex items-center justify-between bg-white p-3 rounded-lg">
                                                    <div class="flex items-center space-x-3">
                                                        <svg class="w-5 h-5 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                                                            <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4z" clip-rule="evenodd"></path>
                                                        </svg>
                                                        <div>
                                                            <p class="text-sm font-medium text-gray-900">${data.payment.e_billing_filename || 'e-Billing.pdf'}</p>
                                                            <p class="text-xs text-gray-500">Diupload oleh Admin</p>
                                                        </div>
                                                    </div>
                                                    <a href="/storage/${data.payment.e_billing_path}" target="_blank" class="bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold py-2 px-3 rounded">
                                                        Download
                                                    </a>
                                                </div>
                                            </div>
                                        ` : ''}
                                                ${data.payment.rejection_reason ? `
                                            <div class="mt-4 pt-4 border-t border-red-200">
                                                <h5 class="font-medium text-red-900 mb-2">Alasan Penolakan Pembayaran</h5>
                                                <div class="bg-red-50 p-3 rounded-lg border border-red-200">
                                                    <p class="text-sm text-red-800">${data.payment.rejection_reason}</p>
                                                    <p class="text-xs text-red-600 mt-2">Silakan upload ulang bukti pembayaran yang sesuai.</p>
                                                </div>
                                            </div>
                                        ` : ''}
                                            </div>
                                        </div>
                                    ` : ''}

                            <!-- Uploaded Files (if any) -->
                            ${data.uploaded_files && data.uploaded_files.length > 0 ? `
                                        <div>
                                            <h4 class="font-semibold text-gray-900 mb-2">Dokumen yang Diupload</h4>
                                            <div class="space-y-2">
                                                ${data.uploaded_files.map(file => `
                                            <div class="flex items-center justify-between bg-blue-50 p-3 rounded-lg">
                                                <div class="flex items-center space-x-3">
                                                    <svg class="w-5 h-5 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4z" clip-rule="evenodd"></path>
                                                    </svg>
                                                    <div>
                                                        <p class="text-sm font-medium text-gray-900">${file.document_name}</p>
                                                        <p class="text-xs text-gray-500">${file.name} • ${file.size} • Diupload: ${file.uploaded_at}</p>
                                                    </div>
                                                </div>
                                                <a href="#" onclick="downloadFile(${data.id}, ${file.id}); return false;" class="text-blue-600 hover:text-blue-800 text-xs font-medium underline">
                                                    Download
                                                </a>
                                            </div>
                                        `).join('')}
                                            </div>
                                        </div>
                                    ` : ''}

                            <!-- Generated Documents (if any) -->
                            ${data.documents && data.documents.length > 0 ? `
                                        <div>
                                            <h4 class="font-semibold text-gray-900 mb-2">Dokumen yang Dihasilkan</h4>
                                            <div class="space-y-2">
                                                ${data.documents.map(doc => `
                                            <div class="flex items-center justify-between bg-green-50 p-3 rounded-lg">
                                                <div class="flex items-center space-x-3">
                                                    <svg class="w-5 h-5 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4z" clip-rule="evenodd"></path>
                                                    </svg>
                                                    <div>
                                                        <p class="text-sm font-medium text-gray-900">${doc.name}</p>
                                                        <p class="text-xs text-gray-500">${doc.size}</p>
                                                    </div>
                                                </div>
                                                <a href="${doc.download_url}" class="bg-green-600 hover:bg-green-700 text-white text-xs font-bold py-2 px-3 rounded">
                                                    Download
                                                </a>
                                            </div>
                                        `).join('')}
                                            </div>
                                        </div>
                                    ` : ''}

                            <!-- History -->
                            ${data.histories && data.histories.length > 0 ? `
                                        <div>
                                            <h4 class="font-semibold text-gray-900 mb-2">Riwayat Pengajuan</h4>
                                            <div class="space-y-3">
                                                ${data.histories.map(history => `
                                            <div class="border-l-4 border-blue-500 pl-4 py-2">
                                                <div class="flex items-center justify-between">
                                                    <h5 class="text-sm font-medium text-gray-900">${history.title}</h5>
                                                    <span class="text-xs text-gray-500">${history.created_at}</span>
                                                </div>
                                                <p class="text-sm text-gray-600 mt-1">${history.description}</p>
                                                <p class="text-xs text-blue-600 mt-1">Oleh: ${history.actor}</p>
                                            </div>
                                        `).join('')}
                                            </div>
                                        </div>
                                    ` : ''}
                        </div>
                    `;

                    showModal('detailModal');
                } else {
                    alert('Gagal memuat detail pengajuan: ' + (result.message || 'Terjadi kesalahan'));
                }
            } catch (error) {
                console.error('Error loading submission detail:', error);
                alert('Terjadi kesalahan saat memuat detail pengajuan');
            }
        }

        function showPaymentModal(submissionId, amount) {
            currentSubmissionId = submissionId;
            currentPaymentAmount = amount;
            showModal('paymentModal');
        }

        async function uploadPayment() {
            try {
                const formData = new FormData(document.getElementById('paymentForm'));

                const response = await fetch(`/user/submissions/${currentSubmissionId}/payment`, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                            'content')
                    }
                });

                // Check if response is JSON
                const contentType = response.headers.get('content-type');
                if (contentType && contentType.includes('application/json')) {
                    const result = await response.json();

                    if (result.success) {
                        alert('Bukti pembayaran berhasil diupload! Silakan tunggu verifikasi dari admin.');
                        hideModal('paymentModal');
                        document.getElementById('paymentForm').reset();
                        loadSubmissions(); // Refresh the table
                    } else {
                        alert('Gagal upload bukti pembayaran: ' + (result.message || 'Terjadi kesalahan'));
                    }
                } else {
                    // Handle non-JSON response (likely redirect or error page)
                    if (response.status === 419) {
                        alert('Sesi telah berakhir. Silakan refresh halaman dan login kembali.');
                    } else if (response.status === 401) {
                        alert('Anda tidak memiliki akses. Silakan login kembali.');
                    } else if (response.status === 404) {
                        alert('Halaman tidak ditemukan. Silakan refresh halaman.');
                    } else {
                        alert('Terjadi kesalahan server. Status: ' + response.status);
                    }
                    console.error('Non-JSON response:', response.status, contentType);
                }
            } catch (error) {
                console.error('Error uploading payment:', error);
                alert('Terjadi kesalahan jaringan saat upload bukti pembayaran. Silakan coba lagi.');
            }
        }

        // Upload modal functions
        function showUploadModal(submissionId) {
            currentSubmissionId = submissionId;
            document.getElementById('uploadSubmissionId').value = submissionId;
            // Initialize with one file field
            document.getElementById('fileFields').innerHTML = `
                <div class="file-field">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Nama Dokumen</label>
                    <input type="text" name="document_names[]" class="w-full p-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500 mb-2" placeholder="Contoh: KTP, Surat Pengantar, dll" required>
                    <label class="block text-sm font-medium text-gray-700 mb-2">File Dokumen</label>
                    <input type="file" name="files[]" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png" class="w-full p-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500" required>
                    <p class="text-xs text-gray-500 mt-1">Format: PDF, DOC, DOCX, JPG, PNG (Max 5MB)</p>
                </div>
            `;
            showModal('uploadModal');
        }

        function addFileField() {
            const fileFields = document.getElementById('fileFields');
            const newField = document.createElement('div');
            newField.className = 'file-field mt-4 pt-4 border-t border-gray-200';
            newField.innerHTML = `
                <label class="block text-sm font-medium text-gray-700 mb-2">Nama Dokumen</label>
                <input type="text" name="document_names[]" class="w-full p-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500 mb-2" placeholder="Contoh: KTP, Surat Pengantar, dll" required>
                <label class="block text-sm font-medium text-gray-700 mb-2">File Dokumen</label>
                <input type="file" name="files[]" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png" class="w-full p-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500" required>
                <p class="text-xs text-gray-500 mt-1">Format: PDF, DOC, DOCX, JPG, PNG (Max 5MB)</p>
            `;
            fileFields.appendChild(newField);
        }

        async function uploadFiles() {
            try {
                const formData = new FormData(document.getElementById('uploadForm'));

                const response = await fetch(`/user/submissions/${currentSubmissionId}/upload-files`, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                });

                const result = await response.json();

                if (result.success) {
                    alert('File berhasil diupload!');
                    hideModal('uploadModal');
                    document.getElementById('uploadForm').reset();
                    // Refresh the detail modal if it's open
                    if (!document.getElementById('detailModal').classList.contains('hidden')) {
                        showDetail(currentSubmissionId);
                    }
                    loadSubmissions(); // Refresh the table
                } else {
                    alert('Gagal upload file: ' + (result.message || 'Terjadi kesalahan'));
                }
            } catch (error) {
                console.error('Error uploading files:', error);
                alert('Terjadi kesalahan saat upload file');
            }
        }

        async function resubmitSubmission(submissionId) {
            if (!confirm('Apakah Anda yakin ingin mengirim ulang pengajuan ini? Status akan kembali ke "Menunggu Review".')) {
                return;
            }

            try {
                const response = await fetch(`/user/submissions/${submissionId}/resubmit`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Content-Type': 'application/json'
                    }
                });

                const result = await response.json();

                if (result.success) {
                    alert('Pengajuan berhasil dikirim ulang!');
                    hideModal('detailModal');
                    loadSubmissions(); // Refresh the table
                } else {
                    alert('Gagal mengirim ulang pengajuan: ' + (result.message || 'Terjadi kesalahan'));
                }
            } catch (error) {
                console.error('Error resubmitting submission:', error);
                alert('Terjadi kesalahan saat mengirim ulang pengajuan');
            }
        }

        // Setup upload form handler
        document.addEventListener('DOMContentLoaded', function() {
            initializeNavigation();
            loadSubmissions();
            setupFormHandlers();

            const uploadForm = document.getElementById('uploadForm');
            if (uploadForm) {
                uploadForm.addEventListener('submit', async function(e) {
                    e.preventDefault();
                    await uploadFiles();
                });
            }
        });

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
