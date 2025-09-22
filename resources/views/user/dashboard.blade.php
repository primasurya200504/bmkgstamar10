<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Pengajuan Surat - BMKG Pontianak</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        [x-cloak] {
            display: none !important;
        }

        .sidebar-item {
            transition: all 0.2s ease;
        }

        .sidebar-item:hover {
            background: #f3f4f6;
        }

        .sidebar-item.active {
            background: #8b5cf6;
            color: white;
            font-weight: 500;
        }
    </style>
</head>

<body class="bg-gray-50" x-data="userDashboard()" x-init="init()">
    <div class="flex h-screen">
        <!-- Sidebar -->
        <div class="w-64 bg-white shadow-lg border-r border-gray-200">
            <!-- Header -->
            <div class="p-4 border-b border-gray-200">
                <h1 class="text-lg font-semibold text-gray-800">BMKG Pontianak</h1>
            </div>

            <!-- Navigation -->
            <nav class="p-4">
                <ul class="space-y-2">
                    <li>
                        <button @click="setActiveTab('dashboard')" :class="activeTab === 'dashboard' ? 'active' : ''"
                            class="sidebar-item w-full text-left px-3 py-2 rounded-lg text-sm flex items-center">
                            <i class="fas fa-tachometer-alt mr-3 w-4"></i>
                            Dasbor
                        </button>
                    </li>
                    <li>
                        <button @click="setActiveTab('application')"
                            :class="activeTab === 'application' ? 'active' : ''"
                            class="sidebar-item w-full text-left px-3 py-2 rounded-lg text-sm flex items-center">
                            <i class="fas fa-file-plus mr-3 w-4"></i>
                            Pengajuan Surat
                        </button>
                    </li>
                    <li>
                        <button @click="setActiveTab('history')" :class="activeTab === 'history' ? 'active' : ''"
                            class="sidebar-item w-full text-left px-3 py-2 rounded-lg text-sm flex items-center">
                            <i class="fas fa-history mr-3 w-4"></i>
                            Riwayat Pengajuan
                        </button>
                    </li>
                    <li>
                        <button @click="setActiveTab('guidelines')" :class="activeTab === 'guidelines' ? 'active' : ''"
                            class="sidebar-item w-full text-left px-3 py-2 rounded-lg text-sm flex items-center">
                            <i class="fas fa-book mr-3 w-4"></i>
                            Panduan Surat/Data
                        </button>
                    </li>
                    <li>
                        <button @click="setActiveTab('profile')" :class="activeTab === 'profile' ? 'active' : ''"
                            class="sidebar-item w-full text-left px-3 py-2 rounded-lg text-sm flex items-center">
                            <i class="fas fa-user mr-3 w-4"></i>
                            Profil
                        </button>
                    </li>
                </ul>
            </nav>

            <!-- Logout Button -->
            <div class="absolute bottom-4 left-4 right-4">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit"
                        class="w-full bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors flex items-center justify-center">
                        <i class="fas fa-sign-out-alt mr-2"></i>
                        Logout
                    </button>
                </form>
            </div>
        </div>

        <!-- Main Content -->
        <div class="flex-1 flex flex-col">
            <!-- Top Header -->
            <header class="bg-white shadow-sm border-b border-gray-200 px-6 py-4">
                <div class="flex justify-between items-center">
                    <div>
                        <h2 class="text-xl font-semibold text-gray-800" x-text="getPageTitle()"></h2>
                        <p class="text-sm text-gray-600 mt-1" x-text="getPageDescription()"></p>
                    </div>
                    <div class="flex items-center space-x-4">
                        <div class="text-sm text-gray-600">
                            <span>{{ Auth::user()->name }}</span>
                        </div>
                        <div
                            class="w-8 h-8 bg-purple-500 rounded-full flex items-center justify-center text-white font-medium">
                            {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                        </div>
                    </div>
                </div>
            </header>

            <!-- Main Content Area -->
            <main class="flex-1 overflow-y-auto p-6">
                <!-- Dashboard Tab -->
                <div x-show="activeTab === 'dashboard'" x-transition>
                    <!-- Stats Cards -->
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                        <div class="bg-white rounded-lg p-6 shadow-sm border border-gray-200">
                            <div class="flex items-center">
                                <div class="w-12 h-12 bg-yellow-100 rounded-lg flex items-center justify-center">
                                    <i class="fas fa-clock text-yellow-600 text-xl"></i>
                                </div>
                                <div class="ml-4">
                                    <p class="text-sm font-medium text-gray-600">Menunggu</p>
                                    <p class="text-2xl font-bold text-gray-900">{{ $stats['pending'] }}</p>
                                </div>
                            </div>
                        </div>

                        <div class="bg-white rounded-lg p-6 shadow-sm border border-gray-200">
                            <div class="flex items-center">
                                <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                                    <i class="fas fa-spinner text-blue-600 text-xl"></i>
                                </div>
                                <div class="ml-4">
                                    <p class="text-sm font-medium text-gray-600">Diproses</p>
                                    <p class="text-2xl font-bold text-gray-900">{{ $stats['in_process'] }}</p>
                                </div>
                            </div>
                        </div>

                        <div class="bg-white rounded-lg p-6 shadow-sm border border-gray-200">
                            <div class="flex items-center">
                                <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                                    <i class="fas fa-check text-green-600 text-xl"></i>
                                </div>
                                <div class="ml-4">
                                    <p class="text-sm font-medium text-gray-600">Selesai</p>
                                    <p class="text-2xl font-bold text-gray-900">{{ $stats['completed'] }}</p>
                                </div>
                            </div>
                        </div>

                        <div class="bg-white rounded-lg p-6 shadow-sm border border-gray-200">
                            <div class="flex items-center">
                                <div class="w-12 h-12 bg-red-100 rounded-lg flex items-center justify-center">
                                    <i class="fas fa-times text-red-600 text-xl"></i>
                                </div>
                                <div class="ml-4">
                                    <p class="text-sm font-medium text-gray-600">Ditolak</p>
                                    <p class="text-2xl font-bold text-gray-900">{{ $stats['rejected'] }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Recent Applications Table -->
                    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                        <div class="px-6 py-4 border-b border-gray-200">
                            <h3 class="text-lg font-medium text-gray-900">Riwayat pengajuan surat/data Anda</h3>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="min-w-full">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">No.
                                            Surat</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                                            Tanggal Pengajuan</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                                            Jenis Data</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                                            Status</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Aksi
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200">
                                    @forelse($applications as $app)
                                        <tr class="hover:bg-gray-50">
                                            <td class="px-6 py-4 text-sm font-medium text-gray-900">
                                                {{ $app->application_number }}</td>
                                            <td class="px-6 py-4 text-sm text-gray-500">
                                                {{ $app->created_at->format('Y-m-d') }}</td>
                                            <td class="px-6 py-4 text-sm text-gray-500">{{ $app->guideline->title }}
                                            </td>
                                            <td class="px-6 py-4">
                                                @switch($app->status)
                                                    @case('pending')
                                                        <span
                                                            class="bg-yellow-100 text-yellow-800 px-2 py-1 text-xs rounded">Menunggu</span>
                                                    @break

                                                    @case('verified')
                                                        <span
                                                            class="bg-blue-100 text-blue-800 px-2 py-1 text-xs rounded">Berhasil</span>
                                                    @break

                                                    @case('payment_pending')
                                                        <span
                                                            class="bg-orange-100 text-orange-800 px-2 py-1 text-xs rounded">Menunggu
                                                            Pembayaran</span>
                                                    @break

                                                    @case('completed')
                                                        <span
                                                            class="bg-green-100 text-green-800 px-2 py-1 text-xs rounded">Selesai</span>
                                                    @break

                                                    @case('rejected')
                                                        <span
                                                            class="bg-red-100 text-red-800 px-2 py-1 text-xs rounded">Ditolak</span>
                                                    @break

                                                    @default
                                                        <span
                                                            class="bg-gray-100 text-gray-800 px-2 py-1 text-xs rounded">{{ ucfirst($app->status) }}</span>
                                                @endswitch
                                            </td>
                                            <td class="px-6 py-4 text-sm">
                                                @if ($app->status === 'payment_pending' && !$app->payment?->payment_proof)
                                                    <button
                                                        onclick="window.userDashboard().uploadPaymentProof({{ $app->id }})"
                                                        class="text-purple-600 hover:text-purple-800 font-medium">
                                                        Upload Bukti
                                                    </button>
                                                @elseif($app->status === 'completed' && $app->generatedDocuments->count() > 0)
                                                    @foreach ($app->generatedDocuments as $doc)
                                                        <a href="{{ route('user.documents.download', $doc->id) }}"
                                                            class="text-green-600 hover:text-green-800 font-medium mr-2">
                                                            Download
                                                        </a>
                                                    @endforeach
                                                @else
                                                    <span class="text-gray-400">-</span>
                                                @endif
                                            </td>
                                        </tr>
                                        @empty
                                            <tr>
                                                <td colspan="5" class="px-6 py-8 text-center text-gray-500">
                                                    <i class="fas fa-inbox text-2xl mb-2"></i>
                                                    <p>Belum ada pengajuan</p>
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- Application Tab -->
                    <div x-show="activeTab === 'application'" x-transition>
                        <!-- Category Selection -->
                        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Kategori Pengajuan</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <button @click="selectedType = 'pnbp'; loadGuidelines()"
                                    :class="selectedType === 'pnbp' ? 'bg-purple-500 text-white border-purple-500' :
                                        'border-gray-200 text-gray-700 hover:border-purple-300'"
                                    class="p-4 border-2 rounded-lg transition-colors">
                                    <div class="text-center">
                                        <h4 class="font-medium">PNBP</h4>
                                        <p class="text-sm mt-1 opacity-75">Untuk keperluan instansi atau umum</p>
                                    </div>
                                </button>

                                <button @click="selectedType = 'non_pnbp'; loadGuidelines()"
                                    :class="selectedType === 'non_pnbp' ? 'bg-purple-500 text-white border-purple-500' :
                                        'border-gray-200 text-gray-700 hover:border-purple-300'"
                                    class="p-4 border-2 rounded-lg transition-colors">
                                    <div class="text-center">
                                        <h4 class="font-medium">Non-PNBP</h4>
                                        <p class="text-sm mt-1 opacity-75">Khusus untuk mahasiswa dan peneliti</p>
                                    </div>
                                </button>
                            </div>
                        </div>

                        <!-- Form Pengajuan -->
                        <div x-show="selectedType" x-transition
                            class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Formulir Pengajuan Surat/Data</h3>
                            <p class="text-sm text-gray-600 mb-6">Tidak boleh diisi asal pengisian hanya centang saja.</p>

                            <!-- Guidelines Table -->
                            <div class="mb-6" x-show="guidelines.length > 0">
                                <h4 class="font-medium text-gray-900 mb-3">Tabel Unduhan Surat Pengantar</h4>
                                <div class="border border-gray-200 rounded-lg overflow-hidden">
                                    <table class="min-w-full">
                                        <thead class="bg-gray-50">
                                            <tr>
                                                <th
                                                    class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                                                    Jenis Surat</th>
                                                <th
                                                    class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                                                    Keterangan</th>
                                                <th
                                                    class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                                                    Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-gray-200">
                                            <template x-for="guideline in guidelines" :key="guideline.id">
                                                <tr>
                                                    <td class="px-4 py-3 text-sm text-gray-900" x-text="guideline.title">
                                                    </td>
                                                    <td class="px-4 py-3 text-sm text-gray-600"
                                                        x-text="guideline.description"></td>
                                                    <td class="px-4 py-3 text-sm">
                                                        <button @click="selectGuideline(guideline)"
                                                            class="text-purple-600 hover:text-purple-800 font-medium">
                                                            Unduh docs
                                                        </button>
                                                    </td>
                                                </tr>
                                            </template>
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <!-- Application Form -->
                            <form x-show="selectedGuideline" @submit.prevent="submitApplication()">
                                <div class="space-y-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Jenis Data yang
                                            Diajukan</label>
                                        <select class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                                            <option>Pilih Jenis Data</option>
                                            <option x-show="selectedGuideline" x-text="selectedGuideline.title"></option>
                                        </select>
                                    </div>

                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-2">Tanggal
                                                Mulai</label>
                                            <input type="date"
                                                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-2">Tanggal
                                                Selesai</label>
                                            <input type="date"
                                                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                                        </div>
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Keperluan Penggunaan
                                            Data</label>
                                        <textarea class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm" rows="4"></textarea>
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Upload Surat
                                            Pengantar</label>
                                        <input type="file"
                                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                                    </div>

                                    <div class="pt-4">
                                        <button type="submit"
                                            class="w-full bg-purple-600 hover:bg-purple-700 text-white font-medium py-3 px-4 rounded-lg transition-colors">
                                            Ajukan Surat
                                        </button>
                                    </div>
                                </div>
                            </form>

                            <!-- Loading State -->
                            <div x-show="selectedType && guidelines.length === 0" class="text-center py-8">
                                <i class="fas fa-spinner fa-spin text-2xl text-gray-400 mb-2"></i>
                                <p class="text-gray-500">Memuat data...</p>
                            </div>
                        </div>
                    </div>

                    <!-- Guidelines Tab -->
                    <div x-show="activeTab === 'guidelines'" x-transition>
                        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Panduan Pengajuan Surat/Data</h3>
                            <p class="text-sm text-gray-600 mb-6">Klik pada jenis data di bawah ini untuk melihat detail,
                                contoh, dan syarat pengajuannya.</p>

                            <!-- Accordion List -->
                            <div class="space-y-3" x-data="{ openAccordion: null }">
                                <div class="border border-gray-200 rounded-lg">
                                    <button @click="openAccordion = openAccordion === 1 ? null : 1"
                                        class="w-full px-4 py-3 text-left flex items-center justify-between hover:bg-gray-50">
                                        <span class="font-medium">Informasi Cuaca untuk Pelayaran</span>
                                        <i class="fas fa-chevron-down transition-transform"
                                            :class="openAccordion === 1 ? 'rotate-180' : ''"></i>
                                    </button>
                                    <div x-show="openAccordion === 1" x-transition class="px-4 pb-3">
                                        <p class="text-sm text-gray-600">Detail panduan untuk pengajuan informasi cuaca
                                            pelayaran...</p>
                                    </div>
                                </div>

                                <div class="border border-gray-200 rounded-lg">
                                    <button @click="openAccordion = openAccordion === 2 ? null : 2"
                                        class="w-full px-4 py-3 text-left flex items-center justify-between hover:bg-gray-50">
                                        <span class="font-medium">Informasi Cuaca untuk Pengeboran Lepas Pantai</span>
                                        <i class="fas fa-chevron-down transition-transform"
                                            :class="openAccordion === 2 ? 'rotate-180' : ''"></i>
                                    </button>
                                    <div x-show="openAccordion === 2" x-transition class="px-4 pb-3">
                                        <p class="text-sm text-gray-600">Detail panduan untuk pengajuan informasi cuaca
                                            pengeboran...</p>
                                    </div>
                                </div>

                                <!-- Add more accordion items as needed -->
                            </div>
                        </div>
                    </div>

                    <!-- Other tabs -->
                    <div x-show="['history', 'profile'].includes(activeTab)" x-transition>
                        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-8 text-center">
                            <i class="fas fa-cog fa-spin text-4xl text-gray-400 mb-4"></i>
                            <h3 class="text-xl font-semibold text-gray-900 mb-2"
                                x-text="'Fitur ' + getTabTitle(activeTab) + ' sedang dalam pengembangan'"></h3>
                            <p class="text-gray-600">Fitur ini akan segera tersedia dalam pembaruan selanjutnya</p>
                        </div>
                    </div>
                </main>
            </div>
        </div>

        <script>
            function userDashboard() {
                return {
                    activeTab: 'dashboard',
                    selectedType: null,
                    selectedGuideline: null,
                    guidelines: [],

                    init() {
                        // Initialize
                    },

                    setActiveTab(tab) {
                        this.activeTab = tab;
                    },

                    getPageTitle() {
                        const titles = {
                            'dashboard': 'Selamat datang, User!',
                            'application': 'Formulir Pengajuan Surat/Data',
                            'history': 'Panduan Surat/Data',
                            'guidelines': 'Panduan Pengajuan Surat/Data',
                            'profile': 'Profil Pengguna'
                        };
                        return titles[this.activeTab] || 'Dashboard';
                    },

                    getPageDescription() {
                        const descriptions = {
                            'dashboard': 'Berikut adalah riwayat pengajuan surat/data Anda.',
                            'application': 'Tidak boleh diisi asal pengisian hanya centang saja.',
                            'history': 'Riwayat pengajuan yang pernah Anda buat',
                            'guidelines': 'Klik pada jenis data di bawah ini untuk melihat detail, contoh, dan syarat pengajuannya.',
                            'profile': 'Kelola informasi profil Anda'
                        };
                        return descriptions[this.activeTab] || '';
                    },

                    getTabTitle(tab) {
                        const titles = {
                            'history': 'Riwayat Pengajuan',
                            'profile': 'Profil Pengguna'
                        };
                        return titles[tab] || tab;
                    },

                    async loadGuidelines() {
                        if (!this.selectedType) return;

                        try {
                            const response = await fetch(`/user/guidelines?type=${this.selectedType}`, {
                                headers: {
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                                        'content')
                                }
                            });
                            this.guidelines = await response.json();
                        } catch (error) {
                            console.error('Error loading guidelines:', error);
                        }
                    },

                    selectGuideline(guideline) {
                        this.selectedGuideline = guideline;
                    },

                    async submitApplication() {
                        // Implementation for form submission
                        alert('Fungsi pengajuan akan diimplementasikan');
                    },

                    formatNumber(number) {
                        return new Intl.NumberFormat('id-ID').format(number);
                    }
                }
            }

            // Global function
            window.userDashboard = () => {
                return Alpine.$data(document.querySelector('[x-data="userDashboard()"]'));
            };
        </script>
    </body>

    </html>
