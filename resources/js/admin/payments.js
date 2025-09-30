// Admin Payments Management
document.addEventListener('DOMContentLoaded', function() {
    initializePaymentsPage();
});

function initializePaymentsPage() {
    loadPayments();
    setupEventListeners();
}

function setupEventListeners() {
    // Status filter
    document.getElementById('statusFilter').addEventListener('change', function() {
        loadPayments();
    });

    // Payment status form submission
    document.getElementById('paymentStatusForm').addEventListener('submit', function(e) {
        e.preventDefault();
        updatePaymentStatus();
    });

    // E-Billing form submission
    document.getElementById('eBillingForm').addEventListener('submit', function(e) {
        e.preventDefault();
        uploadEBilling();
    });
}

function loadPayments() {
    const statusFilter = document.getElementById('statusFilter').value;
    const tableBody = document.getElementById('paymentsTableBody');

    // Show loading
    tableBody.innerHTML = `
        <tr>
            <td colspan="6" class="px-6 py-8 text-center text-gray-500">
                <div class="flex items-center justify-center">
                    <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    Loading...
                </div>
            </td>
        </tr>
    `;

    // Build URL with filter
    let url = '/api/admin/payments';
    if (statusFilter) {
        url += `?status=${statusFilter}`;
    }

    fetch(url, {
        method: 'GET',
        headers: {
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        renderPaymentsTable(data.payments || []);
    })
    .catch(error => {
        console.error('Error loading payments:', error);
        tableBody.innerHTML = `
            <tr>
                <td colspan="6" class="px-6 py-8 text-center text-red-500">
                    <div class="flex items-center justify-center">
                        <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        Error loading payments. Please try again.
                    </div>
                </td>
            </tr>
        `;
    });
}

function renderPaymentsTable(payments) {
    const tableBody = document.getElementById('paymentsTableBody');

    if (payments.length === 0) {
        tableBody.innerHTML = `
            <tr>
                <td colspan="6" class="px-6 py-8 text-center text-gray-500">
                    <div class="flex items-center justify-center">
                        <svg class="w-12 h-12 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path>
                        </svg>
                        <h3 class="text-lg font-medium text-gray-900 mb-2">Tidak ada pembayaran</h3>
                        <p class="text-sm text-gray-500">Belum ada pembayaran dengan filter yang dipilih.</p>
                    </div>
                </td>
            </tr>
        `;
        return;
    }

    let html = '';
    payments.forEach(payment => {
        const statusConfig = getPaymentStatusConfig(payment.status);
        const createdDate = new Date(payment.created_at).toLocaleDateString('id-ID');
        const amount = formatCurrency(payment.amount);

        html += `
            <tr class="hover:bg-gray-50 transition-colors">
                <td class="px-6 py-4 whitespace-nowrap">
                    <div class="text-sm font-medium text-gray-900">${payment.payment_number || 'N/A'}</div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                    <div class="text-sm text-gray-900">${payment.submission?.submission_number || 'N/A'}</div>
                    <div class="text-sm text-gray-500">${payment.submission?.guideline?.title || 'N/A'}</div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                    <div class="text-sm font-medium text-gray-900">${amount}</div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full ${statusConfig.color}">
                        ${statusConfig.text}
                    </span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                    ${createdDate}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                    <div class="flex space-x-2">
                        <button onclick="showPaymentDetail(${payment.id})"
                            class="text-indigo-600 hover:text-indigo-900 p-2 rounded-lg hover:bg-indigo-50 transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                            </svg>
                        </button>
                        <button onclick="showPaymentStatusModal(${payment.id}, '${payment.status}')"
                            class="text-green-600 hover:text-green-900 p-2 rounded-lg hover:bg-green-50 transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                            </svg>
                        </button>
                    </div>
                </td>
            </tr>
        `;
    });

    tableBody.innerHTML = html;
}

function showPaymentDetail(paymentId) {
    fetch(`/api/admin/payments/${paymentId}`, {
        method: 'GET',
        headers: {
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        renderPaymentDetail(data.payment);
        showModal('paymentDetailModal');
    })
    .catch(error => {
        console.error('Error loading payment detail:', error);
        alert('Error loading payment detail. Please try again.');
    });
}

function renderPaymentDetail(payment) {
    const modalContent = document.getElementById('paymentModalContent');
    const statusConfig = getPaymentStatusConfig(payment.status);
    const createdDate = new Date(payment.created_at).toLocaleDateString('id-ID');
    const updatedDate = new Date(payment.updated_at).toLocaleDateString('id-ID');
    const amount = formatCurrency(payment.amount);

    modalContent.innerHTML = `
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- Payment Information -->
            <div class="space-y-6">
                <div>
                    <h4 class="text-lg font-semibold text-gray-900 mb-4">Informasi Pembayaran</h4>
                    <div class="space-y-3">
                        <div class="flex justify-between">
                            <span class="text-sm font-medium text-gray-500">No. Pembayaran:</span>
                            <span class="text-sm text-gray-900">${payment.payment_number || 'N/A'}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-sm font-medium text-gray-500">Jumlah:</span>
                            <span class="text-sm font-semibold text-gray-900">${amount}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-sm font-medium text-gray-500">Status:</span>
                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full ${statusConfig.color}">${statusConfig.text}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-sm font-medium text-gray-500">Metode Pembayaran:</span>
                            <span class="text-sm text-gray-900">${payment.payment_method || 'N/A'}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-sm font-medium text-gray-500">Tanggal Dibuat:</span>
                            <span class="text-sm text-gray-900">${createdDate}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-sm font-medium text-gray-500">Terakhir Update:</span>
                            <span class="text-sm text-gray-900">${updatedDate}</span>
                        </div>
                    </div>
                </div>

                <!-- Submission Information -->
                <div>
                    <h4 class="text-lg font-semibold text-gray-900 mb-4">Informasi Pengajuan</h4>
                    <div class="space-y-3">
                        <div class="flex justify-between">
                            <span class="text-sm font-medium text-gray-500">No. Surat:</span>
                            <span class="text-sm text-gray-900">${payment.submission?.submission_number || 'N/A'}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-sm font-medium text-gray-500">Jenis Data:</span>
                            <span class="text-sm text-gray-900">${payment.submission?.guideline?.title || 'N/A'}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-sm font-medium text-gray-500">Tipe:</span>
                            <span class="text-sm text-gray-900">${payment.submission?.guideline?.type || 'N/A'}</span>
                        </div>
                    </div>
                </div>

                <!-- User Information -->
                <div>
                    <h4 class="text-lg font-semibold text-gray-900 mb-4">Informasi Pengguna</h4>
                    <div class="space-y-3">
                        <div class="flex justify-between">
                            <span class="text-sm font-medium text-gray-500">Nama:</span>
                            <span class="text-sm text-gray-900">${payment.submission?.user?.name || 'N/A'}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-sm font-medium text-gray-500">Email:</span>
                            <span class="text-sm text-gray-900">${payment.submission?.user?.email || 'N/A'}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-sm font-medium text-gray-500">Phone:</span>
                            <span class="text-sm text-gray-900">${payment.submission?.user?.phone || 'N/A'}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Additional Information -->
            <div class="space-y-6">
                <!-- Payment Details -->
                <div>
                    <h4 class="text-lg font-semibold text-gray-900 mb-4">Detail Pembayaran</h4>
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Referensi Pembayaran</label>
                            <p class="text-sm text-gray-900 bg-gray-50 p-3 rounded-lg">${payment.reference || 'Tidak ada referensi'}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Catatan Admin</label>
                            <p class="text-sm text-gray-900 bg-gray-50 p-3 rounded-lg">${payment.notes || 'Tidak ada catatan'}</p>
                        </div>
                    </div>
                </div>

                <!-- Payment History -->
                <div>
                    <h4 class="text-lg font-semibold text-gray-900 mb-4">Riwayat Pembayaran</h4>
                    ${renderPaymentHistory(payment.history || [])}
                </div>

                <!-- Payment Proof -->
                ${payment.proof_path ? `
                    <div>
                        <h4 class="text-lg font-semibold text-gray-900 mb-4">Bukti Pembayaran</h4>
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <a href="/storage/${payment.proof_path}" target="_blank" class="text-indigo-600 hover:text-indigo-900 font-medium">
                                <svg class="w-5 h-5 inline mr-2" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm12 12H4l4-8 3 6 2-4 3 6z" clip-rule="evenodd"></path>
                                </svg>
                                Lihat Bukti Pembayaran
                            </a>
                        </div>
                    </div>
                ` : ''}
            </div>
        </div>
    `;
}

function renderPaymentHistory(history) {
    if (history.length === 0) {
        return '<p class="text-sm text-gray-500">Tidak ada riwayat</p>';
    }

    return `
        <div class="space-y-3">
            ${history.map(item => {
                const statusConfig = getPaymentStatusConfig(item.status);
                const date = new Date(item.created_at).toLocaleString('id-ID');
                return `
                    <div class="flex items-start space-x-3">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 rounded-full ${statusConfig.bgColor} flex items-center justify-center">
                                <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 111.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                </svg>
                            </div>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-gray-900">${statusConfig.text}</p>
                            <p class="text-sm text-gray-500">${date}</p>
                            ${item.notes ? `<p class="text-sm text-gray-700 mt-1">${item.notes}</p>` : ''}
                        </div>
                    </div>
                `;
            }).join('')}
        </div>
    `;
}

function showPaymentStatusModal(paymentId, currentStatus) {
    document.getElementById('paymentId').value = paymentId;
    document.getElementById('paymentStatus').value = currentStatus;
    document.getElementById('paymentNotes').value = '';
    showModal('paymentStatusModal');
}

function updatePaymentStatus() {
    const formData = new FormData(document.getElementById('paymentStatusForm'));

    fetch('/api/admin/payments/update-status', {
        method: 'POST',
        headers: {
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            hideModal('paymentStatusModal');
            loadPayments();
            showNotification('Status pembayaran berhasil diupdate!', 'success');
        } else {
            showNotification('Gagal update status pembayaran: ' + (data.message || 'Unknown error'), 'error');
        }
    })
    .catch(error => {
        console.error('Error updating payment status:', error);
        showNotification('Error updating payment status. Please try again.', 'error');
    });
}

function getPaymentStatusConfig(status) {
    const configs = {
        'pending': { text: 'Pending', color: 'bg-yellow-100 text-yellow-800', bgColor: 'bg-yellow-500' },
        'paid': { text: 'Sudah Bayar', color: 'bg-green-100 text-green-800', bgColor: 'bg-green-500' },
        'failed': { text: 'Gagal', color: 'bg-red-100 text-red-800', bgColor: 'bg-red-500' },
        'refunded': { text: 'Dikembalikan', color: 'bg-purple-100 text-purple-800', bgColor: 'bg-purple-500' }
    };
    return configs[status] || { text: status, color: 'bg-gray-100 text-gray-800', bgColor: 'bg-gray-500' };
}

function formatCurrency(amount) {
    return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR'
    }).format(amount || 0);
}

function showModal(modalId) {
    document.getElementById(modalId).classList.remove('hidden');
}

function hideModal(modalId) {
    document.getElementById(modalId).classList.add('hidden');
}

function showNotification(message, type = 'info') {
    // Simple notification - you can enhance this with a proper notification system
    const notification = document.createElement('div');
    notification.className = `fixed top-4 right-4 px-6 py-4 rounded-lg text-white z-50 ${
        type === 'success' ? 'bg-green-500' :
        type === 'error' ? 'bg-red-500' :
        'bg-blue-500'
    }`;
    notification.textContent = message;

    document.body.appendChild(notification);

    setTimeout(() => {
        notification.remove();
    }, 3000);
}

// E-Billing functions
function showEBillingModal() {
    document.getElementById('eBillingFile').value = '';
    document.getElementById('eBillingNotes').value = '';
    showModal('eBillingModal');
}

function uploadEBilling() {
    const formData = new FormData(document.getElementById('eBillingForm'));
    const fileInput = document.getElementById('eBillingFile');

    // Validate file
    if (!fileInput.files[0]) {
        showNotification('Silakan pilih file E-Billing terlebih dahulu.', 'error');
        return;
    }

    const file = fileInput.files[0];
    const maxSize = 5 * 1024 * 1024; // 5MB
    const allowedTypes = ['application/pdf', 'image/jpeg', 'image/jpg', 'image/png'];

    if (file.size > maxSize) {
        showNotification('Ukuran file maksimal 5MB.', 'error');
        return;
    }

    if (!allowedTypes.includes(file.type)) {
        showNotification('Format file harus PDF, JPG, atau PNG.', 'error');
        return;
    }

    // Show loading state
    const submitButton = document.querySelector('#eBillingForm button[type="submit"]');
    const originalText = submitButton.textContent;
    submitButton.textContent = 'Mengupload...';
    submitButton.disabled = true;

    fetch('/api/admin/payments/upload-e-billing', {
        method: 'POST',
        headers: {
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            hideModal('eBillingModal');
            showNotification('E-Billing berhasil diupload!', 'success');
            loadPayments(); // Refresh the payments list
        } else {
            showNotification('Gagal upload E-Billing: ' + (data.message || 'Unknown error'), 'error');
        }
    })
    .catch(error => {
        console.error('Error uploading E-Billing:', error);
        showNotification('Error uploading E-Billing. Please try again.', 'error');
    })
    .finally(() => {
        // Reset button state
        submitButton.textContent = originalText;
        submitButton.disabled = false;
    });
}

// Make functions globally available
window.showPaymentDetail = showPaymentDetail;
window.showPaymentStatusModal = showPaymentStatusModal;
window.showEBillingModal = showEBillingModal;
window.hideModal = hideModal;
