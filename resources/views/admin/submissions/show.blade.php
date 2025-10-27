@extends('layouts.app')

@section('content')
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="flex justify-between items-center mb-6">
                        <h1 class="text-2xl font-bold text-gray-800">Detail Pengajuan #{{ $submission->id }}</h1>
                        <div class="flex space-x-2">
                            <a href="{{ route('admin.submissions') }}"
                                class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                                Kembali
                            </a>
                        </div>
                    </div>

                    <!-- Submission Status -->
                    <div class="mb-6">
                        <span
                            class="px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full
                        @if ($submission->status == 'pending') bg-yellow-100 text-yellow-800
                        @elseif($submission->status == 'verified') bg-green-100 text-green-800
                        @elseif($submission->status == 'rejected') bg-red-100 text-red-800
                        @elseif($submission->status == 'Diproses') bg-blue-100 text-blue-800
                        @elseif($submission->status == 'completed') bg-green-100 text-green-800
                        @else bg-gray-100 text-gray-800 @endif">
                            Status: {{ $submission->status }}
                        </span>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                        <!-- User Information -->
                        <div>
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Informasi Pengaju</h3>
                            <dl class="space-y-3">
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Nama</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ $submission->user->name ?? 'N/A' }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Email</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ $submission->user->email ?? 'N/A' }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">No HP</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ $submission->user->phone ?? 'N/A' }}</dd>
                                </div>
                            </dl>
                        </div>

                        <!-- Submission Information -->
                        <div>
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Informasi Pengajuan</h3>
                            <dl class="space-y-3">
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Panduan</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ $submission->guideline->title ?? 'N/A' }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Tipe</dt>
                                    <dd class="mt-1 text-sm text-gray-900">
                                        {{ $submission->guideline ? strtoupper($submission->guideline->type) : 'N/A' }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Biaya</dt>
                                    <dd class="mt-1 text-sm text-gray-900">
                                        @if ($submission->guideline && $submission->guideline->fee > 0)
                                            Rp {{ number_format($submission->guideline->fee, 0, ',', '.') }}
                                        @else
                                            Gratis
                                        @endif
                                    </dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Tanggal Pengajuan</dt>
                                    <dd class="mt-1 text-sm text-gray-900">
                                        {{ $submission->created_at->format('d/m/Y H:i') }}</dd>
                                </div>
                            </dl>
                        </div>
                    </div>

                    <!-- Purpose and Description -->
                    <div class="mb-8">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Tujuan Pengajuan</h3>
                        <p class="text-sm text-gray-700 mb-4">{{ $submission->purpose }}</p>

                        @if ($submission->description)
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Deskripsi Tambahan</h3>
                            <p class="text-sm text-gray-700">{{ $submission->description }}</p>
                        @endif
                    </div>

                    <!-- Uploaded Files -->
                    <div class="mb-8">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">File yang Diupload</h3>
                        @php
                            $files = $submission->files ?? collect();
                            $documents = [];
                            if ($submission->documents) {
                                if (is_string($submission->documents)) {
                                    $documents = json_decode($submission->documents, true) ?: [];
                                } elseif (is_array($submission->documents)) {
                                    $documents = $submission->documents;
                                }
                            }
                            $hasFiles = $files->count() > 0 || count($documents) > 0;
                        @endphp

                        @if ($hasFiles)
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                                @foreach ($files as $file)
                                    <div class="border border-gray-200 rounded-lg p-4">
                                        <p class="text-sm font-medium text-gray-900 break-words">
                                            {{ $file->file_name ?? basename($file->file_path) }}</p>
                                        <p class="text-xs text-gray-500">{{ $file->created_at->format('d/m/Y H:i') }}</p>
                                        @if ($file->file_size)
                                            <p class="text-xs text-gray-400">{{ $file->fileSizeHuman }}</p>
                                        @endif
                                        <a href="#"
                                            onclick="downloadFile({{ $submission->id }}, {{ $file->id }}); return false;"
                                            class="bg-blue-500 hover:bg-blue-700 text-white text-xs font-bold py-1 px-2 rounded mt-2 inline-block">
                                            Download
                                        </a>
                                    </div>
                                @endforeach

                                @foreach ($documents as $doc)
                                    <div class="border border-gray-200 rounded-lg p-4">
                                        <p class="text-sm font-medium text-gray-900 break-words">
                                            {{ $doc['original_name'] ?? ($doc['stored_name'] ?? 'Document') }}</p>
                                        <p class="text-xs text-gray-500">
                                            {{ isset($doc['uploaded_at']) ? \Carbon\Carbon::parse($doc['uploaded_at'])->format('d/m/Y H:i') : 'N/A' }}
                                        </p>
                                        @if (isset($doc['size']))
                                            <p class="text-xs text-gray-400">{{ number_format($doc['size'] / 1024, 1) }} KB
                                            </p>
                                        @endif
                                        @if (isset($doc['path']))
                                            <a href="{{ Storage::url($doc['path']) }}" target="_blank"
                                                class="bg-blue-500 hover:bg-blue-700 text-white text-xs font-bold py-1 px-2 rounded mt-2 inline-block">
                                                Download
                                            </a>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <p class="text-sm text-gray-500">Belum ada file yang diupload</p>
                        @endif
                    </div>

                    <!-- Rejection Reason (if rejected) -->
                    @if ($submission->status == 'rejected' && $submission->rejection_note)
                        <div class="mb-8">
                            <h3 class="text-lg font-medium text-red-900 mb-4">Alasan Penolakan</h3>
                            <div class="bg-red-50 border border-red-200 rounded-lg p-4">
                                <p class="text-sm text-red-800">{{ $submission->rejection_note }}</p>
                            </div>
                        </div>
                    @endif

                    <!-- Action Buttons (only for pending submissions) -->
                    @if ($submission->status == 'pending')
                        <div class="flex space-x-4">
                            <!-- Verify Button -->
                            <form action="{{ route('admin.submissions.verify', $submission) }}" method="POST"
                                class="inline">
                                @csrf
                                <button type="submit"
                                    class="bg-purple-500 hover:bg-purple-700 text-white font-bold py-2 px-4 rounded">
                                    Verifikasi
                                </button>
                            </form>

                            <!-- Process Button -->
                            <form action="{{ route('admin.submissions.approve', $submission) }}" method="POST"
                                class="inline">
                                @csrf
                                <button type="submit"
                                    class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                    Proses
                                </button>
                            </form>

                            <!-- Reject Button -->
                            <button onclick="openRejectModal()"
                                class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded">
                                Tolak
                            </button>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Reject Modal -->
    <div id="rejectModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="mt-3">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Tolak Pengajuan</h3>
                <form action="{{ route('admin.submissions.reject', $submission) }}" method="POST">
                    @csrf
                    <div class="mb-4">
                        <label for="reason" class="block text-sm font-medium text-gray-700 mb-2">Alasan Penolakan</label>
                        <textarea id="reason" name="reason" rows="4"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-red-500"
                            required></textarea>
                    </div>
                    <div class="flex justify-end space-x-2">
                        <button type="button" onclick="closeRejectModal()"
                            class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
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
        // Download file function
        function downloadFile(submissionId, fileId) {
            const link = document.createElement('a');
            link.href = `/admin/submissions/${submissionId}/files/${fileId}/download`;
            link.download = ''; // This will use the filename from the server
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
        }

        function openRejectModal() {
            document.getElementById('rejectModal').classList.remove('hidden');
        }

        function closeRejectModal() {
            document.getElementById('rejectModal').classList.add('hidden');
        }
    </script>
@endsection
