@extends('layouts.app')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="page-header">
            <div class="flex justify-between items-start">
                <div>
                    <h1 class="page-title">Manajemen Panduan</h1>
                    <p class="page-subtitle">Kelola panduan dan jenis layanan yang tersedia</p>
                </div>
                <div class="flex space-x-3">
                    <a href="{{ route('admin.guidelines.create') }}" class="btn-modern btn-success">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                        Tambah Panduan
                    </a>
                    <a href="{{ route('admin.dashboard') }}" class="btn-modern btn-secondary">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                        </svg>
                        Kembali ke Dashboard
                    </a>
                </div>
            </div>
        </div>

        <div class="card-modern">
            <div class="p-6">
                <div class="overflow-x-auto">
                    <table class="modern-table min-w-full">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Judul</th>
                                <th>Tipe</th>
                                <th>Biaya</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($guidelines as $guideline)
                                <tr>
                                    <td class="font-medium text-gray-900">#{{ $guideline->id }}</td>
                                    <td>
                                        <div class="flex items-center">
                                            <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-purple-600 rounded-lg flex items-center justify-center text-white mr-3">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                                </svg>
                                            </div>
                                            <div>
                                                <div class="font-medium text-gray-900">{{ $guideline->title }}</div>
                                                <div class="text-sm text-gray-500">{{ Str::limit($guideline->description ?? '', 50) }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge-modern
                                            @if($guideline->type == 'pnbp') badge-completed
                                            @else badge-verified @endif">
                                            {{ strtoupper($guideline->type) }}
                                        </span>
                                    </td>
                                    <td class="font-semibold">
                                        @if($guideline->fee > 0)
                                            <span class="text-green-600">Rp {{ number_format($guideline->fee, 0, ',', '.') }}</span>
                                        @else
                                            <span class="text-blue-600 font-medium">Gratis</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge-modern
                                            @if($guideline->is_active) badge-active
                                            @else badge-inactive @endif">
                                            {{ $guideline->is_active ? 'Aktif' : 'Tidak Aktif' }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="action-buttons">
                                            <a href="{{ route('admin.guidelines.show', $guideline) }}" class="btn-modern btn-primary">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                                </svg>
                                                Lihat
                                            </a>
                                            <a href="{{ route('admin.guidelines.edit', $guideline) }}" class="btn-modern btn-warning">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                                </svg>
                                                Edit
                                            </a>
                                            <form action="{{ route('admin.guidelines.destroy', $guideline) }}" method="POST" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus panduan ini?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn-modern btn-danger">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                    </svg>
                                                    Hapus
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="mt-6">
                    {{ $guidelines->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
