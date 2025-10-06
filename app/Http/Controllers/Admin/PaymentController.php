<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PaymentController extends Controller
{
    public function index()
    {
        $payments = Payment::with('user')->latest()->get();
        return view('admin.payments.index', compact('payments'));
    }

    public function create()
    {
        $users = User::all();
        return view('admin.payments.create', compact('users'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'ebilling_file' => 'required|file|mimes:pdf,jpg,png',
        ]);

        $path = $request->file('ebilling_file')->store('ebillings');

        Payment::create([
            'user_id' => $request->user_id,
            'ebilling_file' => $path,
            'status' => 'pending',
        ]);

        // ✅ redirect konsisten ke admin.payments.index
        return redirect()->route('admin.payments.index')
            ->with('success', 'E-Billing berhasil dikirim ke user.');
    }

    public function verify($id)
    {
        $payment = Payment::findOrFail($id);
        $payment->update(['status' => 'verified']);

        return back()->with('success', 'Pembayaran user sudah diverifikasi.');
    }

    // Method baru: Menampilkan halaman uploads (daftar file yang diupload, misal proof dari payments)
    public function uploads()
    {
        // Ambil daftar payments yang memiliki proof upload
        $uploads = Payment::with('user')
            ->whereNotNull('proof')  // Asumsi field 'proof' menyimpan path file upload
            ->latest()
            ->get();

        return view('admin.uploads', compact('uploads'));
    }

    // Method baru: Handle aksi upload file baru
    public function storeUpload(Request $request)
    {
        // Validasi input
        $request->validate([
            'user_id' => 'required|exists:users,id',  // Opsional: jika upload terkait user
            'file' => 'required|file|mimes:pdf,jpg,png|max:2048',  // Sesuaikan tipe dan ukuran
        ]);

        // Simpan file ke storage (folder 'uploads')
        $path = $request->file('file')->store('uploads', 'public');

        // Simpan entri ke DB (integrasi dengan model Payment)
        Payment::create([
            'user_id' => $request->user_id,  // Opsional, sesuaikan jika tidak perlu
            'proof' => $path,  // Asumsi field 'proof' di model Payment untuk simpan path
            'status' => 'pending',  // Status awal
            // Tambahkan field lain jika perlu, seperti ebilling_file
        ]);

        return redirect()->route('admin.uploads')
            ->with('success', 'File berhasil diupload dan disimpan.');
    }
}
