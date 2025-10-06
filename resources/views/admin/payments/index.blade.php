@extends('layouts.app')
@section('content')
<h1>Manajemen Upload eBilling</h1>
<table class="min-w-full">
    <thead><tr><th>ID</th><th>User</th><th>Status</th><th>Action</th></tr></thead>
    <tbody>
        @foreach($payments as $payment)
            <tr>
                <td>{{ $payment->id }}</td>
                <td>{{ $payment->user->name ?? 'N/A' }}</td>
                <td>{{ $payment->status }}</td>
                <td>
                    <form action="{{ route('admin.verify.payment', $payment->id) }}" method="POST">
                        @csrf
                        <button type="submit">Verifikasi</button>
                    </form>
                </td>
            </tr>
        @endforeach
    </tbody>
</table>
@endsection
