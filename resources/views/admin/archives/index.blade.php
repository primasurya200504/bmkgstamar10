@extends('layouts.app')
@section('content')
<h1>Manajemen Pengarsipan</h1>
<table class="min-w-full">
    <thead><tr><th>ID</th><th>Submission</th><th>User</th><th>Files</th></tr></thead>
    <tbody>
        @foreach($archives as $archive)
            <tr>
                <td>{{ $archive->id }}</td>
                <td>{{ $archive->submission->title ?? 'N/A' }}</td>
                <td>{{ $archive->user->name ?? 'N/A' }}</td>
                <td>
                    @foreach($archive->files ?? [] as $file)
                        <a href="{{ Storage::url($file->path) }}" target="_blank">Download</a>
                    @endforeach
                </td>
            </tr>
        @endforeach
    </tbody>
</table>
@endsection
