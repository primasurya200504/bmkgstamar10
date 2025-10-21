@extends('layouts.app')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900">
                <div class="flex justify-between items-center mb-6">
                    <h1 class="text-2xl font-bold text-gray-800">Arsip & Laporan</h1>
                    <a href="{{ route('admin.dashboard') }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                        Kembali ke Dashboard
                    </a>
                </div>

                <!-- Filter Form -->
                <div class="bg-gray-50 p-4 rounded-lg mb-6">
                    <form method="GET" action="{{ route('admin.archives') }}" class="flex flex-wrap gap-4 items-end">
                        <div>
                            <label for="filter_year" class="block text-sm font-medium text-gray-700 mb-1">Tahun</label>
                            <select name="year" id="filter_year" class="border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <option value="">Semua Tahun</option>
                                @for($y = date('Y'); $y >= 2020; $y--)
                                    <option value="{{ $y }}" {{ request('year') == $y ? 'selected' : '' }}>{{ $y }}</option>
                                @endfor
                            </select>
                        </div>
                        <div>
                            <label for="filter_month" class="block text-sm font-medium text-gray-700 mb-1">Bulan</label>
                            <select name="month" id="filter_month" class="border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <option value="">Semua Bulan</option>
                                @for($m = 1; $m <= 12; $m++)
                                    <option value="{{ $m }}" {{ request('month') == $m ? 'selected' : '' }}>{{ date('F', mktime(0, 0, 0, $m, 1)) }}</option>
                                @endfor
                            </select>
                        </div>
                        <div class="flex gap-2">
                            <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                Filter
                            </button>
                            <a href="{{ route('admin.archives') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                                Reset
                            </a>
                        </div>
                    </form>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full bg-white">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No. Surat</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">User</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal Arsip</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Catatan</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($archives as $archive)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                        {{ $archive->id }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $archive->submission->submission_number ?? 'SUB-' . str_pad($archive->submission->id, 4, '0', STR_PAD_LEFT) }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $archive->submission->user->name ?? 'N/A' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $archive->archive_date ? $archive->archive_date->format('d/m/Y H:i') : $archive->created_at->format('d/m/Y H:i') }}
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-500">
                                        {{ $archive->notes ?? 'Pengajuan selesai diproses dan diarsipkan' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <button onclick="showArchiveDetail({{ $archive->id }})" class="text-indigo-600 hover:text-indigo-900 transition-colors">
                                            Detail
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="mt-4">
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
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>

        <div id="archiveModalContent">
            <!-- Content will be populated by JavaScript -->
        </div>
    </div>
</div>

<script>
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
