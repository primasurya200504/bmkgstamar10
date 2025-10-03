@extends('layouts.admin')

@section('content')
    <h1>Kirim E-Billing</h1>
    <form action="{{ route('admin.payments.store') }}" method="post" enctype="multipart/form-data">
        @csrf
        <label>Pilih User:</label>
        <select name="user_id" required>
            @foreach ($users as $user)
                <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->email }})</option>
            @endforeach
        </select>
        <br><br>
        <label>Upload E-Billing (PDF/JPG/PNG):</label>
        <input type="file" name="ebilling_file" required>
        <br><br>
        <button type="submit">Kirim</button>
    </form>
@endsection
