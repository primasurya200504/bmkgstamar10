@extends('layouts.app')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900">
                <div class="flex justify-between items-center mb-6">
                    <h1 class="text-2xl font-bold text-gray-800">Edit Panduan</h1>
                    <a href="{{ route('admin.guidelines') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                        Kembali
                    </a>
                </div>

                <form action="{{ route('admin.guidelines.update', $guideline) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="title" class="block text-sm font-medium text-gray-700">Judul Panduan</label>
                            <input type="text" name="title" id="title" value="{{ old('title', $guideline->title) }}" required
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                            @error('title')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="type" class="block text-sm font-medium text-gray-700">Tipe</label>
                            <select name="type" id="type" required
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                <option value="">Pilih Tipe</option>
                                <option value="pnbp" {{ old('type', $guideline->type) == 'pnbp' ? 'selected' : '' }}>PNBP</option>
                                <option value="non_pnbp" {{ old('type', $guideline->type) == 'non_pnbp' ? 'selected' : '' }}>Non PNBP</option>
                            </select>
                            @error('type')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="fee" class="block text-sm font-medium text-gray-700">Biaya (Rp)</label>
                            <input type="number" name="fee" id="fee" value="{{ old('fee', $guideline->fee) }}" min="0" step="0.01" required
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                            @error('fee')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Status</label>
                            <div class="mt-2">
                                <label class="inline-flex items-center">
                                    <input type="checkbox" name="is_active" value="1" {{ old('is_active', $guideline->is_active) ? 'checked' : '' }}
                                        class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                                    <span class="ml-2 text-sm text-gray-700">Aktif</span>
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="mt-6">
                        <label for="description" class="block text-sm font-medium text-gray-700">Deskripsi</label>
                        <textarea name="description" id="description" rows="4" required
                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">{{ old('description', $guideline->description) }}</textarea>
                        @error('description')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="mt-6">
                        <label class="block text-sm font-medium text-gray-700">Dokumen yang Diperlukan</label>
                        <div id="documents-container" class="mt-2 space-y-2">
                            @php
                                $documents = old('required_documents', $guideline->required_documents ?? []);
                            @endphp
                            @if(is_array($documents) && count($documents) > 0)
                                @foreach($documents as $index => $document)
                                    <div class="flex items-center space-x-2">
                                        <input type="text" name="required_documents[]" value="{{ $document }}" placeholder="Nama dokumen"
                                            class="flex-1 border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                        <button type="button" onclick="removeDocument(this)" class="text-red-600 hover:text-red-800">Hapus</button>
                                    </div>
                                @endforeach
                            @else
                                <div class="flex items-center space-x-2">
                                    <input type="text" name="required_documents[]" placeholder="Nama dokumen"
                                        class="flex-1 border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                    <button type="button" onclick="removeDocument(this)" class="text-red-600 hover:text-red-800">Hapus</button>
                                </div>
                            @endif
                        </div>
                        <button type="button" onclick="addDocument()" class="mt-2 bg-green-500 hover:bg-green-700 text-white font-bold py-1 px-3 rounded text-sm">
                            Tambah Dokumen
                        </button>
                    </div>

                    <div class="mt-6 flex justify-end">
                        <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                            Update Panduan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function addDocument() {
    const container = document.getElementById('documents-container');
    const div = document.createElement('div');
    div.className = 'flex items-center space-x-2';
    div.innerHTML = `
        <input type="text" name="required_documents[]" placeholder="Nama dokumen" class="flex-1 border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
        <button type="button" onclick="removeDocument(this)" class="text-red-600 hover:text-red-800">Hapus</button>
    `;
    container.appendChild(div);
}

function removeDocument(button) {
    button.parentElement.remove();
}
</script>
@endsection
