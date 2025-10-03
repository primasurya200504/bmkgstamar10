@extends('layouts.admin')

@section('content')
    <h1>Manajemen Pembayaran</h1>
    <a href="{{ route('admin.payments.create') }}">+ Buat E-Billing</a>

    <table border="1" cellpadding="6">
        <tr>
            <th>User</th>
            <th>E-Billing</th>
            <th>Bukti</th>
            <th>Status</th>
            <th>Aksi</th>
        </tr>
        @foreach ($payments as $p)
            <tr>
                <td>{{ $p->user->name }}</td>
                <td><a href="{{ Storage::url($p->ebilling_file) }}" target="_blank">Lihat</a></td>
                <td>
                    @if ($p->proof_file)
                        <a href="{{ Storage::url($p->proof_file) }}" target="_blank">Lihat</a>
                    @else
                        Belum upload
                    @endif
                </td>
                <td>{{ ucfirst($p->status) }}</td>
                <td>
                    @if ($p->status == 'uploaded')
                        <form action="{{ route('admin.payments.verify', $p->id) }}" method="post">
                            @csrf
                            <button type="submit">Verifikasi</button>
                        </form>
                    @endif
                </td>
            </tr>
        @endforeach
    </table>
@endsection
