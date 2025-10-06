@extends('layouts.app')
@section('content')
<h1>Manajemen Pengajuan</h1>
<table class="min-w-full">
    <thead><tr><th>ID</th><th>User</th><th>Status</th><th>Action</th></tr></thead>
    <tbody>
        @foreach($submissions as $submission)
            <tr>
                <td>{{ $submission->id }}</td>
                <td>{{ $submission->user->name }}</td>
                <td>{{ $submission->status }}</td>
                <td>
                    <form action="{{ route('admin.upload.file', $submission->id) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <input type="file" name="file_data" required>
                        <button type="submit">Upload File</button>
                    </form>
                </td>
            </tr>
        @endforeach
    </tbody>
</table>
@endsection
