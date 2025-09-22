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
        [x-cloak] { display: none !important; }
        .sidebar-item { transition: all 0.2s ease; }
        .sidebar-item:hover { background: #f3f4f6; }
        .sidebar-item.active { background: #8b5cf6; color: white; font-weight: 500; }
    </style>
</head>
<body class="bg-gray-50" x-data="adminDashboard()" x-init="init()">
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
                        <button @click="setActiveTab('dashboard')" 
                                :class="activeTab === 'dashboard' ? 'active' : ''"
                                class="sidebar-item w-full text-left px-3 py-2 rounded-lg text-sm flex items-center">
                            <i class="fas fa-tachometer-alt mr-3 w-4"></i>
                            Dasbor
                        </button>
                    </li>
                    <li>
                        <button @click="setActiveTab('requests')" 
                                :class="activeTab === 'requests' ? 'active' : ''"
                                class="sidebar-item w-full text-left px-3 py-2 rounded-lg text-sm flex items-center">
                            <i class="fas fa-file-alt mr-3 w-4"></i>
                            Manajemen Permintaan
                            <span x-show="stats.pending_requests > 0" 
                                  class="ml-auto bg-red-500 text-white text-xs px-2 py-0.5 rounded-full" 
                                  x-text="stats.pending_requests"></span>
                        </button>
                    </li>
                    <li>
                        <button @click="setActiveTab('payments')" 
                                :class="activeTab === 'payments' ? 'active' : ''"
                                class="sidebar-item w-full text-left px-3 py-2 rounded-lg text-sm flex items-center">
                            <i class="fas fa-credit-card mr-3 w-4"></i>
                            Manajemen Pembayaran
                            <span x-show="stats.pending_payments > 0" 
                                  class="ml-auto bg-orange-500 text-white text-xs px-2 py-0.5 rounded-full" 
                                  x-text="stats.pending_payments"></span>
                        </button>
                    </li>
                    <li>
                        <button @click="setActiveTab('documents')" 
                                :class="activeTab === 'documents' ? 'active' : ''"
                                class="sidebar-item w-full text-left px-3 py-2 rounded-lg text-sm flex items-center">
                            <i class="fas fa-upload mr-3 w-4"></i>
                            Upload Dokumen
                        </button>
                    </li>
                    <li>
                        <button @click="setActiveTab('guidelines')" 
                                :class="activeTab === 'guidelines' ? 'active' : ''"
                                class="sidebar-item w-full text-left px-3 py-2 rounded-lg text-sm flex items-center">
                            <i class="fas fa-book mr-3 w-4"></i>
                            Manajemen Panduan
                        </button>
                    </li>
                    <li>
                        <button @click="setActiveTab('archives')" 
                                :class="activeTab === 'archives' ? 'active' : ''"
                                class="sidebar-item w-full text-left px-3 py-2 rounded-lg text-sm flex items-center">
                            <i class="fas fa-archive mr-3 w-4"></i>
                            Manajemen Arsip
                        </button>
                    </li>
                    <li>
                        <button @click="setActiveTab('users')" 
                                :class="activeTab === 'users' ? 'active' : ''"
                                class="sidebar-item w-full text-left px-3 py-2 rounded-lg text-sm flex items-center">
                            <i class="fas fa-users mr-3 w-4"></i>
                            Manajemen Pengguna
                        </button>
                    </li>
                </ul>
            </nav>

            <!-- Logout Button -->
            <div class="absolute bottom-4 left-4 right-4">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="w-full bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors flex items-center justify-center">
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
                        <div class="w-8 h-8 bg-purple-500 rounded-full flex items-center justify-center text-white font-medium">
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
                                    <p class="text-sm font-medium text-gray-600">Pending Requests</p>
                                    <p class="text-2xl font-bold text-gray-900">{{ $stats['pending_requests'] }}</p>
                                </div>
                            </div>
                        </div>

                        <div class="bg-white rounded-lg p-6 shadow-sm border border-gray-200">
                            <div class="flex items-center">
                                <div class="w-12 h-12 bg-red-100 rounded-lg flex items-center justify-center">
                                    <i class="fas fa-credit-card text-red-600 text-xl"></i>
                                </div>
                                <div class="ml-4">
                                    <p class="text-sm font-medium text-gray-600">Pending Payments</p>
                                    <p class="text-2xl font-bold text-gray-900">{{ $stats['pending_payments'] }}</p>
                                </div>
                            </div>
                        </div>

                        <div class="bg-white rounded-lg p-6 shadow-sm border border-gray-200">
                            <div class="flex items-center">
                                <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                                    <i class="fas fa-spinner text-blue-600 text-xl"></i>
                                </div>
                                <div class="ml-4">
                                    <p class="text-sm font-medium text-gray-600">Processing</p>
                                    <p class="text-2xl font-bold text-gray-900">{{ $stats['processing'] }}</p>
                                </div>
                            </div>
                        </div>

                        <div class="bg-white rounded-lg p-6 shadow-sm border border-gray-200">
                            <div class="flex items-center">
                                <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                                    <i class="fas fa-check text-green-600 text-xl"></i>
                                </div>
                                <div class="ml-4">
                                    <p class="text-sm font-medium text-gray-600">Completed</p>
                                    <p class="text-2xl font-bold text-gray-900">{{ $stats['completed'] }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Recent Applications Table -->
                    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                        <div class="px-6 py-4 border-b border-gray-200">
                            <h3 class="text-lg font-medium text-gray-900">Recent Applications</h3>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="min-w-full">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Application Number</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">User</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Type</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200">
                                    @forelse($recent_applications as $app)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-6 py-4 text-sm font-medium text-gray-900">{{ $app->application_number }}</td>
                                        <td class="px-6 py-4 text-sm text-gray-500">{{ $app->user->name }}</td>
                                        <td class="px-6 py-4">
                                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded {{ $app->type === 'pnbp' ? 'bg-blue-100 text-blue-800' : 'bg-green-100 text-green-800' }}">
                                                {{ strtoupper($app->type) }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4">
                                            @switch($app->status)
                                                @case('pending')
                                                    <span class="bg-yellow-100 text-yellow-800 px-2 py-1 text-xs font-semibold rounded">Menunggu</span>
                                                    @break
                                                @case('verified')
                                                    <span class="bg-blue-100 text-blue-800 px-2 py-1 text-xs font-semibold rounded">Terverifikasi</span>
                                                    @break
                                                @case('completed')
                                                    <span class="bg-green-100 text-green-800 px-2 py-1 text-xs font-semibold rounded">Selesai</span>
                                                    @break
                                                @case('rejected')
                                                    <span class="bg-red-100 text-red-800 px-2 py-1 text-xs font-semibold rounded">Ditolak</span>
                                                    @break
                                                @default
                                                    <span class="bg-gray-100 text-gray-800 px-2 py-1 text-xs font-semibold rounded">{{ ucfirst($app->status) }}</span>
                                            @endswitch
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-500">{{ $app->created_at->format('d/m/Y') }}</td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="5" class="px-6 py-8 text-center text-gray-500">
                                            <i class="fas fa-inbox text-2xl mb-2"></i>
                                            <p>No recent applications</p>
                                        </td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Requests Tab -->
                <div x-show="activeTab === 'requests'" x-transition>
                    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                        <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
                            <h3 class="text-lg font-medium text-gray-900">Daftar Permintaan</h3>
                            <button @click="loadRequests()" class="bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                                <i class="fas fa-refresh mr-2"></i>Refresh
                            </button>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="min-w-full">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">No. Aplikasi</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">User</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Jenis Layanan</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tipe</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tanggal</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200" x-html="requestsTable">
                                    <tr>
                                        <td colspan="7" class="px-6 py-8 text-center text-gray-500">
                                            <i class="fas fa-spinner fa-spin text-xl mb-2"></i>
                                            <p>Loading data...</p>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Payments Tab -->
                <div x-show="activeTab === 'payments'" x-transition>
                    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                        <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
                            <h3 class="text-lg font-medium text-gray-900">Daftar Pembayaran</h3>
                            <button @click="loadPayments()" class="bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                                <i class="fas fa-refresh mr-2"></i>Refresh
                            </button>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="min-w-full">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">No. Aplikasi</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">User</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Jumlah</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Bukti Bayar</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200" x-html="paymentsTable">
                                    <tr>
                                        <td colspan="6" class="px-6 py-8 text-center text-gray-500">
                                            <i class="fas fa-spinner fa-spin text-xl mb-2"></i>
                                            <p>Loading data...</p>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Other tabs content -->
                <div x-show="['documents', 'guidelines', 'archives', 'users'].includes(activeTab)" x-transition>
                    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-8 text-center">
                        <i class="fas fa-cog fa-spin text-4xl text-gray-400 mb-4"></i>
                        <h3 class="text-xl font-semibold text-gray-900 mb-2" x-text="'Fitur ' + getTabTitle(activeTab) + ' sedang dalam pengembangan'"></h3>
                        <p class="text-gray-600">Fitur ini akan segera tersedia dalam pembaruan selanjutnya</p>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <!-- Modal untuk Verifikasi Request -->
    <div x-show="showRequestModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 flex items-center justify-center z-50" x-cloak>
        <div class="bg-white rounded-lg p-6 w-96 shadow-xl">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Verifikasi Permintaan</h3>
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Catatan</label>
                <textarea x-model="requestNotes" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm" rows="3" placeholder="Tambahkan catatan (opsional)"></textarea>
            </div>
            <div class="flex justify-end space-x-3">
                <button @click="showRequestModal = false" class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg transition-colors">
                    Batal
                </button>
                <button @click="verifyRequest('reject')" class="px-4 py-2 text-sm font-medium text-white bg-red-600 hover:bg-red-700 rounded-lg transition-colors">
                    Tolak
                </button>
                <button @click="verifyRequest('approve')" class="px-4 py-2 text-sm font-medium text-white bg-green-600 hover:bg-green-700 rounded-lg transition-colors">
                    Setujui
                </button>
            </div>
        </div>
    </div>

    <!-- Modal untuk Verifikasi Payment -->
    <div x-show="showPaymentModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 flex items-center justify-center z-50" x-cloak>
        <div class="bg-white rounded-lg p-6 w-96 shadow-xl">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Verifikasi Pembayaran</h3>
            <p class="text-sm text-gray-600 mb-6">Apakah Anda yakin ingin memverifikasi pembayaran ini?</p>
            <div class="flex justify-end space-x-3">
                <button @click="showPaymentModal = false" class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg transition-colors">
                    Batal
                </button>
                <button @click="verifyPayment('reject')" class="px-4 py-2 text-sm font-medium text-white bg-red-600 hover:bg-red-700 rounded-lg transition-colors">
                    Tolak
                </button>
                <button @click="verifyPayment('approve')" class="px-4 py-2 text-sm font-medium text-white bg-green-600 hover:bg-green-700 rounded-lg transition-colors">
                    Setujui
                </button>
            </div>
        </div>
    </div>

    <script>
        function adminDashboard() {
            return {
                activeTab: 'dashboard',
                showRequestModal: false,
                showPaymentModal: false,
                selectedRequest: null,
                selectedPayment: null,
                requestNotes: '',
                requestsTable: '',
                paymentsTable: '',
                stats: {
                    pending_requests: {{ $stats['pending_requests'] }},
                    pending_payments: {{ $stats['pending_payments'] }},
                    processing: {{ $stats['processing'] }},
                    completed: {{ $stats['completed'] }}
                },

                init() {
                    this.loadRequests();
                    this.loadPayments();
                },

                setActiveTab(tab) {
                    this.activeTab = tab;
                    if (tab === 'requests') {
                        this.loadRequests();
                    } else if (tab === 'payments') {
                        this.loadPayments();
                    }
                },

                getPageTitle() {
                    const titles = {
                        'dashboard': 'Selamat datang, Admin!',
                        'requests': 'Manajemen Permintaan',
                        'payments': 'Manajemen Pembayaran',
                        'documents': 'Upload Dokumen',
                        'guidelines': 'Manajemen Panduan',
                        'archives': 'Manajemen Arsip',
                        'users': 'Manajemen Pengguna'
                    };
                    return titles[this.activeTab] || 'Dashboard';
                },

                getPageDescription() {
                    const descriptions = {
                        'dashboard': 'Ringkasan aktivitas sistem pengajuan surat',
                        'requests': 'Kelola dan verifikasi permintaan pengguna',
                        'payments': 'Verifikasi pembayaran yang masuk',
                        'documents': 'Upload dokumen hasil permintaan',
                        'guidelines': 'Kelola panduan dan jenis layanan',
                        'archives': 'Arsip dokumen dan laporan',
                        'users': 'Kelola data pengguna sistem'
                    };
                    return descriptions[this.activeTab] || '';
                },

                getTabTitle(tab) {
                    const titles = {
                        'documents': 'Upload Dokumen',
                        'guidelines': 'Manajemen Panduan',
                        'archives': 'Manajemen Arsip',
                        'users': 'Manajemen Pengguna'
                    };
                    return titles[tab] || tab;
                },

                async loadRequests() {
                    try {
                        const response = await fetch('/admin/requests', {
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                            }
                        });
                        
                        if (response.ok) {
                            const data = await response.json();
                            if (data.data && data.data.length > 0) {
                                this.requestsTable = data.data.map(app => `
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-6 py-4 text-sm font-medium text-gray-900">${app.application_number}</td>
                                        <td class="px-6 py-4 text-sm text-gray-500">${app.user.name}</td>
                                        <td class="px-6 py-4 text-sm text-gray-500">${app.guideline.title}</td>
                                        <td class="px-6 py-4">
                                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded ${app.type === 'pnbp' ? 'bg-blue-100 text-blue-800' : 'bg-green-100 text-green-800'}">
                                                ${app.type.toUpperCase()}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4">
                                            <span class="px-2 py-1 text-xs font-semibold rounded ${this.getStatusColor(app.status)}">
                                                ${this.formatStatus(app.status)}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-500">${new Date(app.created_at).toLocaleDateString('id-ID')}</td>
                                        <td class="px-6 py-4 text-sm">
                                            ${app.status === 'pending' ? `
                                                <button onclick="window.adminDashboard().openRequestModal(${app.id})" class="text-purple-600 hover:text-purple-800 font-medium">
                                                    Verifikasi
                                                </button>
                                            ` : '-'}
                                        </td>
                                    </tr>
                                `).join('');
                            } else {
                                this.requestsTable = '<tr><td colspan="7" class="px-6 py-8 text-center text-gray-500">Tidak ada permintaan</td></tr>';
                            }
                        }
                    } catch (error) {
                        console.error('Error loading requests:', error);
                    }
                },

                async loadPayments() {
                    try {
                        const response = await fetch('/admin/payments', {
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                            }
                        });
                        
                        if (response.ok) {
                            const data = await response.json();
                            if (data.data && data.data.length > 0) {
                                this.paymentsTable = data.data.map(payment => `
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-6 py-4 text-sm font-medium text-gray-900">${payment.application.application_number}</td>
                                        <td class="px-6 py-4 text-sm text-gray-500">${payment.application.user.name}</td>
                                        <td class="px-6 py-4 text-sm text-gray-500">Rp ${this.formatNumber(payment.amount)}</td>
                                        <td class="px-6 py-4 text-sm">
                                            ${payment.payment_proof ? `<a href="/storage/${payment.payment_proof}" target="_blank" class="text-purple-600 hover:text-purple-800">Lihat</a>` : 'Belum ada'}
                                        </td>
                                        <td class="px-6 py-4">
                                            <span class="px-2 py-1 text-xs font-semibold rounded ${this.getPaymentStatusColor(payment.status)}">
                                                ${this.formatStatus(payment.status)}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 text-sm">
                                            ${payment.status === 'pending' && payment.payment_proof ? `
                                                <button onclick="window.adminDashboard().openPaymentModal(${payment.id})" class="text-purple-600 hover:text-purple-800 font-medium">
                                                    Verifikasi
                                                </button>
                                            ` : '-'}
                                        </td>
                                    </tr>
                                `).join('');
                            } else {
                                this.paymentsTable = '<tr><td colspan="6" class="px-6 py-8 text-center text-gray-500">Tidak ada pembayaran</td></tr>';
                            }
                        }
                    } catch (error) {
                        console.error('Error loading payments:', error);
                    }
                },

                openRequestModal(id) {
                    this.selectedRequest = id;
                    this.requestNotes = '';
                    this.showRequestModal = true;
                },

                openPaymentModal(id) {
                    this.selectedPayment = id;
                    this.showPaymentModal = true;
                },

                async verifyRequest(action) {
                    try {
                        const response = await fetch(`/admin/requests/${this.selectedRequest}/verify`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                            },
                            body: JSON.stringify({
                                action: action,
                                notes: this.requestNotes
                            })
                        });

                        if (response.ok) {
                            this.showRequestModal = false;
                            await this.loadRequests();
                            this.stats.pending_requests = Math.max(0, this.stats.pending_requests - 1);
                            alert('Request berhasil diverifikasi!');
                        }
                    } catch (error) {
                        console.error('Error verifying request:', error);
                        alert('Terjadi kesalahan saat memverifikasi request');
                    }
                },

                async verifyPayment(action) {
                    try {
                        const response = await fetch(`/admin/payments/${this.selectedPayment}/verify`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                            },
                            body: JSON.stringify({
                                action: action
                            })
                        });

                        if (response.ok) {
                            this.showPaymentModal = false;
                            await this.loadPayments();
                            this.stats.pending_payments = Math.max(0, this.stats.pending_payments - 1);
                            alert('Payment berhasil diverifikasi!');
                        }
                    } catch (error) {
                        console.error('Error verifying payment:', error);
                        alert('Terjadi kesalahan saat memverifikasi payment');
                    }
                },

                getStatusColor(status) {
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
                },

                getPaymentStatusColor(status) {
                    const colors = {
                        'pending': 'bg-yellow-100 text-yellow-800',
                        'verified': 'bg-green-100 text-green-800',
                        'rejected': 'bg-red-100 text-red-800'
                    };
                    return colors[status] || 'bg-gray-100 text-gray-800';
                },

                formatStatus(status) {
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
                },

                formatNumber(number) {
                    return new Intl.NumberFormat('id-ID').format(number);
                }
            }
        }

        // Global function for onclick handlers
        window.adminDashboard = () => {
            return Alpine.$data(document.querySelector('[x-data="adminDashboard()"]'));
        };
    </script>
</body>
</html>
