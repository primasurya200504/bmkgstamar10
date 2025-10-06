@extends('layouts.app')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900">
                <!-- Konten Dashboard Admin (contoh: stats & recent data) -->
                <h1 class="text-2xl font-bold">Dashboard Admin</h1>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-6">
                    <div class="bg-blue-100 p-4 rounded">
                        <h3>Pengajuan Baru</h3>
                        <p class="text-2xl">{{ $submissions->count() }}</p>
                    </div>
                    <div class="bg-green-100 p-4 rounded">
                        <h3>Pembayaran Pending</h3>
                        <p class="text-2xl">{{ $payments->count() }}</p>
                    </div>
                    <div class="bg-yellow-100 p-4 rounded">
                        <h3>Total User</h3>
                        <p class="text-2xl">{{ $users->count() }}</p>
                    </div>
                </div>

                <!-- Recent Submissions -->
                <h2 class="mt-8">Pengajuan Terbaru</h2>
                <table class="min-w-full mt-4">
                    <thead><tr><th>ID</th><th>User</th><th>Status</th><th>Action</th></tr></thead>
                    <tbody>
                        @foreach($submissions as $submission)
                            <tr>
                                <td>{{ $submission->id }}</td>
                                <td>{{ $submission->user->name }}</td>
                                <td>{{ $submission->status }}</td>
                                <td><a href="{{ route('admin.submissions') }}" class="text-blue-500">Lihat</a></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                <!-- Recent Archives -->
                <h2 class="mt-8">Arsip Terbaru</h2>
                <ul>
                    @foreach($archives as $archive)
                        <li>{{ $archive->submission->title ?? 'N/A' }} - {{ $archive->created_at }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection
