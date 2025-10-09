<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Response;
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
        $users = User::select('id', 'email', 'phone_number')->latest()->get();
        $archives = Archive::with('submission')->latest()->take(5)->get(); // Arsip terbaru

        return view('admin.dashboard', compact('submissions', 'payments', 'users', 'archives'));
    }

    // Manajemen Pengajuan (list & approve/reject)
    public function submissions()
    {
        $submissions = Submission::with('user', 'files')
            ->whereNotIn('status', ['Selesai', 'Diproses'])
            ->whereDoesntHave('archives')
            ->paginate(10);
        return view('admin.submissions.index', compact('submissions'));
    }

    // Show submission detail
    public function showSubmission(Submission $submission)
    {
        $submission->load('user', 'files', 'guideline');
        return view('admin.submissions.show', compact('submission'));
    }

    // Verify submission - move to payments management
    public function verifySubmission(Request $request, Submission $submission)
    {
        $submission->update(['status' => 'Diproses']);
        return redirect()->route('admin.ebilling')->with('success', 'Pengajuan berhasil diverifikasi! Pengajuan telah dipindahkan ke kelola pembayaran.');
    }

    // Approve submission - all submissions go to e-billing upload
    public function approveSubmission(Request $request, Submission $submission)
    {
        $submission->update(['status' => 'Diproses']);
        return redirect()->route('admin.ebilling.upload')->with('success', 'Pengajuan berhasil diproses! Silakan upload dokumen yang diperlukan untuk pengajuan ini.');
    }

    // Reject submission
    public function rejectSubmission(Request $request, Submission $submission)
    {
        $request->validate([
            'reason' => 'required|string|max:500'
        ]);

        $submission->update([
            'status' => 'rejected',
            'rejection_note' => $request->reason
        ]);

        // Send email notification to user (if mail is configured)
        // Mail::to($submission->user->email)->send(new SubmissionRejected($submission));

        return redirect()->route('admin.submissions')->with('success', 'Pengajuan berhasil ditolak!');
    }

    // eBilling management - show PNBP submissions ready for e-billing upload
    public function ebilling()
    {
        $pnlpSubmissions = Submission::with('user', 'guideline')
            ->where('status', 'Diproses')
            ->whereHas('guideline', function($query) {
                $query->where('fee', '>', 0);
            })
            ->paginate(10);

        $payments = Payment::with('submission.user', 'submission.guideline')
            ->whereIn('status', ['pending', 'proof_uploaded'])
            ->paginate(10);

        return view('admin.payments.index', compact('pnlpSubmissions', 'payments'));
    }

    // Upload e-Billing/Document page - for all processed submissions
    public function uploadEBillingPage()
    {
        $submissions = Submission::with('user', 'guideline', 'payment')
            ->where('status', 'Diproses')
            ->paginate(10);

        return view('admin.ebilling', compact('submissions'));
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
            $payment->submission->update(['status' => 'verified']);
        }

        return redirect()->route('admin.data-uploads.show', $payment->submission_id)->with('success', 'Pembayaran diverifikasi! Pengajuan telah dipindahkan ke upload data.');
    }

    // Upload e-billing/document for a submission
    public function uploadEBilling(Request $request, Submission $submission)
    {
        $request->validate([
            'e_billing_file' => 'required|file|mimes:pdf,jpg,jpeg,png|max:5120'
        ]);

        $file = $request->file('e_billing_file');
        $path = $file->store('e_billing', 'public');

        if ($submission->guideline && $submission->guideline->fee > 0) {
            // PNBP submission - create payment record with e-billing
            $payment = Payment::updateOrCreate(
                ['submission_id' => $submission->id],
                [
                    'amount' => $submission->guideline->fee,
                    'status' => 'pending',
                    'e_billing_path' => $path,
                    'e_billing_filename' => $file->getClientOriginalName(),
                ]
            );

            // Update submission status to payment_pending
            $submission->update(['status' => 'payment_pending']);

            return redirect()->back()->with('success', 'e-Billing berhasil diupload! User dapat melakukan pembayaran.');
        } else {
            // Non-PNBP submission - just update status to allow data upload
            $submission->update(['status' => 'verified']);

            // Store the uploaded document as a regular file
            $submission->files()->create([
                'file_name' => $file->getClientOriginalName(),
                'file_path' => $path,
                'file_type' => $file->getMimeType(),
                'file_size' => $file->getSize(),
                'document_name' => 'Dokumen Verifikasi Admin'
            ]);

            return redirect()->route('admin.data-uploads.show', $submission->id)->with('success', 'Dokumen berhasil diupload! Pengajuan telah dipindahkan ke upload data.');
        }
    }

    // Reject payment
    public function rejectPayment(Request $request, $id)
    {
        $payment = Payment::findOrFail($id);

        $request->validate([
            'reject_reason' => 'required|string|max:500'
        ]);

        $payment->update([
            'status' => 'pending',
            'rejection_reason' => $request->reject_reason
        ]);

        // Don't update submission status, so user can upload new proof directly

        return redirect()->back()->with('success', 'Pembayaran berhasil ditolak! User dapat mengupload ulang bukti pembayaran.');
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

    // Show archive detail
    public function showArchive($id)
    {
        // First try to find an actual Archive record
        $archive = Archive::with(['submission.user', 'submission.files', 'submission.generatedDocuments', 'submission.histories', 'submission.payment'])->find($id);

        if ($archive) {
            // It's an actual Archive record
            $data = [
                'id' => $archive->id,
                'submission' => $archive->submission->load('guideline', 'files', 'generatedDocuments', 'histories', 'payment'),
                'archive_date' => $archive->archive_date,
                'notes' => $archive->notes,
                'created_at' => $archive->created_at,
            ];
        } else {
            // Try to find a completed submission with this ID
            $submission = Submission::with(['user', 'guideline', 'files', 'generatedDocuments', 'histories', 'payment'])
                ->where('status', 'completed')
                ->find($id);

            if (!$submission) {
                abort(404, 'Archive not found');
            }

            // Create pseudo-archive data for completed submission
            $data = [
                'id' => $submission->id,
                'submission' => $submission,
                'archive_date' => $submission->updated_at,
                'notes' => 'Pengajuan selesai diproses dan diarsipkan',
                'created_at' => $submission->created_at,
            ];
        }

        return response()->json([
            'success' => true,
            'data' => $data
        ]);
    }

    // Manajemen Pengarsipan (list semua proses & file)
    public function archives()
    {
        $year = request('year');
        $month = request('month');

        // Get all completed submissions (status 'completed')
        $completedSubmissionsQuery = Submission::with(['user', 'generatedDocuments'])
            ->where('status', 'completed');

        // Apply filters to completed submissions
        if ($year) {
            $completedSubmissionsQuery->whereYear('updated_at', $year);
            if ($month) {
                $completedSubmissionsQuery->whereMonth('updated_at', $month);
            }
        }

        $completedSubmissions = $completedSubmissionsQuery->get()
            ->map(function ($submission) {
                // Create a pseudo-archive object for completed submissions
                return (object) [
                    'id' => $submission->id,
                    'submission' => $submission,
                    'user' => $submission->user,
                    'archive_date' => $submission->updated_at,
                    'created_at' => $submission->created_at,
                    'is_archive' => false // Flag to distinguish from actual Archive records
                ];
            });

        // Get actual Archive records
        $archiveRecordsQuery = Archive::with(['submission.generatedDocuments', 'user']);

        // Apply filters to archive records
        if ($year) {
            $archiveRecordsQuery->whereYear('created_at', $year);
            if ($month) {
                $archiveRecordsQuery->whereMonth('created_at', $month);
            }
        }

        $archiveRecords = $archiveRecordsQuery->get()
            ->map(function ($archive) {
                $archive->is_archive = true; // Flag for actual Archive records
                return $archive;
            });

        // Combine and sort by creation date (newest first)
        $allArchives = $completedSubmissions->concat($archiveRecords)
            ->sortByDesc('created_at')
            ->values();

        // Paginate the combined collection
        $perPage = 10;
        $currentPage = request()->get('page', 1);
        $offset = ($currentPage - 1) * $perPage;
        $archives = new \Illuminate\Pagination\LengthAwarePaginator(
            $allArchives->slice($offset, $perPage),
            $allArchives->count(),
            $perPage,
            $currentPage,
            ['path' => request()->url(), 'pageName' => 'page']
        );

        return view('admin.archives.index', compact('archives'));
    }

    // Manajemen Pengguna (list email & no HP)
    public function users()
    {
        $users = User::select('id', 'name', 'email', 'phone_number', 'role', 'created_at')->paginate(20);
        return view('admin.users.index', compact('users'));
    }

    // Create user form
    public function create()
    {
        return view('admin.users.create');
    }

    // Store new user
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|in:admin,user',
            'phone_number' => 'nullable|string|max:20',
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'role' => $request->role,
            'phone_number' => $request->phone_number,
        ]);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Pengguna berhasil ditambahkan!'
            ]);
        }

        return redirect()->route('admin.users')->with('success', 'Pengguna berhasil ditambahkan!');
    }

    // Edit user form
    public function edit(User $user)
    {
        return view('admin.users.edit', compact('user'));
    }

    // Update user
    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:8|confirmed',
            'role' => 'required|in:admin,user',
            'phone_number' => 'nullable|string|max:20',
        ]);

        $updateData = [
            'name' => $request->name,
            'email' => $request->email,
            'role' => $request->role,
            'phone_number' => $request->phone_number,
        ];

        if ($request->filled('password')) {
            $updateData['password'] = bcrypt($request->password);
        }

        $user->update($updateData);

        return redirect()->route('admin.users')->with('success', 'Pengguna berhasil diperbarui!');
    }

    // Delete user
    public function destroy(User $user)
    {
        // Prevent deleting admin users or users with submissions
        if ($user->role === 'admin') {
            return redirect()->route('admin.users')->with('error', 'Tidak dapat menghapus pengguna admin!');
        }

        if ($user->submissions()->count() > 0) {
            return redirect()->route('admin.users')->with('error', 'Tidak dapat menghapus pengguna yang memiliki pengajuan!');
        }

        $user->delete();

        return redirect()->route('admin.users')->with('success', 'Pengguna berhasil dihapus!');
    }

    // Download uploaded file by admin
    public function downloadUploadedFile($submissionId, $fileId)
    {
        try {
            // Get submission
            $submission = Submission::findOrFail($submissionId);

            // Get the specific file from the submission
            $file = $submission->files()->where('id', $fileId)->firstOrFail();

            $filePath = storage_path('app/public/' . $file->file_path);

            if (!Storage::disk('public')->exists($file->file_path)) {
                return response()->json([
                    'success' => false,
                    'message' => 'File tidak ditemukan'
                ], 404);
            }

            return response()->download($filePath, $file->file_name, [
                'Content-Type' => $file->file_type ?: mime_content_type($filePath),
                'Content-Disposition' => 'attachment; filename="' . $file->file_name . '"'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mendownload file'
            ], 500);
        }
    }

    // Download e-billing file
    public function downloadEBilling($paymentId)
    {
        try {
            $payment = Payment::findOrFail($paymentId);

            if (!$payment->e_billing_path || !Storage::disk('public')->exists($payment->e_billing_path)) {
                return response()->json([
                    'success' => false,
                    'message' => 'File e-Billing tidak ditemukan'
                ], 404);
            }

            $filePath = storage_path('app/public/' . $payment->e_billing_path);
            $fileName = $payment->e_billing_filename ?: basename($payment->e_billing_path);

            return response()->download($filePath, $fileName, [
                'Content-Type' => mime_content_type($filePath),
                'Content-Disposition' => 'attachment; filename="' . $fileName . '"'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mendownload file e-Billing'
            ], 500);
        }
    }

    // Download payment proof file
    public function downloadPaymentProof($paymentId)
    {
        try {
            $payment = Payment::findOrFail($paymentId);

            if (!$payment->payment_proof || !Storage::disk('public')->exists($payment->payment_proof)) {
                return response()->json([
                    'success' => false,
                    'message' => 'File bukti pembayaran tidak ditemukan'
                ], 404);
            }

            $filePath = storage_path('app/public/' . $payment->payment_proof);
            $fileName = basename($payment->payment_proof);

            return response()->download($filePath, $fileName, [
                'Content-Type' => mime_content_type($filePath),
                'Content-Disposition' => 'attachment; filename="' . $fileName . '"'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mendownload file bukti pembayaran'
            ], 500);
        }
    }
}
