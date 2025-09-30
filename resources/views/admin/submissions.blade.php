@extends('layouts.app')

@section('content')
    <!-- Header -->
    <header class="bg-white shadow-sm border-b border-gray-100 px-8 py-6">
        <div class="flex justify-between items-center">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Manajemen Pengajuan</h1>
                <p class="text-gray-600 mt-2">Kelola semua pengajuan data dan dokumen dari pengguna</p>
            </div>

            <div class="flex items-center space-x-4">
                <select id="statusFilter" class="p-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500">
                    <option value="">Semua Status</option>
                    <option value="pending">Pending</option>
                    <option value="verified">Terverifikasi</option>
                    <option value="payment_pending">Menunggu Pembayaran</option>
                    <option value="paid">Sudah Bayar</option>
                    <option value="processing">Sedang Diproses</option>
                    <option value="completed">Selesai</option>
                    <option value="rejected">Ditolak</option>
                </select>
                <button onclick="loadSubmissions()"
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

    <!-- Detail Modal -->
    <div id="detailModal"
        class="modal-overlay hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white rounded-2xl p-8 w-full max-w-4xl mx-4 max-h-[90vh] overflow-y-auto">
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-2xl font-bold text-gray-900">Detail Pengajuan</h3>
                <button onclick="hideModal('detailModal')" class="text-gray-500 hover:text-gray-700 p-2">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12">
                        </path>
                    </svg>
                </button>
            </div>

            <div id="modalContent">
                <!-- Content will be populated by JavaScript -->
            </div>
        </div>
    </div>

    <!-- Status Update Modal -->
    <div id="statusModal"
        class="modal-overlay hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white rounded-2xl p-8 w-full max-w-md mx-4">
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-2xl font-bold text-gray-900">Update Status</h3>
                <button onclick="hideModal('statusModal')" class="text-gray-500 hover:text-gray-700 p-2">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12">
                        </path>
                    </svg>
                </button>
            </div>

            <form id="statusForm" class="space-y-6">
                @csrf
                <input type="hidden" id="statusSubmissionId" name="submission_id">

                <div>
                    <label for="status" class="block text-sm font-semibold text-gray-700 mb-3">Status Baru</label>
                    <select id="status" name="status" required
                        class="w-full p-4 border border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500">
                        <option value="">Pilih status</option>
                        <option value="pending">Pending</option>
                        <option value="verified">Terverifikasi</option>
                        <option value="payment_pending">Menunggu Pembayaran</option>
                        <option value="paid">Sudah Bayar</option>
                        <option value="processing">Sedang Diproses</option>
                        <option value="completed">Selesai</option>
                        <option value="rejected">Ditolak</option>
                    </select>
                </div>

                <div id="rejectionReasonField" style="display: none;">
                    <label for="rejection_reason" class="block text-sm font-semibold text-gray-700 mb-3">Alasan
                        Penolakan</label>
                    <textarea id="rejection_reason" name="rejection_reason" rows="3"
                        class="w-full p-4 border border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500"
                        placeholder="Jelaskan alasan penolakan pengajuan..."></textarea>
                </div>

                <div>
                    <label for="notes" class="block text-sm font-semibold text-gray-700 mb-3">Catatan
                        (Opsional)</label>
                    <textarea id="notes" name="notes" rows="3"
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

    <script src="{{ asset('js/admin/submissions.js') }}"></script>
@endsection
