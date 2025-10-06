@extends('layouts.app')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900">
                <div class="flex justify-between items-center mb-6">
                    <div>
                        <h1 class="text-2xl font-bold">Upload Data Pengajuan</h1>
                        <p class="text-gray-600">Pengajuan: {{ $submission->submission_number ?? $submission->id }} - {{ $submission->user->name }}</p>
                    </div>
                    <div>
                        <a href="{{ route('admin.data-uploads.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded mr-2">
                            Kembali
                        </a>
                        @if($submission->generatedDocuments->count() > 0)
                            <form action="{{ route('admin.data-uploads.complete', $submission->id) }}" method="POST" class="inline">
                                @csrf
                                @method('POST')
                                <button type="submit" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded"
                                        onclick="return confirm('Apakah Anda yakin ingin menyelesaikan upload data? Pengajuan akan dipindahkan ke arsip.')">
                                    Selesai & Arsipkan
                                </button>
                            </form>
                        @endif
                    </div>
                </div>

                @if(session('success'))
                    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                        {{ session('success') }}
                    </div>
                @endif

                @if(session('error'))
                    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                        {{ session('error') }}
                    </div>
                @endif

                <!-- Submission Details -->
                <div class="bg-gray-50 p-4 rounded-lg mb-6">
                    <h3 class="text-lg font-semibold mb-3">Detail Pengajuan</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <strong>Pengguna:</strong> {{ $submission->user->name }} ({{ $submission->user->email }})
                        </div>
                        <div>
                            <strong>Jenis Pengajuan:</strong> {{ $submission->guideline->title ?? 'N/A' }}
                        </div>
                        <div>
                            <strong>Tanggal Pengajuan:</strong> {{ $submission->created_at->format('d/m/Y H:i') }}
                        </div>
                        <div>
                            <strong>Status:</strong>
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                @if($submission->status == 'Diterima') bg-green-100 text-green-800
                                @elseif($submission->status == 'Diproses') bg-yellow-100 text-yellow-800
                                @elseif($submission->status == 'Selesai') bg-blue-100 text-blue-800
                                @else bg-gray-100 text-gray-800 @endif">
                                {{ $submission->status }}
                            </span>
                        </div>
                        <div>
                            <strong>Pembayaran:</strong>
                            @if($submission->payment)
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                    @if($submission->payment->status == 'verified') bg-green-100 text-green-800
                                    @else bg-yellow-100 text-yellow-800 @endif">
                                    {{ $submission->payment->status == 'verified' ? 'Terverifikasi' : 'Pending' }}
                                </span>
                            @else
                                Tidak ada
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Upload Document Form -->
                <div class="bg-blue-50 p-4 rounded-lg mb-6">
                    <h3 class="text-lg font-semibold mb-3">Upload Dokumen Baru</h3>
                    <form action="{{ route('admin.data-uploads.upload', $submission->id) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div>
                                <label for="document_name" class="block text-sm font-medium text-gray-700">Nama Dokumen</label>
                                <input type="text" name="document_name" id="document_name" required
                                       class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                            </div>
                            <div>
                                <label for="document_type" class="block text-sm font-medium text-gray-700">Jenis Dokumen</label>
                                <select name="document_type" id="document_type" required
                                        class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                                    <option value="">Pilih jenis dokumen</option>
                                    <option value="Surat Keputusan">Surat Keputusan</option>
                                    <option value="Dokumen Teknis">Dokumen Teknis</option>
                                    <option value="Laporan">Laporan</option>
                                    <option value="Sertifikat">Sertifikat</option>
                                    <option value="Data Pendukung">Data Pendukung</option>
                                    <option value="Lainnya">Lainnya</option>
                                </select>
                            </div>
                            <div>
                                <label for="document_file" class="block text-sm font-medium text-gray-700">File Dokumen</label>
                                <input type="file" name="document_file" id="document_file" required accept=".pdf,.doc,.docx,.xls,.xlsx,.jpg,.jpeg,.png"
                                       class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                                <p class="mt-1 text-sm text-gray-500">Maksimal 10MB. Format: PDF, DOC, DOCX, XLS, XLSX, JPG, JPEG, PNG</p>
                            </div>
                        </div>
                        <div class="mt-4">
                            <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                Upload Dokumen
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Uploaded Documents -->
                <div class="bg-white border border-gray-200 rounded-lg">
                    <div class="px-4 py-3 border-b border-gray-200">
                        <h3 class="text-lg font-semibold">Dokumen yang Telah Diupload ({{ $submission->generatedDocuments->count() }})</h3>
                    </div>
                    <div class="p-4">
                        @if($submission->generatedDocuments->count() > 0)
                            <div class="space-y-3">
                                @foreach($submission->generatedDocuments as $document)
                                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                        <div class="flex-1">
                                            <h4 class="font-medium">{{ $document->document_name }}</h4>
                                            <p class="text-sm text-gray-600">{{ $document->document_type }} • {{ number_format($document->file_size / 1024, 1) }} KB • Diupload: {{ $document->created_at->format('d/m/Y H:i') }}</p>
                                        </div>
                                        <div class="flex space-x-2">
                                            <a href="{{ route('admin.data-uploads.download', $document->id) }}"
                                               class="bg-green-500 hover:bg-green-700 text-white px-3 py-1 rounded text-sm">
                                                Download
                                            </a>
                                            <form action="{{ route('admin.data-uploads.delete', $document->id) }}" method="POST" class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="bg-red-500 hover:bg-red-700 text-white px-3 py-1 rounded text-sm"
                                                        onclick="return confirm('Apakah Anda yakin ingin menghapus dokumen ini?')">
                                                    Hapus
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <p class="text-gray-500 text-center py-8">Belum ada dokumen yang diupload untuk pengajuan ini.</p>
                        @endif
                    </div>
                </div>

                <!-- User Uploaded Files -->
                @if($submission->files->count() > 0)
                    <div class="bg-white border border-gray-200 rounded-lg mt-6">
                        <div class="px-4 py-3 border-b border-gray-200">
                            <h3 class="text-lg font-semibold">File yang Diupload User ({{ $submission->files->count() }})</h3>
                        </div>
                        <div class="p-4">
                            <div class="space-y-3">
                                @foreach($submission->files as $file)
                                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                        <div class="flex-1">
                                            <h4 class="font-medium">{{ $file->document_name ?? $file->file_name }}</h4>
                                            <p class="text-sm text-gray-600">{{ $file->file_type }} • {{ number_format($file->file_size / 1024, 1) }} KB</p>
                                        </div>
                                        <div class="flex space-x-2">
                                            <a href="{{ Storage::url($file->file_path) }}" target="_blank"
                                               class="bg-blue-500 hover:bg-blue-700 text-white px-3 py-1 rounded text-sm">
                                                Lihat
                                            </a>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
