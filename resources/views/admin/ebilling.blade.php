    @extends('layouts.app')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900">
                <div class="flex justify-between items-center mb-6">
                    <h1 class="text-2xl font-bold text-gray-800">Upload Dokumen & e-Billing</h1>
                    <a href="{{ route('admin.dashboard') }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                        Kembali ke Dashboard
                    </a>
                </div>

                <div class="mb-8">
                    <p class="text-gray-600 mb-4">
                        Upload dokumen verifikasi untuk pengajuan non-PNBP atau e-Billing untuk pengajuan PNBP yang telah diproses.
                        Setelah upload, user dapat melanjutkan ke tahap berikutnya sesuai jenis pengajuan.
                    </p>
                </div>

                <!-- Search and Filter -->
                <div class="mb-6">
                    <div class="flex flex-col md:flex-row gap-4">
                        <div class="flex-1">
                            <input type="text" id="searchInput" placeholder="Cari berdasarkan nama user atau judul panduan..."
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        </div>
                        <div class="flex gap-2">
                            <select id="statusFilter" class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                <option value="">Semua Status</option>
                                <option value="Diproses">Diproses</option>
                                <option value="payment_pending">Menunggu Pembayaran</option>
                                <option value="paid">Sudah Bayar</option>
                                <option value="verified">Dokumen Diupload</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Submissions List -->
                <div class="overflow-x-auto">
                    <table class="min-w-full bg-white">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">User</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kategori Data</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Panduan</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Biaya</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200" id="submissionsTable">
                            @foreach($submissions ?? [] as $submission)
                                <tr class="hover:bg-gray-50 submission-row" data-status="{{ $submission->status }}" data-user="{{ $submission->user->name }}" data-guideline="{{ $submission->guideline->title ?? '' }}">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $submission->id }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $submission->user->name }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                            @if($submission->guideline->type == 'pnbp') bg-green-100 text-green-800
                                            @else bg-blue-100 text-blue-800 @endif">
                                            {{ strtoupper($submission->guideline->type ?? 'N/A') }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $submission->guideline->title ?? 'N/A' }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">Rp {{ number_format($submission->guideline->fee ?? 0, 0, ',', '.') }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                            @if($submission->status == 'Diproses') bg-blue-100 text-blue-800
                                            @elseif($submission->status == 'payment_pending') bg-yellow-100 text-yellow-800
                                            @elseif($submission->status == 'paid') bg-green-100 text-green-800
                                            @elseif($submission->status == 'verified') bg-green-100 text-green-800
                                            @else bg-gray-100 text-gray-800 @endif">
                                            @if($submission->status == 'Diproses')
                                                @if($submission->guideline && $submission->guideline->fee > 0)
                                                    Menunggu e-Billing
                                                @else
                                                    Menunggu Dokumen
                                                @endif
                                            @elseif($submission->status == 'payment_pending') Menunggu Pembayaran
                                            @elseif($submission->status == 'paid') Sudah Bayar
                                            @elseif($submission->status == 'verified') Dokumen Diupload
                                            @else {{ $submission->status }} @endif
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        @if($submission->status == 'Diproses' || $submission->status == 'verified')
                                            @if($submission->guideline && $submission->guideline->fee > 0)
                                                <button onclick="openEBillingModal({{ $submission->id }}, '{{ $submission->user->name }}', '{{ $submission->guideline->title ?? 'N/A' }}', '{{ number_format($submission->guideline->fee ?? 0, 0, ',', '.') }}', 'PNBP')"
                                                        class="bg-green-500 hover:bg-green-700 text-white font-bold py-1 px-3 rounded text-xs">
                                                    Upload e-Billing
                                                </button>
                                            @else
                                                <button onclick="openEBillingModal({{ $submission->id }}, '{{ $submission->user->name }}', '{{ $submission->guideline->title ?? 'N/A' }}', '0', 'Non-PNBP')"
                                                        class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-1 px-3 rounded text-xs">
                                                    Upload Dokumen
                                                </button>
                                            @endif
                                        @elseif($submission->status == 'payment_pending' || $submission->status == 'paid')
                                            <span class="text-gray-500">
                                                @if($submission->guideline && $submission->guideline->fee > 0)
                                                    e-Billing sudah dikirim
                                                @else
                                                    Dokumen sudah diupload
                                                @endif
                                            </span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="mt-4">
                    {{ $submissions->links() ?? '' }}
                </div>

                <!-- Empty State -->
                @if(empty($submissions) || $submissions->isEmpty())
                    <div class="text-center py-12">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-gray-900">Tidak ada pengajuan yang perlu diproses</h3>
                        <p class="mt-1 text-sm text-gray-500">Belum ada pengajuan yang siap untuk upload dokumen atau e-Billing.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- e-Billing Upload Modal -->
<div id="eBillingModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Upload e-Billing</h3>
            <div class="mb-4 p-3 bg-gray-50 rounded-lg">
                <p class="text-sm text-gray-600">
                    <strong>User:</strong> <span id="eBillingUserName"></span><br>
                    <strong>Panduan:</strong> <span id="eBillingSubmissionTitle"></span><br>
                    <strong>Biaya:</strong> Rp <span id="eBillingAmount"></span>
                </p>
            </div>
            <form id="eBillingForm" action="" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="mb-4">
                    <label for="e_billing_file" class="block text-sm font-medium text-gray-700 mb-2">File e-Billing</label>
                    <input type="file" id="e_billing_file" name="e_billing_file" accept=".pdf,.jpg,.jpeg,.png" required
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <p class="text-xs text-gray-500 mt-1">Format: PDF, JPG, PNG (Max 5MB)</p>
                </div>
                <div class="flex justify-end space-x-2">
                    <button type="button" onclick="closeEBillingModal()" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                        Batal
                    </button>
                    <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                        Upload e-Billing
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function openEBillingModal(submissionId, userName, submissionTitle, amount, type) {
    document.getElementById('eBillingUserName').textContent = userName;
    document.getElementById('eBillingSubmissionTitle').textContent = submissionTitle;
    document.getElementById('eBillingAmount').textContent = amount;

    // Update modal title and labels based on type
    const modalTitle = document.querySelector('#eBillingModal h3');
    const fileLabel = document.querySelector('label[for="e_billing_file"]');
    const submitButton = document.querySelector('#eBillingForm button[type="submit"]');

    if (type === 'PNBP') {
        modalTitle.textContent = 'Upload e-Billing';
        fileLabel.textContent = 'File e-Billing';
        submitButton.textContent = 'Upload e-Billing';
    } else {
        modalTitle.textContent = 'Upload Dokumen Verifikasi';
        fileLabel.textContent = 'File Dokumen';
        submitButton.textContent = 'Upload Dokumen';
    }

    document.getElementById('eBillingForm').action = `/admin/submissions/${submissionId}/upload-ebilling`;
    document.getElementById('eBillingModal').classList.remove('hidden');
}

function closeEBillingModal() {
    document.getElementById('eBillingModal').classList.add('hidden');
    document.getElementById('e_billing_file').value = '';
}

// Search and Filter functionality
document.getElementById('searchInput').addEventListener('input', filterTable);
document.getElementById('statusFilter').addEventListener('change', filterTable);

function filterTable() {
    const searchTerm = document.getElementById('searchInput').value.toLowerCase();
    const statusFilter = document.getElementById('statusFilter').value;
    const rows = document.querySelectorAll('.submission-row');

    rows.forEach(row => {
        const userName = row.dataset.user.toLowerCase();
        const guideline = row.dataset.guideline.toLowerCase();
        const status = row.dataset.status;

        const matchesSearch = userName.includes(searchTerm) || guideline.includes(searchTerm);
        const matchesStatus = !statusFilter || status === statusFilter;

        if (matchesSearch && matchesStatus) {
            row.style.display = '';
        } else {
            row.style.display = 'none';
        }
    });
}
</script>
@endsection
