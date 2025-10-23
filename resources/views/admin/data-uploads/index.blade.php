@extends('layouts.app')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="page-header">
            <div class="flex justify-between items-start">
                <div>
                    <h1 class="page-title">Manajemen Upload Data</h1>
                    <p class="page-subtitle">Upload dan kelola dokumen hasil pengajuan pengguna</p>
                </div>
                <a href="{{ route('admin.dashboard') }}" class="btn-modern btn-secondary">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    Kembali ke Dashboard
                </a>
            </div>
        </div>

        @if(session('success'))
            <div class="bg-green-50 border border-green-200 text-green-800 px-6 py-4 rounded-lg mb-6 flex items-center">
                <svg class="w-5 h-5 mr-3" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                </svg>
                {{ session('success') }}
            </div>
        @endif

        <div class="card-modern">
            <div class="p-6">
                <div class="overflow-x-auto">
                    <table class="modern-table min-w-full">
                        <thead>
                            <tr>
                                <th>ID Pengajuan</th>
                                <th>Pengguna</th>
                                <th>Kategori Data</th>
                                <th>Jenis Pengajuan</th>
                                <th>Tanggal</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($submissions as $submission)
                                <tr>
                                    <td class="font-medium text-gray-900">
                                        {{ $submission->submission_number ?? 'SUB-' . str_pad($submission->id, 4, '0', STR_PAD_LEFT) }}
                                    </td>
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
                                    <td class="text-gray-600">{{ $submission->created_at->format('d/m/Y') }}</td>
                                    <td>
                                        <span class="badge-modern
                                            @if($submission->status == 'Diterima') badge-completed
                                            @elseif($submission->status == 'Diproses') badge-verified
                                            @elseif($submission->status == 'Selesai') badge-completed
                                            @else badge-pending @endif">
                                            {{ $submission->status }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="action-buttons">
                                            <a href="{{ route('admin.data-uploads.show', $submission->id) }}" class="btn-modern btn-primary">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                                                </svg>
                                                Upload Data
                                            </a>
                                            @if($submission->generatedDocuments->count() > 0)
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 ml-2">
                                                    <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                                    </svg>
                                                    {{ $submission->generatedDocuments->count() }} dokumen
                                                </span>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-6 py-12 text-center">
                                        <div class="flex flex-col items-center">
                                            <svg class="w-12 h-12 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                            </svg>
                                            <h3 class="text-lg font-medium text-gray-900 mb-2">Tidak ada pengajuan</h3>
                                            <p class="text-gray-500">Belum ada pengajuan yang siap untuk upload data.</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($submissions->hasPages())
                    <div class="mt-6">
                        {{ $submissions->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
