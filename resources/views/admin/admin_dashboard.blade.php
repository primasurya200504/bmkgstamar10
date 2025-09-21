<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dasbor Admin BMKG Pontianak</title>
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
                    <path fill-rule="evenodd" d="M9 12a1 1 0 100-2 1 1 0 000 2zM15 12a1 1 0 100-2 1 1 0 000 2z"
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
            <a href="#guidelines"
                class="flex items-center px-4 py-2 rounded-lg text-gray-300 hover:bg-gray-800 transition-colors">
                <svg class="w-5 h-5 mr-3" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd"
                        d="M4 4a2 2 0 00-2 2v2a2 2 0 002 2h2v2H4a2 2 0 00-2 2v2a2 2 0 002 2h12a2 2 0 002-2v-2a2 2 0 00-2-2h-2v-2h2a2 2 0 002-2V6a2 2 0 00-2-2H4z"
                        clip-rule="evenodd"></path>
                </svg>
                <span>Manajemen Panduan</span>
            </a>
            <a href="#users"
                class="flex items-center px-4 py-2 rounded-lg text-gray-300 hover:bg-gray-800 transition-colors">
                <svg class="w-5 h-5 mr-3" fill="currentColor" viewBox="0 0 20 20">
                    <path
                        d="M10 2a1 1 0 00-1 1v1a1 1 0 00-2 0V3a1 1 0 00-1-1H4a2 2 0 00-2 2v2a2 2 0 002 2h1a1 1 0 001-1v-1a1 1 0 00-2 0v1a1 1 0 001 1h12a1 1 0 001-1V5a1 1 0 00-2 0v1a1 1 0 001 1h1a2 2 0 002-2V4a2 2 0 00-2-2h-2a1 1 0 00-1 1v1a1 1 0 00-2 0V3a1 1 0 00-1-1H9a1 1 0 00-1 1v1a1 1 0 00-2 0V3a1 1 0 00-1-1H4z">
                    </path>
                </svg>
                <span>Manajemen Pengguna</span>
            </a>
        </nav>
        <div class="mt-auto w-full">
            <form id="admin-logout-form" action="{{ route('logout') }}" method="POST" class="w-full">
                @csrf
                <button type="submit"
                    class="w-full bg-red-500 hover:bg-red-600 text-white font-bold py-3 px-6 rounded-lg transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2">
                    Logout
                </button>
            </form>
        </div>
    </div>

    <main class="flex-1 p-8 main-content overflow-y-auto">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-3xl font-bold text-gray-800">Dasbor Admin</h1>
            <div class="flex items-center space-x-4">
                <div class="text-gray-600">Selamat datang, Admin!</div>
                <div class="bg-blue-600 text-white px-4 py-2 rounded-full font-semibold">Admin</div>
            </div>
        </div>

        @if (session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-6"
                role="alert">
                <span class="block sm:inline">{{ session('success') }}</span>
            </div>
        @endif

        <div id="dashboard" class="space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="bg-white rounded-xl shadow-lg p-6">
                    <h3 class="text-lg font-semibold text-gray-700 mb-2">Total Permintaan</h3>
                    <p class="text-4xl font-bold text-gray-900">125</p>
                    <p class="text-sm text-gray-500 mt-1">Permintaan data total.</p>
                </div>
                <div class="bg-white rounded-xl shadow-lg p-6">
                    <h3 class="text-lg font-semibold text-gray-700 mb-2">Permintaan Menunggu</h3>
                    <p class="text-4xl font-bold text-gray-900">15</p>
                    <p class="text-sm text-gray-500 mt-1">Permintaan yang perlu diverifikasi.</p>
                </div>
                <div class="bg-white rounded-xl shadow-lg p-6">
                    <h3 class="text-lg font-semibold text-gray-700 mb-2">Pembayaran Tertunda</h3>
                    <p class="text-4xl font-bold text-gray-900">5</p>
                    <p class="text-sm text-gray-500 mt-1">Pembayaran yang perlu dikonfirmasi.</p>
                </div>
            </div>
        </div>

        <div id="requests" class="space-y-6 hidden">
            <h2 class="text-2xl font-bold mb-4">Manajemen Permintaan</h2>
            <div class="bg-white p-6 rounded-xl shadow-lg">
                <h2 class="text-xl font-semibold mb-4">Daftar Pengajuan</h2>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    NOMOR PENGAJUAN</th>
                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    JENIS SURAT</th>
                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    PEMOHON</th>
                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    TANGGAL PENGAJUAN</th>
                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    STATUS SURAT</th>
                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    PEMBAYARAN</th>
                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    AKSI</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse ($submissions as $submission)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap">{{ $submission->submission_number }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">{{ $submission->data_type }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">{{ $submission->user->name }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        {{ $submission->created_at->format('Y-m-d') }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <form action="{{ route('admin.submissions.updateStatus', $submission) }}"
                                            method="POST" class="inline" id="status-form-{{ $submission->id }}">
                                            @csrf
                                            @method('PATCH')
                                            <select name="status"
                                                class="px-2 py-1 rounded-full text-xs font-semibold
                                                @if ($submission->status === 'Diterima') bg-green-100 text-green-800
                                                @elseif ($submission->status === 'Diproses') bg-yellow-100 text-yellow-800
                                                @else bg-red-100 text-red-800 @endif"
                                                onchange="this.form.submit()">
                                                <option value="Diproses"
                                                    @if ($submission->status === 'Diproses') selected @endif>
                                                    Diproses</option>
                                                <option value="Diterima"
                                                    @if ($submission->status === 'Diterima') selected @endif>
                                                    Diterima</option>
                                                <option value="Ditolak"
                                                    @if ($submission->status === 'Ditolak') selected @endif>
                                                    Ditolak</option>
                                            </select>
                                        </form>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <form action="{{ route('admin.submissions.updateStatus', $submission) }}"
                                            method="POST" class="inline" id="payment-form-{{ $submission->id }}">
                                            @csrf
                                            @method('PATCH')
                                            <select name="payment_status"
                                                class="px-2 py-1 rounded-full text-xs font-semibold
                                                @if ($submission->payment_status === 'Sudah Dibayar') bg-blue-100 text-blue-800
                                                @else bg-red-100 text-red-800 @endif"
                                                onchange="this.form.submit()">
                                                <option value="Belum Dibayar"
                                                    @if ($submission->payment_status === 'Belum Dibayar') selected @endif>Belum Dibayar
                                                </option>
                                                <option value="Sudah Dibayar"
                                                    @if ($submission->payment_status === 'Sudah Dibayar') selected @endif>Sudah Dibayar
                                                </option>
                                            </select>
                                        </form>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <button onclick="showDetailModal({{ json_encode($submission) }})"
                                            class="text-indigo-600 hover:text-indigo-900">Detail</button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-6 py-4 whitespace-nowrap text-center text-gray-500">
                                        Tidak ada pengajuan surat.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div id="billing" class="space-y-6 hidden">
            <h2 class="text-2xl font-bold mb-4">Manajemen Pembayaran</h2>
            <div class="bg-white rounded-xl shadow-lg p-6">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    No. Surat</th>
                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Tanggal Pengajuan</th>
                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Jenis Data</th>
                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Pemohon</th>
                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Status</th>
                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            {{-- Tampilan pembayaran --}}
                            @forelse ($submissions as $submission)
                                @if ($submission->payment_status === 'Menunggu Pembayaran')
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                            {{ $submission->submission_number }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $submission->created_at->format('Y-m-d') }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $submission->data_type }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $submission->user->name }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span
                                                class="status-badge bg-yellow-100 text-yellow-800">{{ $submission->payment_status }}</span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                            <a href="#" class="text-green-600 hover:text-green-900"
                                                onclick="showPaymentModal({
                                                    nomorSurat: '{{ $submission->submission_number }}',
                                                    tanggalPengajuan: '{{ $submission->created_at->format('Y-m-d') }}',
                                                    jenisData: '{{ $submission->data_type }}',
                                                    pemohon: '{{ $submission->user->name }}',
                                                    status: '{{ $submission->payment_status }}',
                                                    buktiPembayaran: '{{ $submission->payment_proof_path }}',
                                                    ebilling: '{{ $submission->ebilling_path }}'
                                                })">Konfirmasi
                                                Pembayaran</a>
                                        </td>
                                    </tr>
                                @endif
                            @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-4 whitespace-nowrap text-center text-gray-500">
                                        Tidak ada pengajuan pembayaran.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

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
                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Judul Panduan</th>
                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse ($guidelines as $guideline)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                        {{ $guideline->title }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-2">
                                        <a href="#"
                                            onclick="showGuidelineModal('edit', {
                                                id: '{{ $guideline->id }}',
                                                title: '{{ $guideline->title }}',
                                                content: '{{ $guideline->content }}',
                                                exampleData: '{{ json_encode($guideline->example_data) }}',
                                                requirements: '{{ json_encode($guideline->requirements) }}'
                                            })"
                                            class="text-indigo-600 hover:text-indigo-900">Edit</a>
                                        <form action="{{ route('admin.guidelines.destroy', $guideline) }}"
                                            method="POST" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-900"
                                                onclick="return confirm('Apakah Anda yakin ingin menghapus panduan ini?')">Hapus</button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="2" class="px-6 py-4 whitespace-nowrap text-center text-gray-500">
                                        Tidak ada panduan.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div id="users" class="space-y-6 hidden">
            <h2 class="text-2xl font-bold mb-4">Manajemen Pengguna</h2>
            <div class="bg-white rounded-xl shadow-lg p-6">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    ID Pengguna</th>
                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Nama Lengkap</th>
                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Tipe Pengguna</th>
                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Tanggal Bergabung</th>
                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse ($users as $user)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                        {{ $user->id }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $user->name }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $user->role }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $user->created_at->format('Y-m-d') }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <a href="#" class="text-red-600 hover:text-red-900">Hapus</a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-4 whitespace-nowrap text-center text-gray-500">
                                        Tidak ada pengguna terdaftar.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </main>

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
                    <h4 class="text-lg font-semibold text-gray-700 mb-2">Dokumen Terlampir</h4><a href="#"
                        id="modalFileLink" target="_blank"
                        class="text-indigo-600 hover:underline flex items-center"><svg class="w-5 h-5 mr-2"
                            fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M14.752 11.123A4.5 4.5 0 0115 15a4.5 4.5 0 01-4.5 4.5M15 15h1m-1 0a2.5 2.5 0 01-5 0m-4-10l-4 4m0 0l4 4m-4-4h18a2 2 0 012 2v10a2 2 0 01-2 2H4a2 2 0 01-2-2V6a2 2 0 012-2z">
                            </path>
                        </svg><span id="modalFileNama">nama_file.pdf</span></a>
                </div>
            </div>
            <div class="mt-8 flex justify-end space-x-4"><button
                    class="bg-red-500 hover:bg-red-600 text-white font-bold py-2 px-4 rounded-lg">Tolak</button><button
                    class="bg-green-500 hover:bg-green-600 text-white font-bold py-2 px-4 rounded-lg">Verifikasi</button>
            </div>
        </div>
    </div>

    <div id="paymentModal" class="modal-overlay hidden">
        <div class="modal-content">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-2xl font-bold text-gray-900">Verifikasi Pembayaran</h3><button
                    onclick="hideModal('paymentModal')" class="text-gray-500 hover:text-gray-700"><svg
                        class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M6 18L18 6M6 6l12 12"></path>
                    </svg></button>
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
                            <p class="font-medium">Jenis Data:</p>
                            <p id="paymentJenisData" class="text-gray-900"></p>
                        </div>
                        <div>
                            <p class="font-medium">Tanggal Pengajuan:</p>
                            <p id="paymentTanggalPengajuan" class="text-gray-900"></p>
                        </div>
                        <div>
                            <p class="font-medium">Status:</p>
                            <p id="paymentStatus" class="text-yellow-800"></p>
                        </div>
                        <div>
                            <p class="font-medium">E-Billing:</p><a id="paymentEbillingLink" href="#"
                                target="_blank" class="text-indigo-600 hover:underline"></a>
                        </div>
                    </div>
                </div>
                <div>
                    <h4 class="text-lg font-semibold text-gray-700 mb-2">Bukti Pembayaran</h4>
                    <div class="mt-4 border border-gray-300 rounded-lg overflow-hidden"><img id="paymentProofImage"
                            src="" alt="Bukti Pembayaran" class="w-full h-auto object-cover"></div>
                </div>
            </div>
            <div class="mt-8 flex justify-end space-x-4"><button
                    class="bg-red-500 hover:bg-red-600 text-white font-bold py-2 px-4 rounded-lg">Tolak</button><button
                    class="bg-green-500 hover:bg-green-600 text-white font-bold py-2 px-4 rounded-lg">Verifikasi
                    Pembayaran</button></div>
        </div>
    </div>

    <div id="rejectionModal" class="modal-overlay hidden">
        <div class="modal-content">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-2xl font-bold text-gray-900">Tolak Permintaan</h3><button
                    onclick="hideModal('rejectionModal')" class="text-gray-500 hover:text-gray-700"><svg
                        class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M6 18L18 6M6 6l12 12"></path>
                    </svg></button>
            </div>
            <div class="space-y-4">
                <div><label for="rejectionNote" class="block text-sm font-medium text-gray-700">Catatan
                        Penolakan:</label>
                    <textarea id="rejectionNote" rows="4"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"></textarea>
                </div>
                <div class="flex items-center space-x-4">
                    <p class="text-sm font-medium text-gray-700">Notifikasi ke pengguna:</p><a id="whatsappLink"
                        href="#" target="_blank"
                        class="bg-green-500 hover:bg-green-600 text-white font-bold py-2 px-4 rounded-lg flex items-center"><svg
                            class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 24 24">
                            <path
                                d="M12.04 2C6.544 2 2.053 6.495 2.053 11.99c0 1.777.478 3.504 1.39 5.044L2.05 22l5.244-1.378a9.982 9.982 0 004.746 1.258c5.495 0 9.985-4.49 9.985-9.99s-4.49-9.99-9.985-9.99zm5.305 13.914l-1.076-1.076a.71.71 0 00-.51-.21c-.139 0-.27.05-.37.15l-.658.657c-.12.12-.27.15-.41.05a8.774 8.774 0 01-3.69-2.12c-.22-.22-.32-.42-.32-.61 0-.17.06-.35.15-.46l.66-.-0.66a.48.48 0 00.12-.34l-.45-.9c-.06-.11-.18-.17-.32-.17h-.88c-.14 0-.25.06-.32.17l-.54.91c-.2.34-.32.74-.32 1.16 0 .97.38 1.9.9 2.58l.1.13c1.07 1.4 2.45 2.22 3.99 2.5a6.04 6.04 0 00.9.15c.34-.05.6-.14.78-.34a.73.73 0 00.22-.5l-.33-1.1c-.02-.13-.08-.24-.18-.34z">
                            </path>
                        </svg>Kirim Notifikasi</a>
                </div>
            </div>
        </div>
    </div>

    <div id="guidelineModal" class="modal-overlay hidden">
        <div class="modal-content">
            <div class="flex justify-between items-center mb-4">
                <h3 id="guidelineModalTitle" class="text-2xl font-bold text-gray-900">Tambah Panduan Baru</h3><button
                    onclick="hideModal('guidelineModal')" class="text-gray-500 hover:text-gray-700"><svg
                        class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M6 18L18 6M6 6l12 12"></path>
                    </svg></button>
            </div>
            <form id="guidelineForm" method="POST" class="space-y-4">
                @csrf
                @method('POST') {{-- Default to POST, will be changed to PUT for edits --}}
                <input type="hidden" name="_method" id="formMethod">
                <div>
                    <label for="guidelineTitle" class="block text-sm font-medium text-gray-700">Judul Panduan</label>
                    <input type="text" name="title" id="guidelineTitle"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                        required>
                </div>
                <div>
                    <label for="guidelineContent" class="block text-sm font-medium text-gray-700">Isi Panduan</label>
                    <textarea name="content" id="guidelineContent" rows="6"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                        required></textarea>
                </div>
                <div>
                    <label for="guidelineExample" class="block text-sm font-medium text-gray-700">Contoh Data (JSON,
                        opsional)</label>
                    <textarea name="example_data" id="guidelineExample" rows="3"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                        placeholder="Contoh: [{ 'Tanggal': '2023-10-25', 'Suhu (oC)': '31' }]"></textarea>
                </div>
                <div>
                    <label for="guidelineRequirements" class="block text-sm font-medium text-gray-700">Syarat
                        Pengajuan (satu per baris)</label>
                    <textarea name="requirements" id="guidelineRequirements" rows="4"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                        placeholder="Surat pengantar dari universitas.&#10;Proposal penelitian terkait."></textarea>
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
        document.addEventListener('DOMContentLoaded', () => {
                    const navLinks = document.querySelectorAll('div.sidebar nav a');
                    const sections = {
                        'dashboard': document.getElementById('dashboard'),
                        'requests': document.getElementById('requests'),
                        'billing': document.getElementById('billing'),
                        'guidelines': document.getElementById('guidelines'),
                        'users': document.getElementById('users')
                    };
                    const detailModal = document.getElementById('detailModal');
                    const paymentModal = document.getElementById('paymentModal');
                    const rejectionModal = document.getElementById('rejectionModal');
                    const guidelineModal = document.getElementById('guidelineModal');

                    navLinks.forEach(link => {
                        link.addEventListener('click', function(event) {
                            event.preventDefault();
                            const targetId = this.getAttribute('href').substring(1);

                            navLinks.forEach(l => {
                                l.classList.remove('bg-indigo-700');
                                l.classList.add('text-gray-300', 'hover:bg-gray-800');
                            });

                            this.classList.remove('text-gray-300', 'hover:bg-gray-800');
                            this.classList.add('bg-indigo-700');

                            for (const sectionId in sections) {
                                if (sections[sectionId]) {
                                    sections[sectionId].classList.add('hidden');
                                }
                            }

                            if (sections[targetId]) {
                                sections[targetId].classList.remove('hidden');
                            }
                        });
                    });

                    document.addEventListener('DOMContentLoaded', () => {
                        const firstNavLink = document.querySelector('div.sidebar nav a[href="#dashboard"]');
                        if (firstNavLink) {
                            firstNavLink.click();
                        }
                    });

                    function showDetailModal(submission) {
                        document.getElementById('modalNomorSurat').textContent = submission.submission_number;
                        document.getElementById('modalPemohon').textContent = submission.user.name;
                        document.getElementById('modalEmail').textContent = submission.user.email;
                        document.getElementById('modalTipePengguna').textContent = submission.user.role;
                        document.getElementById('modalJenisData').textContent = submission.data_type;
                        document.getElementById('modalTanggalPengajuan').textContent = new Date(submission.created_at)
                            .toLocaleDateString();
                        document.getElementById('modalPeriodeData').textContent = submission.start_date && submission
                            .end_date ? `${submission.start_date} hingga ${submission.end_date}` : 'Tidak ada periode';
                        document.getElementById('modalKeperluan').textContent = submission.purpose;
                        document.getElementById('modalFileNama').textContent = submission.cover_letter_path ? submission
                            .cover_letter_path.split('/').pop() : 'Tidak ada file';
                        document.getElementById('modalFileLink').href = `/storage/${submission.cover_letter_path}`;
                        detailModal.classList.remove('hidden');
                    }

                    function showPaymentModal(data) {
                        document.getElementById('paymentNomorSurat').textContent = data.nomorSurat;
                        document.getElementById('paymentPemohon').textContent = data.pemohon;
                        document.getElementById('paymentJenisData').textContent = data.jenisData;
                        document.getElementById('paymentTanggalPengajuan').textContent = data.tanggalPengajuan;
                        document.getElementById('paymentStatus').textContent = data.status;
                        document.getElementById('paymentProofImage').src = `/storage/${data.buktiPembayaran}`;
                        document.getElementById('paymentEbillingLink').textContent = data.ebilling;
                        document.getElementById('paymentEbillingLink').href = `/storage/${data.ebilling}`;
                        paymentModal.classList.remove('hidden');
                    }

                    function showRejectionModal(nama, nomorHP) {
                        rejectionModal.classList.remove('hidden');
                        const rejectionNote = document.getElementById('rejectionNote');
                        const whatsappLink = document.getElementById('whatsappLink');
                        const message =
                            `Halo ${nama}, kami dari BMKG Pontianak. Permintaan data Anda ditolak karena data yang dikirimkan kurang lengkap. Mohon kirimkan ulang surat pengajuan yang telah diperbaiki.`;
                        const encodedMessage = encodeURIComponent(message);
                        whatsappLink.href = `https://wa.me/${nomorHP}?text=${encodedMessage}`;
                    }

                    function showGuidelineModal(mode, data = {}) {
                        const title = document.getElementById('guidelineModalTitle');
                        const form = document.getElementById('guidelineForm');
                        const guidelineTitleInput = document.getElementById('guidelineTitle');
                        const guidelineContentInput = document.getElementById('guidelineContent');
                        const guidelineExampleInput = document.getElementById('guidelineExample');
                        const guidelineRequirementsInput = document.getElementById('guidelineRequirements');
                        const formMethodInput = document.getElementById('formMethod');

                        form.reset();

                        if (mode === 'add') {
                            title.textContent = 'Tambah Panduan Baru';
                            form.action = "{{ route('admin.guidelines.store') }}";
                            formMethodInput.value = 'POST'; // Set method untuk CREATE
                            document.querySelector('#guidelineForm button[type="submit"]').textContent = 'Simpan';
                        } else if (mode === 'edit') {
                            title.textContent = 'Edit Panduan';
                            guidelineTitleInput.value = data.title;
                            guidelineContentInput.value = data.content;

                            // Pastikan data adalah string, jika tidak, konversi
                            let exampleData = JSON.parse(data.exampleData || 'null');
                            guidelineExampleInput.value = exampleData ? JSON.stringify(exampleData, null, 2) : '';

                            let requirementsData = JSON.parse(data.requirements || 'null');
                            guidelineRequirementsInput.value = requirementsData ? requirementsData.join('\n') : '';

                            form.action = "{{ route('admin.guidelines.update', ':id') }}".replace(':id', data.id);
                            formMethodInput.value = 'PUT'; // Set method untuk UPDATE
                            document.querySelector('#guidelineForm button[type="submit"]').textContent = 'Update';
                        }

                        guidelineModal.classList.remove('hidden');
                    }

                    function hideModal(modalId) {
                        const modal = document.getElementById(modalId);
                        if (modal) {
                            modal.classList.add('hidden');
                        }
                    }
    </script>
</body>

</html>
