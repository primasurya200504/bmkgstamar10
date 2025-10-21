@extends('layouts.app')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900">
                <div class="flex justify-between items-center mb-6">
                    <h1 class="text-2xl font-bold text-gray-800">Manajemen Pembayaran</h1>
                    <a href="{{ route('admin.dashboard') }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                        Kembali ke Dashboard
                    </a>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full bg-white">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">User</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kategori Data</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jenis Pengajuan</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Biaya</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($pnlpSubmissions ?? [] as $submission)
                                <tr class="hover:bg-gray-50">
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
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                            Menunggu e-Billing
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <button onclick="openEBillingModal({{ $submission->id }}, '{{ $submission->user->name }}', '{{ $submission->guideline->title ?? 'N/A' }}')"
                                                class="bg-green-500 hover:bg-green-700 text-white font-bold py-1 px-3 rounded text-xs">
                                            Upload e-Billing
                                        </button>
                                    </td>
                                </tr>
                            @endforeach

                            @if(isset($payments))
                                @foreach($payments as $payment)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $payment->id }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $payment->submission->user->name ?? 'N/A' }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                                @if($payment->submission->guideline->type == 'pnbp') bg-green-100 text-green-800
                                                @else bg-blue-100 text-blue-800 @endif">
                                                {{ strtoupper($payment->submission->guideline->type ?? 'N/A') }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $payment->submission->guideline->title ?? 'N/A' }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">Rp {{ number_format($payment->amount ?? 0, 0, ',', '.') }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                                @if($payment->status == 'pending') bg-yellow-100 text-yellow-800
                                                @elseif($payment->status == 'proof_uploaded') bg-blue-100 text-blue-800
                                                @elseif($payment->status == 'verified') bg-green-100 text-green-800
                                                @elseif($payment->status == 'rejected') bg-red-100 text-red-800
                                                @else bg-gray-100 text-gray-800 @endif">
                                                @if($payment->status == 'proof_uploaded') Bukti Diupload
                                                @elseif($payment->status == 'pending' && $payment->rejection_reason) Ditolak - Menunggu Upload Ulang
                                                @else {{ ucfirst($payment->status) }} @endif
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                            @if($payment->status == 'proof_uploaded')
                                                <div class="flex space-x-2">
                                                    @if($payment->payment_proof)
                                                        <a href="{{ route('admin.download.payment.proof', $payment->id) }}"
                                                           class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-1 px-2 rounded text-xs">Lihat Bukti</a>
                                                    @endif
                                                    <form action="{{ route('admin.verify.payment', $payment->id) }}" method="POST" class="inline">
                                                        @csrf
                                                        <button type="submit" class="bg-green-500 hover:bg-green-700 text-white font-bold py-1 px-2 rounded text-xs">Verifikasi</button>
                                                    </form>
                                                    <button onclick="openRejectPaymentModal({{ $payment->id }})"
                                                            class="bg-red-500 hover:bg-red-700 text-white font-bold py-1 px-2 rounded text-xs">Tolak</button>
                                                </div>
                                            @elseif($payment->status == 'verified')
                                                <span class="text-green-600 font-medium">Sudah Diverifikasi</span>
                                            @elseif($payment->status == 'rejected')
                                                <span class="text-red-600 font-medium">Ditolak</span>
                                            @else
                                                <span class="text-gray-500">Menunggu Bukti Pembayaran</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            @endif
                        </tbody>
                    </table>
                </div>

                <div class="mt-4">
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
