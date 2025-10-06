<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Submission; // Untuk manajemen pengajuan
use App\Models\Payment;    // Untuk eBilling
use App\Models\Archive;    // Untuk pengarsipan
use App\Models\User;       // Untuk manajemen pengguna

class AdminController extends Controller
{
    public function __construct()
    {
        $this->middleware('admin'); // Pastikan middleware aktif
    }

    // Dashboard utama (1 view saja)
    public function dashboard()
    {
        $submissions = Submission::with('user')->latest()->take(10)->get(); // Contoh data pengajuan
        $payments = Payment::with('user')->where('status', 'pending')->get(); // Pembayaran pending
        $users = User::select('id', 'email', 'phone')->latest()->get(); // List user
        $archives = Archive::with('submission')->latest()->take(5)->get(); // Arsip terbaru

        return view('admin.dashboard', compact('submissions', 'payments', 'users', 'archives'));
    }

    // Manajemen Pengajuan (list & approve/reject)
    public function submissions()
    {
        $submissions = Submission::with('user', 'files')->paginate(10);
        return view('admin.submissions.index', compact('submissions'));
    }

    // Manajemen Upload eBilling (upload & verifikasi)
    public function ebilling()
    {
        $payments = Payment::with('user')->paginate(10);
        return view('admin.payments.index', compact('payments')); // View untuk list & verifikasi
    }

    public function verifyPayment(Request $request, $id)
    {
        $payment = Payment::findOrFail($id);
        $payment->status = 'verified';
        $payment->save();
        return redirect()->back()->with('success', 'Pembayaran diverifikasi!');
    }

    // Manajemen Upload File Data Pengajuan (admin upload ke submission user)
    public function uploadFileData(Request $request, $submissionId)
    {
        $submission = Submission::findOrFail($submissionId);
        // Logic upload file (gunakan Storage::putFile)
        $file = $request->file('file_data');
        $path = $file->store('submission_files', 'public');
        // Simpan ke model SubmissionFile atau Archive
        $submission->files()->create(['path' => $path, 'type' => 'admin_data']);
        return redirect()->back()->with('success', 'File dikirim ke user!');
    }

    // Manajemen Pengarsipan (list semua proses & file)
    public function archives()
    {
        $archives = Archive::with(['submission', 'files', 'user'])->paginate(10);
        return view('admin.archives.index', compact('archives'));
    }

    // Manajemen Pengguna (list email & no HP)
    public function users()
    {
        $users = User::select('id', 'name', 'email', 'phone')->paginate(20);
        return view('admin.users.index', compact('users'));
    }
}
