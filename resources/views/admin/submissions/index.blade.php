@extends('layouts.app')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="page-header">
            <div class="flex justify-between items-start">
                <div>
                    <h1 class="page-title">Manajemen Pengajuan</h1>
                    <p class="page-subtitle">Kelola dan pantau semua pengajuan data dari pengguna</p>
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
                                <th>Status</th>
                                <th>Tanggal</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($submissions as $submission)
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
                                    <td>
                                        <span class="badge-modern
                                            @if($submission->status == 'pending') badge-pending
                                            @elseif($submission->status == 'verified') badge-completed
                                            @elseif($submission->status == 'rejected') badge-rejected
                                            @elseif($submission->status == 'Diproses') badge-verified
                                            @elseif($submission->status == 'completed') badge-completed
                                            @else badge-pending @endif">
                                            {{ $submission->status }}
                                        </span>
                                    </td>
                                    <td class="text-gray-600">{{ $submission->created_at->format('d/m/Y') }}</td>
                                    <td>
                                        <div class="action-buttons">
                                            <a href="{{ route('admin.submissions.show', $submission) }}" class="btn-modern btn-primary">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                                </svg>
                                                Detail
                                            </a>
                                            <form action="{{ route('admin.submissions.approve', $submission) }}" method="POST" class="inline">
                                                @csrf
                                                <button type="submit" class="btn-modern btn-success">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                    </svg>
                                                    Proses
                                                </button>
                                            </form>
                                            <button onclick="openRejectModal({{ $submission->id }})" class="btn-modern btn-danger">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                                </svg>
                                                Tolak
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="mt-6">
                    {{ $submissions->links() }}
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Reject Modal -->
<div id="rejectModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Tolak Pengajuan</h3>
            <form id="rejectForm" action="" method="POST">
                @csrf
                <div class="mb-4">
                    <label for="reason" class="block text-sm font-medium text-gray-700 mb-2">Alasan Penolakan</label>
                    <textarea id="reason" name="reason" rows="4" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-red-500" required></textarea>
                </div>
                <div class="flex justify-end space-x-2">
                    <button type="button" onclick="closeRejectModal()" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                        Batal
                    </button>
                    <button type="submit" class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded">
                        Tolak
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function openRejectModal(submissionId) {
    document.getElementById('rejectForm').action = `/admin/submissions/${submissionId}/reject`;
    document.getElementById('rejectModal').classList.remove('hidden');
}

function closeRejectModal() {
    document.getElementById('rejectModal').classList.add('hidden');
    document.getElementById('reason').value = '';
}
</script>
@endsection
