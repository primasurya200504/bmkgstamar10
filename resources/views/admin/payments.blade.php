@extends('layouts.app')

@section('content')
    <!-- Header -->
    <header class="bg-white shadow-sm border-b border-gray-100 px-8 py-6">
        <div class="flex justify-between items-center">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Manajemen Pembayaran</h1>
                <p class="text-gray-600 mt-2">Kelola semua pembayaran dari pengguna</p>
            </div>

            <div class="flex items-center space-x-4">
                <button onclick="showEBillingModal()"
                    class="bg-green-600 hover:bg-green-700 text-white px-4 py-3 rounded-xl font-medium transition-colors">
                    <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12">
                        </path>
                    </svg>
                    Upload E-Billing
                </button>
                <select id="statusFilter" class="p-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500">
                    <option value="">Semua Status</option>
                    <option value="pending">Pending</option>
                    <option value="paid">Sudah Bayar</option>
                    <option value="failed">Gagal</option>
                    <option value="refunded">Dikembalikan</option>
                </select>
                <button onclick="loadPayments()"
                    class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-3 rounded-xl font-medium transition-colors">
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

    <!-- Content -->
    <div class="p-8">
        <div class="bg-white rounded-2xl shadow-lg p-8 border border-gray-100">
            <div class="overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th
                                    class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                    No. Pembayaran</th>
                                <th
                                    class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                    Pengajuan</th>
                                <th
                                    class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                    Jumlah</th>
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
                                <td colspan="6" class="px-6 py-8 text-center text-gray-500">
                                    <div class="flex items-center justify-center">
                                        <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-gray-400"
                                            xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
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
    </div>

    <!-- Payment Detail Modal -->
    <div id="paymentDetailModal"
        class="modal-overlay hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white rounded-2xl p-8 w-full max-w-4xl mx-4 max-h-[90vh] overflow-y-auto">
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-2xl font-bold text-gray-900">Detail Pembayaran</h3>
                <button onclick="hideModal('paymentDetailModal')" class="text-gray-500 hover:text-gray-700 p-2">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12">
                        </path>
                    </svg>
                </button>
            </div>

            <div id="paymentModalContent">
                <!-- Content will be populated by JavaScript -->
            </div>
        </div>
    </div>

    <!-- Payment Status Update Modal -->
    <div id="paymentStatusModal"
        class="modal-overlay hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white rounded-2xl p-8 w-full max-w-md mx-4">
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-2xl font-bold text-gray-900">Update Status Pembayaran</h3>
                <button onclick="hideModal('paymentStatusModal')" class="text-gray-500 hover:text-gray-700 p-2">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12">
                        </path>
                    </svg>
                </button>
            </div>

            <form id="paymentStatusForm" class="space-y-6">
                @csrf
                <input type="hidden" id="paymentId" name="payment_id">

                <div>
                    <label for="paymentStatus" class="block text-sm font-semibold text-gray-700 mb-3">Status
                        Pembayaran</label>
                    <select id="paymentStatus" name="status" required
                        class="w-full p-4 border border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500">
                        <option value="">Pilih status</option>
                        <option value="pending">Pending</option>
                        <option value="paid">Sudah Bayar</option>
                        <option value="failed">Gagal</option>
                        <option value="refunded">Dikembalikan</option>
                    </select>
                </div>

                <div>
                    <label for="paymentNotes" class="block text-sm font-semibold text-gray-700 mb-3">Catatan
                        (Opsional)</label>
                    <textarea id="paymentNotes" name="notes" rows="3"
                        class="w-full p-4 border border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500"
                        placeholder="Tambahkan catatan untuk perubahan status..."></textarea>
                </div>

                <div class="pt-6 border-t border-gray-200">
                    <button type="submit"
                        class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-4 px-6 rounded-xl transition-all duration-200">
                        Update Status
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- E-Billing Upload Modal -->
    <div id="eBillingModal"
        class="modal-overlay hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white rounded-2xl p-8 w-full max-w-md mx-4">
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-2xl font-bold text-gray-900">Upload E-Billing</h3>
                <button onclick="hideModal('eBillingModal')" class="text-gray-500 hover:text-gray-700 p-2">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12">
                        </path>
                    </svg>
                </button>
            </div>

            <form id="eBillingForm" class="space-y-6">
                @csrf

                <div>
                    <label for="eBillingFile" class="block text-sm font-semibold text-gray-700 mb-3">File
                        E-Billing</label>
                    <input type="file" id="eBillingFile" name="e_billing_file" accept=".pdf,.jpg,.jpeg,.png"
                        class="w-full p-4 border border-gray-300 rounded-xl focus:ring-2 focus:ring-green-500 file:mr-4 file:py-2 file:px-4 file:rounded-l-xl file:border-0 file:text-sm file:font-semibold file:bg-green-50 file:text-green-700 hover:file:bg-green-100">
                    <p class="mt-2 text-sm text-gray-500">Upload file E-Billing dalam format PDF, JPG, atau PNG (max 5MB)
                    </p>
                </div>

                <div>
                    <label for="eBillingNotes" class="block text-sm font-semibold text-gray-700 mb-3">Catatan
                        (Opsional)</label>
                    <textarea id="eBillingNotes" name="notes" rows="3"
                        class="w-full p-4 border border-gray-300 rounded-xl focus:ring-2 focus:ring-green-500"
                        placeholder="Tambahkan catatan untuk upload E-Billing..."></textarea>
                </div>

                <div class="pt-6 border-t border-gray-200">
                    <button type="submit"
                        class="w-full bg-green-600 hover:bg-green-700 text-white font-bold py-4 px-6 rounded-xl transition-all duration-200">
                        Upload E-Billing
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script src="{{ asset('js/admin/payments.js') }}"></script>
@endsection
