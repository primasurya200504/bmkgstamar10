@extends('layouts.app')
@section('content')
<h1>Manajemen Pengguna</h1>
<table>
    <thead><tr><th>ID</th><th>Nama</th><th>Email</th><th>No HP</th></tr></thead>
    <tbody>
        @foreach($users as $user)
            <tr><td>{{ $user->id }}</td><td>{{ $user->name }}</td><td>{{ $user->email }}</td><td>{{ $user->phone ?? 'N/A' }}</td></tr>
        @endforeach
    </tbody>
</table>
@endsection
