@extends('layouts.app')

@section('content')
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="page-header">
                <div class="flex justify-between items-start">
                    <div>
                        <h1 class="page-title">Arsip & Laporan</h1>
                        <p class="page-subtitle">Kelola dan pantau arsip pengajuan yang telah selesai diproses</p>
                    </div>
                    <a href="{{ route('admin.dashboard') }}" class="btn-modern btn-secondary">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                        </svg>
                        Kembali ke Dashboard
                    </a>
                </div>
            </div>

            <!-- Filter Form -->
            <div class="card-modern mb-6">
                <div class="p-6">
                    <form method="GET" action="{{ route('admin.archives') }}"
                        class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4 items-end">
                        <div>
                            <label for="search" class="block text-sm font-medium text-gray-700 mb-2">Cari</label>
                            <input type="text" name="search" id="search" value="{{ request('search') }}"
                                placeholder="Nama user atau nomor surat..."
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        </div>
                        <div>
                            <label for="filter_year" class="block text-sm font-medium text-gray-700 mb-2">Tahun</label>
                            <select name="year" id="filter_year"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                <option value="">Semua Tahun</option>
                                @for ($y = date('Y'); $y >= 2020; $y--)
                                    <option value="{{ $y }}" {{ request('year') == $y ? 'selected' : '' }}>
                                        {{ $y }}</option>
                                @endfor
                            </select>
                        </div>
                        <div>
                            <label for="filter_month" class="block text-sm font-medium text-gray-700 mb-2">Bulan</label>
                            <select name="month" id="filter_month"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                <option value="">Semua Bulan</option>
                                @for ($m = 1; $m <= 12; $m++)
                                    <option value="{{ $m }}" {{ request('month') == $m ? 'selected' : '' }}>
                                        {{ date('F', mktime(0, 0, 0, $m, 1)) }}</option>
                                @endfor
                            </select>
                        </div>
                        <div>
                            <label for="filter_category" class="block text-sm font-medium text-gray-700 mb-2">Kategori
                                Data</label>
                            <select name="category" id="filter_category"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                <option value="">Semua Kategori</option>
                                <option value="pnbp" {{ request('category') == 'pnbp' ? 'selected' : '' }}>PNBP</option>
                                <option value="non_pnbp" {{ request('category') == 'non_pnbp' ? 'selected' : '' }}>Non-PNBP
                                </option>
                            </select>
                        </div>
                        <div class="flex gap-2">
                            <button type="submit" class="btn-modern btn-primary flex-1">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                </svg>
                                Filter
                            </button>
                            <a href="{{ route('admin.archives') }}" class="btn-modern btn-outline">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15">
                                    </path>
                                </svg>
                                Reset
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Export Actions -->
            <div class="card-modern mb-6">
                <div class="p-6">
                    <form id="exportForm" method="POST" action="{{ route('admin.archives.export-selected-pdf') }}">
                        @csrf
                        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                            <div class="flex items-center space-x-4">
                                <label class="flex items-center cursor-pointer">
                                    <input type="checkbox" id="selectAll"
                                        class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 focus:ring-2">
                                    <span class="ml-2 text-sm font-medium text-gray-700">Pilih Semua</span>
                                </label>
                                <button type="submit" id="exportSelectedBtn"
                                    class="btn-modern btn-success disabled:opacity-50 disabled:cursor-not-allowed" disabled>
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                                        </path>
                                    </svg>
                                    Export PDF Terpilih
                                </button>
                            </div>
                            <a href="{{ route('admin.archives.export-pdf') . '?' . request()->getQueryString() }}"
                                class="btn-modern btn-danger">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                                    </path>
                                </svg>
                                Export PDF Semua
                            </a>
                        </div>

                        <!-- Hidden checkboxes container - will be populated by JavaScript -->
                        <div id="selectedArchivesContainer"></div>
                    </form>
                </div>
            </div>

            <!-- Archives Table -->
            <div class="card-modern">
                <div class="p-6">
                    <div class="overflow-x-auto">
                        <table class="modern-table min-w-full">
                            <thead>
                                <tr>
                                    <th class="w-12">
                                        <input type="checkbox" id="selectAllHeader"
                                            class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 focus:ring-2">
                                    </th>
                                    <th>ID</th>
                                    <th>No. Surat</th>
                                    <th>User</th>
                                    <th>Kategori Data</th>
                                    <th>Tanggal Arsip</th>
                                    <th>Catatan</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($archives as $archive)
                                    <tr>
                                        <td>
                                            <input type="checkbox" name="selected_archives[]"
                                                value="{{ $archive->id }}"
                                                class="archive-checkbox w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 focus:ring-2">
                                        </td>
                                        <td class="font-medium text-gray-900">#{{ $archive->id }}</td>
                                        <td class="font-medium text-blue-600">
                                            {{ $archive->submission->submission_number ?? 'SUB-' . str_pad($archive->submission->id, 4, '0', STR_PAD_LEFT) }}
                                        </td>
                                        <td>
                                            <div class="flex items-center">
                                                <div
                                                    class="w-8 h-8 bg-gradient-to-br from-blue-500 to-purple-600 rounded-full flex items-center justify-center text-white font-semibold text-xs mr-3">
                                                    {{ strtoupper(substr($archive->submission->user->name ?? 'N', 0, 1)) }}
                                                </div>
                                                <span
                                                    class="font-medium">{{ $archive->submission->user->name ?? 'N/A' }}</span>
                                            </div>
                                        </td>
                                        <td>
                                            <span
                                                class="badge-modern
                                            @if ($archive->submission->guideline->type == 'pnbp') badge-completed
                                            @else badge-verified @endif">
                                                {{ strtoupper($archive->submission->guideline->type ?? 'N/A') }}
                                            </span>
                                        </td>
                                        <td class="text-gray-600">
                                            {{ $archive->archive_date ? $archive->archive_date->format('d/m/Y H:i') : $archive->created_at->format('d/m/Y H:i') }}
                                        </td>
                                        <td class="max-w-xs">
                                            <p class="text-sm text-gray-600 truncate"
                                                title="{{ $archive->notes ?? 'Pengajuan selesai diproses dan diarsipkan' }}">
                                                {{ $archive->notes ?? 'Pengajuan selesai diproses dan diarsipkan' }}
                                            </p>
                                        </td>
                                        <td>
                                            <button onclick="showArchiveDetail({{ $archive->id }}); return false;"
                                                class="btn-modern btn-primary">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z">
                                                    </path>
                                                </svg>
                                                Detail
                                            </button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="px-6 py-12 text-center">
                                            <div class="flex flex-col items-center">
                                                <svg class="w-12 h-12 text-gray-400 mb-4" fill="none"
                                                    stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10">
                                                    </path>
                                                </svg>
                                                <h3 class="text-lg font-medium text-gray-900 mb-2">Tidak ada arsip</h3>
                                                <p class="text-gray-500">Belum ada pengajuan yang diarsipkan.</p>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-6">
                        {{ $archives->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Detail Modal -->
    <div id="archiveDetailModal"
        class="modal-overlay hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white rounded-2xl p-8 w-full max-w-4xl mx-4 max-h-[90vh] overflow-y-auto">
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-2xl font-bold text-gray-900">Detail Arsip Pengajuan</h3>
                <button onclick="hideArchiveModal('archiveDetailModal')" class="text-gray-500 hover:text-gray-700 p-2">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12">
                        </path>
                    </svg>
                </button>
            </div>

            <div id="archiveModalContent">
                <!-- Content will be populated by JavaScript -->
            </div>
        </div>
    </div>

    <script>
        // Checkbox functionality
        document.addEventListener('DOMContentLoaded', function() {
            const selectAllCheckbox = document.getElementById('selectAll');
            const selectAllHeaderCheckbox = document.getElementById('selectAllHeader');
            const archiveCheckboxes = document.querySelectorAll('.archive-checkbox');
            const exportSelectedBtn = document.getElementById('exportSelectedBtn');
            const selectedArchivesContainer = document.getElementById('selectedArchivesContainer');

            function updateExportButton() {
                const checkedBoxes = document.querySelectorAll('.archive-checkbox:checked');
                exportSelectedBtn.disabled = checkedBoxes.length === 0;

                // Update hidden checkboxes in form
                selectedArchivesContainer.innerHTML = '';
                checkedBoxes.forEach(checkbox => {
                    const hiddenInput = document.createElement('input');
                    hiddenInput.type = 'hidden';
                    hiddenInput.name = 'selected_archives[]';
                    hiddenInput.value = checkbox.value;
                    selectedArchivesContainer.appendChild(hiddenInput);
                });
            }

            // Handle "Pilih Semua" checkbox
            selectAllCheckbox.addEventListener('change', function() {
                const isChecked = this.checked;
                archiveCheckboxes.forEach(checkbox => {
                    checkbox.checked = isChecked;
                });
                selectAllHeaderCheckbox.checked = isChecked;
                updateExportButton();
            });

            // Handle header checkbox
            selectAllHeaderCheckbox.addEventListener('change', function() {
                const isChecked = this.checked;
                archiveCheckboxes.forEach(checkbox => {
                    checkbox.checked = isChecked;
                });
                selectAllCheckbox.checked = isChecked;
                updateExportButton();
            });

            // Handle individual checkboxes
            archiveCheckboxes.forEach(checkbox => {
                checkbox.addEventListener('change', function() {
                    const totalCheckboxes = archiveCheckboxes.length;
                    const checkedBoxes = document.querySelectorAll('.archive-checkbox:checked')
                        .length;

                    selectAllCheckbox.checked = checkedBoxes === totalCheckboxes;
                    selectAllHeaderCheckbox.checked = checkedBoxes === totalCheckboxes;
                    updateExportButton();
                });
            });

            // Initial state
            updateExportButton();
        });

        function showArchiveDetail(archiveId) {
            // Fetch archive detail
            fetch(`/admin/archives/${archiveId}`)
                .then(response => response.json())
                .then(result => {
                    if (result.success) {
                        const data = result.data;
                        const modalContent = document.getElementById('archiveModalContent');

                        modalContent.innerHTML = `
                    <div class="space-y-6">
                        <!-- Header -->
                        <div class="border-b pb-4">
                            <h3 class="text-xl font-bold text-gray-900">Detail Pengajuan ${data.submission.submission_number || 'SUB-' + String(data.submission.id).padStart(4, '0')}</h3>
                            <p class="text-sm text-gray-500 mt-1">Dibuat pada ${data.submission.created_at}</p>
                        </div>

                        <!-- Status -->
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <div class="flex items-center justify-between">
                                <span class="text-sm font-medium text-gray-700">Status:</span>
                                <span class="px-3 py-1 text-sm font-semibold rounded-full ${getStatusBadgeClass(data.submission.status)}">
                                    ${getStatusText(data.submission.status)}
                                </span>
                            </div>
                        </div>

                        <!-- Archive Info -->
                        <div class="bg-blue-50 p-4 rounded-lg">
                            <h4 class="font-semibold text-blue-900 mb-2">Informasi Arsip</h4>
                            <div class="grid grid-cols-2 gap-4 text-sm">
                                <div>
                                    <p><span class="font-medium">ID Arsip:</span> ${data.id}</p>
                                    <p><span class="font-medium">Tanggal Arsip:</span> ${data.archive_date ? new Date(data.archive_date).toLocaleDateString('id-ID') : 'N/A'}</p>
                                </div>
                                <div>
                                    <p><span class="font-medium">Catatan:</span> ${data.notes || 'Pengajuan selesai diproses dan diarsipkan'}</p>
                                </div>
                            </div>
                        </div>

                        <!-- Guideline Info -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <h4 class="font-semibold text-gray-900 mb-2">Informasi Layanan</h4>
                                <div class="space-y-2 text-sm">
                                    <p><span class="font-medium">Jenis:</span> ${data.submission.guideline ? data.submission.guideline.title : 'N/A'}</p>
                                    <p><span class="font-medium">Tipe:</span> ${data.submission.guideline ? (data.submission.guideline.type === 'pnbp' ? 'PNBP (Berbayar)' : 'Non-PNBP (Gratis)') : 'N/A'}</p>
                                    ${data.submission.guideline && data.submission.guideline.fee > 0 ? `<p><span class="font-medium">Biaya:</span> Rp ${new Intl.NumberFormat('id-ID').format(data.submission.guideline.fee)}</p>` : ''}
                                </div>
                            </div>

                            <div>
                                <h4 class="font-semibold text-gray-900 mb-2">Periode Data</h4>
                                <div class="space-y-2 text-sm">
                                    <p><span class="font-medium">Mulai:</span> ${data.submission.start_date ? new Date(data.submission.start_date).toLocaleDateString('id-ID') : 'N/A'}</p>
                                    <p><span class="font-medium">Akhir:</span> ${data.submission.end_date ? new Date(data.submission.end_date).toLocaleDateString('id-ID') : 'N/A'}</p>
                                </div>
                            </div>
                        </div>

                        <!-- Purpose -->
                        <div>
                            <h4 class="font-semibold text-gray-900 mb-2">Tujuan Penggunaan</h4>
                            <p class="text-sm text-gray-700 bg-gray-50 p-3 rounded-lg">${data.submission.purpose}</p>
                        </div>

                        <!-- Payment Info (if exists) -->
                        ${data.submission.payment ? `
                                    <div>
                                        <h4 class="font-semibold text-gray-900 mb-2">Informasi Pembayaran</h4>
                                        <div class="bg-blue-50 p-4 rounded-lg">
                                            <div class="grid grid-cols-2 gap-4 text-sm">
                                                <div>
                                                    <p><span class="font-medium">Jumlah:</span> Rp ${new Intl.NumberFormat('id-ID').format(data.submission.payment.amount)}</p>
                                                    <p><span class="font-medium">Status:</span> ${data.submission.payment.status === 'verified' ? 'Terverifikasi' : data.submission.payment.status === 'uploaded' ? 'Menunggu Verifikasi' : 'Pending'}</p>
                                                </div>
                                                <div>
                                                    ${data.submission.payment.method ? `<p><span class="font-medium">Metode:</span> ${data.submission.payment.method}</p>` : ''}
                                                    ${data.submission.payment.reference ? `<p><span class="font-medium">Referensi:</span> ${data.submission.payment.reference}</p>` : ''}
                                                    ${data.submission.payment.paid_at ? `<p><span class="font-medium">Dibayar:</span> ${data.submission.payment.paid_at}</p>` : ''}
                                                </div>
                                            </div>
                                            ${data.submission.payment.e_billing_path ? `
                                        <div class="mt-4 pt-4 border-t border-blue-200">
                                            <h5 class="font-medium text-blue-900 mb-2">File e-Billing dari Admin</h5>
                                            <div class="flex items-center justify-between bg-white p-3 rounded-lg">
                                                <div class="flex items-center space-x-3">
                                                    <svg class="w-5 h-5 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4z" clip-rule="evenodd"></path>
                                                    </svg>
                                                    <div>
                                                        <p class="text-sm font-medium text-gray-900">${data.submission.payment.e_billing_filename || 'e-Billing.pdf'}</p>
                                                        <p class="text-xs text-gray-500">Diupload oleh Admin</p>
                                                    </div>
                                                </div>
                                                <a href="/admin/payments/${data.submission.payment.id}/e-billing/download" class="bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold py-2 px-3 rounded">
                                                    Download
                                                </a>
                                            </div>
                                        </div>
                                    ` : ''}
                                        </div>
                                    </div>
                                ` : ''}

                        <!-- Uploaded Files (if any) -->
                        ${data.submission.files && data.submission.files.length > 0 ? `
                                    <div>
                                        <h4 class="font-semibold text-gray-900 mb-2">Dokumen yang Diupload</h4>
                                        <div class="space-y-2">
                                            ${data.submission.files.map(file => `
                                        <div class="flex items-center justify-between bg-blue-50 p-3 rounded-lg">
                                            <div class="flex items-center space-x-3">
                                                <svg class="w-5 h-5 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4z" clip-rule="evenodd"></path>
                                                </svg>
                                                <div>
                                                    <p class="text-sm font-medium text-gray-900">${file.document_name}</p>
                                                    <p class="text-xs text-gray-500">${file.file_name} • ${file.file_size_human} • Diupload: ${new Date(file.created_at).toLocaleDateString('id-ID')}</p>
                                                </div>
                                            </div>
                                            <a href="#" onclick="downloadFile(${data.submission.id}, ${file.id}); return false;" class="text-blue-600 hover:text-blue-800 text-xs font-medium underline">
                                                Download
                                            </a>
                                        </div>
                                    `).join('')}
                                        </div>
                                    </div>
                                ` : ''}

                        <!-- Generated Documents (if any) -->
                        ${data.submission.generatedDocuments && data.submission.generatedDocuments.length > 0 ? `
                                    <div>
                                        <h4 class="font-semibold text-gray-900 mb-2">Dokumen yang Dihasilkan</h4>
                                        <div class="space-y-2">
                                            ${data.submission.generatedDocuments.map(doc => `
                                        <div class="flex items-center justify-between bg-green-50 p-3 rounded-lg">
                                            <div class="flex items-center space-x-3">
                                                <svg class="w-5 h-5 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4z" clip-rule="evenodd"></path>
                                                </svg>
                                                <div>
                                                    <p class="text-sm font-medium text-gray-900">${doc.document_name}</p>
                                                    <p class="text-xs text-gray-500">${doc.formatted_file_size} • Oleh: ${doc.uploader_name} • ${new Date(doc.created_at).toLocaleDateString('id-ID')}</p>
                                                </div>
                                            </div>
                                            <button onclick="downloadGeneratedDocument(${data.submission.id}, ${doc.id})" class="bg-green-600 hover:bg-green-700 text-white text-xs font-bold py-2 px-3 rounded">
                                                Download
                                            </button>
                                        </div>
                                    `).join('')}
                                        </div>
                                    </div>
                                ` : ''}

                        <!-- History -->
                        ${data.submission.histories && data.submission.histories.length > 0 ? `
                                    <div>
                                        <h4 class="font-semibold text-gray-900 mb-2">Riwayat Pengajuan</h4>
                                        <div class="space-y-3">
                                            ${data.submission.histories.map(history => `
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

                        document.getElementById('archiveDetailModal').classList.remove('hidden');
                    } else {
                        alert('Gagal memuat detail arsip: ' + (result.message || 'Terjadi kesalahan'));
                    }
                })
                .catch(error => {
                    console.error('Error loading archive detail:', error);
                    alert('Terjadi kesalahan saat memuat detail arsip');
                });
        }

        function hideArchiveModal(modalId) {
            document.getElementById(modalId).classList.add('hidden');
        }

        function getStatusText(status) {
            const statusTexts = {
                'pending': 'Menunggu Review',
                'verified': 'Terverifikasi',
                'payment_pending': 'Menunggu Pembayaran',
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

        function downloadFile(submissionId, fileId) {
            const link = document.createElement('a');
            link.href = `/admin/submissions/${submissionId}/files/${fileId}/download`;
            link.style.display = 'none';
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
        }

        function downloadGeneratedDocument(submissionId, documentId) {
            const link = document.createElement('a');
            link.href = `/admin/data-uploads/${documentId}/download`;
            link.style.display = 'none';
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
        }
    </script>
@endsection
