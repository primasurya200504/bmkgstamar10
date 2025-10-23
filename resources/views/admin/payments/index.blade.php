@extends('layouts.app')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="page-header">
            <div class="flex justify-between items-start">
                <div>
                    <h1 class="page-title">Manajemen Pembayaran</h1>
                    <p class="page-subtitle">Pantau dan kelola semua transaksi pembayaran pengguna</p>
                </div>
                <a href="{{ route('admin.dashboard') }}" class="btn-modern btn-secondary">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    Kembali ke Dashboard
                </a>
            </div>
        </div>

        <div class="card-modern">
            <div class="p-6">
                <div class="overflow-x-auto">
                    <table class="modern-table min-w-full">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>User</th>
                                <th>Kategori Data</th>
                                <th>Jenis Pengajuan</th>
                                <th>Biaya</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($pnlpSubmissions ?? [] as $submission)
                                <tr>
                                    <td class="font-medium text-gray-900">#{{ $submission->id }}</td>
                                    <td>
                                        <div class="flex items-center">
                                            <div class="w-8 h-8 bg-gradient-to-br from-blue-500 to-purple-600 rounded-full flex items-center justify-center text-white font-semibold text-xs mr-3">
                                                {{ strtoupper(substr($submission->user->name, 0, 1)) }}
                                            </div>
                                            <span class="font-medium">{{ $submission->user->name }}</span>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge-modern
                                            @if($submission->guideline->type == 'pnbp') badge-completed
                                            @else badge-verified @endif">
                                            {{ strtoupper($submission->guideline->type ?? 'N/A') }}
                                        </span>
                                    </td>
                                    <td class="font-medium">{{ $submission->guideline->title ?? 'N/A' }}</td>
                                    <td class="font-semibold text-green-600">Rp {{ number_format($submission->guideline->fee ?? 0, 0, ',', '.') }}</td>
                                    <td>
                                        <span class="badge-modern badge-verified">
                                            Menunggu e-Billing
                                        </span>
                                    </td>
                                    <td>
                                        <button onclick="openEBillingModal({{ $submission->id }}, '{{ $submission->user->name }}', '{{ $submission->guideline->title ?? 'N/A' }}')"
                                                class="btn-modern btn-success">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                                            </svg>
                                            Upload e-Billing
                                        </button>
                                    </td>
                                </tr>
                            @endforeach

                            @if(isset($payments))
                                @foreach($payments as $payment)
                                    <tr>
                                        <td class="font-medium text-gray-900">#{{ $payment->id }}</td>
                                        <td>
                                            <div class="flex items-center">
                                                <div class="w-8 h-8 bg-gradient-to-br from-blue-500 to-purple-600 rounded-full flex items-center justify-center text-white font-semibold text-xs mr-3">
                                                    {{ strtoupper(substr($payment->submission->user->name ?? 'N', 0, 1)) }}
                                                </div>
                                                <span class="font-medium">{{ $payment->submission->user->name ?? 'N/A' }}</span>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="badge-modern
                                                @if($payment->submission->guideline->type == 'pnbp') badge-completed
                                                @else badge-verified @endif">
                                                {{ strtoupper($payment->submission->guideline->type ?? 'N/A') }}
                                            </span>
                                        </td>
                                        <td class="font-medium">{{ $payment->submission->guideline->title ?? 'N/A' }}</td>
                                        <td class="font-semibold text-green-600">Rp {{ number_format($payment->amount ?? 0, 0, ',', '.') }}</td>
                                        <td>
                                            <span class="badge-modern
                                                @if($payment->status == 'pending') badge-pending
                                                @elseif($payment->status == 'proof_uploaded') badge-verified
                                                @elseif($payment->status == 'verified') badge-completed
                                                @elseif($payment->status == 'rejected') badge-rejected
                                                @else badge-pending @endif">
                                                @if($payment->status == 'proof_uploaded') Bukti Diupload
                                                @elseif($payment->status == 'pending' && $payment->rejection_reason) Ditolak - Upload Ulang
                                                @else {{ ucfirst($payment->status) }} @endif
                                            </span>
                                        </td>
                                        <td>
                                            @if($payment->status == 'proof_uploaded')
                                                <div class="action-buttons">
                                                    @if($payment->payment_proof)
                                                        <a href="{{ route('admin.download.payment.proof', $payment->id) }}"
                                                           class="btn-modern btn-primary">
                                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                                            </svg>
                                                            Lihat Bukti
                                                        </a>
                                                    @endif
                                                    <form action="{{ route('admin.verify.payment', $payment->id) }}" method="POST" class="inline">
                                                        @csrf
                                                        <button type="submit" class="btn-modern btn-success">
                                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                            </svg>
                                                            Verifikasi
                                                        </button>
                                                    </form>
                                                    <button onclick="openRejectPaymentModal({{ $payment->id }})"
                                                            class="btn-modern btn-danger">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                                        </svg>
                                                        Tolak
                                                    </button>
                                                </div>
                                            @elseif($payment->status == 'verified')
                                                <span class="text-green-600 font-medium flex items-center">
                                                    <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                                    </svg>
                                                    Sudah Diverifikasi
                                                </span>
                                            @elseif($payment->status == 'rejected')
                                                <span class="text-red-600 font-medium flex items-center">
                                                    <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                                                    </svg>
                                                    Ditolak
                                                </span>
                                            @else
                                                <span class="text-gray-500 flex items-center">
                                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                    </svg>
                                                    Menunggu Bukti
                                                </span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            @endif
                        </tbody>
                    </table>
                </div>

                <div class="mt-6">
                    {{ $payments->links() }}
                </div>
            </div>
        </div>
    </div>
</div>

<!-- e-Billing Upload Modal -->
<div id="eBillingModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Upload e-Billing</h3>
            <div class="mb-4">
                <p class="text-sm text-gray-600">
                    <strong>User:</strong> <span id="eBillingUserName"></span><br>
                    <strong>Pengajuan:</strong> <span id="eBillingSubmissionTitle"></span>
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

<!-- Reject Payment Modal -->
<div id="rejectPaymentModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Tolak Pembayaran</h3>
            <form id="rejectPaymentForm" action="" method="POST">
                @csrf
                <div class="mb-4">
                    <label for="reject_reason" class="block text-sm font-medium text-gray-700 mb-2">Alasan Penolakan</label>
                    <textarea id="reject_reason" name="reject_reason" rows="4" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-red-500" required></textarea>
                </div>
                <div class="flex justify-end space-x-2">
                    <button type="button" onclick="closeRejectPaymentModal()" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                        Batal
                    </button>
                    <button type="submit" class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded">
                        Tolak Pembayaran
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function openEBillingModal(submissionId, userName, submissionTitle) {
    document.getElementById('eBillingUserName').textContent = userName;
    document.getElementById('eBillingSubmissionTitle').textContent = submissionTitle;
    document.getElementById('eBillingForm').action = `/admin/submissions/${submissionId}/upload-ebilling`;
    document.getElementById('eBillingModal').classList.remove('hidden');
}

function closeEBillingModal() {
    document.getElementById('eBillingModal').classList.add('hidden');
    document.getElementById('e_billing_file').value = '';
}

function openRejectPaymentModal(paymentId) {
    document.getElementById('rejectPaymentForm').action = `/admin/payments/${paymentId}/reject`;
    document.getElementById('rejectPaymentModal').classList.remove('hidden');
}

function closeRejectPaymentModal() {
    document.getElementById('rejectPaymentModal').classList.add('hidden');
    document.getElementById('reject_reason').value = '';
}
</script>
@endsection
