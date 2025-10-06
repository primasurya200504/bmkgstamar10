<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use App\Models\Submission; // Untuk manajemen pengajuan
use App\Models\Payment;    // Untuk eBilling
use App\Models\Archive;    // Untuk pengarsipan
use App\Models\User;       // Untuk manajemen pengguna

class AdminController extends Controller
{
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

    // Show submission detail
    public function showSubmission(Submission $submission)
    {
        $submission->load('user', 'files', 'guideline');
        return view('admin.submissions.show', compact('submission'));
    }

    // Approve submission
    public function approveSubmission(Request $request, Submission $submission)
    {
        $submission->update(['status' => 'Diterima']);

        // Create payment record for e-billing if guideline has fee
        if ($submission->guideline && $submission->guideline->fee > 0) {
            Payment::create([
                'submission_id' => $submission->id,
                'amount' => $submission->guideline->fee,
                'status' => 'pending',
            ]);
        }

        return redirect()->route('admin.submissions')->with('success', 'Pengajuan berhasil diverifikasi!');
    }

    // Reject submission
    public function rejectSubmission(Request $request, Submission $submission)
    {
        $request->validate([
            'reason' => 'required|string|max:500'
        ]);

        $submission->update([
            'status' => 'Ditolak',
            'rejection_note' => $request->reason
        ]);

        // Send email notification to user (if mail is configured)
        // Mail::to($submission->user->email)->send(new SubmissionRejected($submission));

        return redirect()->route('admin.submissions')->with('success', 'Pengajuan berhasil ditolak!');
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
        $payment->verified_at = now();
        $payment->verified_by = Auth::id();
        $payment->save();

        // Update submission status to indicate payment is verified and ready for document upload
        if ($payment->submission) {
            $payment->submission->update(['status' => 'Diterima']);
        }

        return redirect()->route('admin.data-uploads.show', $payment->submission_id)->with('success', 'Pembayaran diverifikasi! Silakan upload data untuk pengajuan ini.');
    }

    // Manajemen Upload File Data Pengajuan (admin upload ke submission user)
    public function uploadFileData(Request $request, $submissionId)
    {
        $submission = Submission::findOrFail($submissionId);
        // Logic upload file (gunakan Storage::putFile)
        $file = $request->file('file_data');
        $path = $file->store('submission_files', 'public');
        // Simpan ke model SubmissionFile atau Archive
        $submission->files()->create([
            'file_name' => $file->getClientOriginalName(),
            'file_path' => $path,
            'file_type' => $file->getMimeType(),
            'file_size' => $file->getSize(),
            'document_name' => 'File dari Admin'
        ]);
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
