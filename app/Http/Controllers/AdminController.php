<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
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
        $submissions = Submission::with('user', 'files')
            ->whereNotIn('status', ['payment_pending', 'proof_uploaded', 'paid', 'verified', 'processing', 'completed', 'Selesai'])
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
            'status' => 'Ditolak',
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
            ->whereHas('guideline', function ($query) {
                $query->where('fee', '>', 0);
            })
            ->paginate(10);

        $payments = Payment::with('submission.user', 'submission.guideline')
            ->whereIn('status', ['pending', 'uploaded', 'proof_uploaded'])
            ->paginate(10);

        $proofUploadedSubmissions = Submission::with('user', 'guideline', 'payment')
            ->where('status', 'proof_uploaded')
            ->whereHas('guideline', function ($query) {
                $query->where('fee', '>', 0);
            })
            ->paginate(10);

        return view('admin.payments.index', compact('pnlpSubmissions', 'payments', 'proofUploadedSubmissions'));
    }

    // Upload e-Billing/Document page - for all processed submissions
    public function uploadEBillingPage()
    {
        $submissions = Submission::with('user', 'guideline')
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
            'status' => 'rejected',
            'rejection_reason' => $request->reject_reason
        ]);

        // Update submission status back to proof_uploaded so user can see rejection reason and upload new proof
        if ($payment->submission) {
            $payment->submission->update(['status' => 'proof_uploaded']);
        }

        return redirect()->back()->with('success', 'Pembayaran berhasil ditolak!');
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
    public function archives(Request $request)
    {
        // Get actual Archive records only (avoid duplicates with completed submissions)
        $archiveQuery = Archive::with(['submission.generatedDocuments', 'submission.user', 'submission.guideline', 'submission.payment', 'submission.files', 'user']);

        // Apply filters
        $search = $request->get('search');
        $year = $request->get('year');
        $month = $request->get('month');
        $category = $request->get('category');

        if ($search) {
            $archiveQuery->where(function ($query) use ($search) {
                $query->whereHas('submission', function ($q) use ($search) {
                    $q->where('submission_number', 'like', '%' . $search . '%');
                })->orWhereHas('submission.user', function ($q) use ($search) {
                    $q->where('name', 'like', '%' . $search . '%');
                });
            });
        }

        if ($year) {
            $archiveQuery->whereYear('created_at', $year);
        }

        if ($month) {
            $archiveQuery->whereMonth('created_at', $month);
        }

        if ($category) {
            $archiveQuery->whereHas('submission.guideline', function ($query) use ($category) {
                $query->where('type', $category);
            });
        }

        $archiveRecords = $archiveQuery->get()->map(function ($archive) {
            $archive->is_archive = true; // Flag for actual Archive records
            $archive->files = $archive->submission ? $archive->submission->files : collect();
            $archive->generatedDocuments = $archive->submission ? $archive->submission->generatedDocuments : collect();
            return $archive;
        });

        // Paginate the filtered archive records
        $perPage = 10;
        $currentPage = $request->get('page', 1);
        $offset = ($currentPage - 1) * $perPage;
        $archives = new \Illuminate\Pagination\LengthAwarePaginator(
            $archiveRecords->slice($offset, $perPage),
            $archiveRecords->count(),
            $perPage,
            $currentPage,
            ['path' => $request->url(), 'pageName' => 'page']
        );

        // Append query parameters to pagination links
        $archives->appends($request->query());

        return view('admin.archives.index', compact('archives'));
    }

    // Manajemen Pengguna (list email & no HP)
    public function users()
    {
        $users = User::select('id', 'name', 'email', 'phone')->paginate(20);
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
            'email' => 'required|email|unique:users,email',
            'phone' => 'nullable|string|max:20',
            'password' => 'required|confirmed|min:8',
            'role' => 'required|in:admin,user'
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'password' => Hash::make($request->password),
            'role' => $request->role
        ]);

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
            'email' => 'required|email|unique:users,email,' . $user->id,
            'phone' => 'nullable|string|max:20',
            'role' => 'required|in:admin,user'
        ]);

        $user->update([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'role' => $request->role
        ]);

        return redirect()->route('admin.users')->with('success', 'Pengguna berhasil diperbarui!');
    }

    // Delete user
    public function destroy(User $user)
    {
        // Prevent deleting the current admin user
        if ($user->id === Auth::id()) {
            return redirect()->back()->with('error', 'Anda tidak dapat menghapus akun Anda sendiri!');
        }

        // Check if user has submissions
        if ($user->submissions()->count() > 0) {
            return redirect()->back()->with('error', 'Pengguna memiliki pengajuan aktif dan tidak dapat dihapus!');
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

            if (!file_exists($filePath)) {
                return response()->json([
                    'success' => false,
                    'message' => 'File tidak ditemukan'
                ], 404);
            }

            return response()->download($filePath, $file->file_name);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mendownload file'
            ], 500);
        }
    }

    // Download payment proof
    public function downloadPaymentProof($id)
    {
        try {
            $payment = Payment::findOrFail($id);

            if (!$payment->payment_proof) {
                return response()->json([
                    'success' => false,
                    'message' => 'Bukti pembayaran tidak ditemukan'
                ], 404);
            }

            $filePath = storage_path('app/public/' . $payment->payment_proof);

            if (!file_exists($filePath)) {
                return response()->json([
                    'success' => false,
                    'message' => 'File bukti pembayaran tidak ditemukan'
                ], 404);
            }

            return response()->download($filePath, basename($payment->payment_proof));
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mendownload bukti pembayaran'
            ], 500);
        }
    }

    // Export all archives to PDF (with filters)
    public function exportArchivesPdf(Request $request)
    {
        // Get actual Archive records only (avoid duplicates)
        $archiveRecordsQuery = Archive::with(['submission.generatedDocuments', 'submission.user', 'submission.guideline', 'submission.payment', 'submission.files', 'user']);

        // Apply filters
        $search = $request->get('search');
        $year = $request->get('year');
        $month = $request->get('month');
        $category = $request->get('category');

        if ($search) {
            $archiveRecordsQuery->where(function ($query) use ($search) {
                $query->whereHas('submission', function ($q) use ($search) {
                    $q->where('submission_number', 'like', '%' . $search . '%');
                })->orWhereHas('user', function ($q) use ($search) {
                    $q->where('name', 'like', '%' . $search . '%');
                });
            });
        }

        if ($year) {
            $archiveRecordsQuery->whereYear('created_at', $year);
        }

        if ($month) {
            $archiveRecordsQuery->whereMonth('created_at', $month);
        }

        if ($category) {
            $archiveRecordsQuery->whereHas('submission.guideline', function ($query) use ($category) {
                $query->where('type', $category);
            });
        }

        $archiveRecords = $archiveRecordsQuery->get()->map(function ($archive) {
            $archive->is_archive = true;
            $archive->files = $archive->submission ? $archive->submission->files : collect();
            $archive->generatedDocuments = $archive->submission ? $archive->submission->generatedDocuments : collect();
            // Copy submission fields to archive for PDF view
            if ($archive->submission) {
                $archive->submission_number = $archive->submission->submission_number ?? 'N/A';
                $archive->purpose = $archive->submission->purpose ?? 'N/A';
                $archive->start_date = $archive->submission->start_date;
                $archive->end_date = $archive->submission->end_date;
                $archive->status = $archive->submission->status ?? 'Selesai';
                $archive->created_at = $archive->submission->created_at;
                $archive->updated_at = $archive->submission->updated_at;
                $archive->user = $archive->submission->user;
                $archive->guideline = $archive->submission->guideline;
                $archive->payment = $archive->submission->payment;
            }
            return $archive;
        });

        // Sort by creation date (newest first)
        $allArchives = $archiveRecords->sortByDesc('created_at')->values();

        // Calculate summary
        $totalArchives = $allArchives->count();
        $totalPnbp = $allArchives->filter(function ($archive) {
            return $archive->submission && $archive->submission->guideline && $archive->submission->guideline->type == 'pnbp';
        })->count();
        $totalNonPnbp = $allArchives->filter(function ($archive) {
            return $archive->submission && $archive->submission->guideline && $archive->submission->guideline->type == 'non_pnbp';
        })->count();
        $totalAmount = $allArchives->sum(function ($archive) {
            return $archive->submission && $archive->submission->guideline ? $archive->submission->guideline->fee : 0;
        });

        // Generate PDF
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('admin.archives.pdf', compact(
            'allArchives',
            'totalArchives',
            'totalPnbp',
            'totalNonPnbp',
            'totalAmount',
            'search',
            'year',
            'month',
            'category'
        ));

        return $pdf->download('laporan-arsip-' . now()->format('Y-m-d-H-i-s') . '.pdf');
    }

    // Export selected archives to PDF
    public function exportSelectedArchivesPdf(Request $request)
    {
        $request->validate([
            'selected_archives' => 'required|array|min:1',
            'selected_archives.*' => 'integer'
        ]);

        $selectedIds = $request->selected_archives;

        // Get actual Archive records only (avoid duplicates)
        $archiveRecords = Archive::with(['submission.generatedDocuments', 'submission.user', 'submission.guideline', 'submission.payment', 'submission.files', 'user'])
            ->whereIn('id', $selectedIds)
            ->get()
            ->map(function ($archive) {
                $archive->is_archive = true;
                $archive->files = $archive->submission ? $archive->submission->files : collect();
                $archive->generatedDocuments = $archive->submission ? $archive->submission->generatedDocuments : collect();
                // Copy submission fields to archive for PDF view
                if ($archive->submission) {
                    $archive->submission_number = $archive->submission->submission_number ?? 'N/A';
                    $archive->purpose = $archive->submission->purpose ?? 'N/A';
                    $archive->start_date = $archive->submission->start_date;
                    $archive->end_date = $archive->submission->end_date;
                    $archive->status = $archive->submission->status ?? 'Selesai';
                    $archive->created_at = $archive->submission->created_at;
                    $archive->updated_at = $archive->submission->updated_at;
                    $archive->user = $archive->submission->user;
                    $archive->guideline = $archive->submission->guideline;
                    $archive->payment = $archive->submission->payment;
                }
                return $archive;
            });

        // Sort selected archives
        $selectedArchives = $archiveRecords->sortByDesc('created_at')->values();

        if ($selectedArchives->isEmpty()) {
            return redirect()->back()->with('error', 'Tidak ada arsip yang dipilih untuk diekspor.');
        }

        // Calculate summary for selected
        $totalArchives = $selectedArchives->count();
        $totalPnbp = $selectedArchives->filter(function ($archive) {
            return $archive->submission && $archive->submission->guideline && $archive->submission->guideline->type == 'pnbp';
        })->count();
        $totalNonPnbp = $selectedArchives->filter(function ($archive) {
            return $archive->submission && $archive->submission->guideline && $archive->submission->guideline->type == 'non_pnbp';
        })->count();
        $totalAmount = $selectedArchives->sum(function ($archive) {
            return $archive->submission && $archive->submission->guideline ? $archive->submission->guideline->fee : 0;
        });

        // Generate PDF
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('admin.archives.pdf', [
            'allArchives' => $selectedArchives,
            'totalArchives' => $totalArchives,
            'totalPnbp' => $totalPnbp,
            'totalNonPnbp' => $totalNonPnbp,
            'totalAmount' => $totalAmount,
            'search' => null,
            'year' => null,
            'month' => null,
            'category' => null
        ]);

        return $pdf->download('arsip-terpilih-' . now()->format('Y-m-d-H-i-s') . '.pdf');
    }

    // Show archive detail
    public function showArchive($id)
    {
        try {
            // Try to find as actual Archive record
            $archiveRecord = Archive::with(['submission.generatedDocuments', 'submission.user', 'submission.guideline', 'submission.payment', 'submission.files', 'submission.histories', 'user'])
                ->where('id', $id)
                ->first();

            if (!$archiveRecord) {
                return response()->json([
                    'success' => false,
                    'message' => 'Arsip tidak ditemukan'
                ], 404);
            }

            $archive = $archiveRecord;
            $archive->is_archive = true;
            $archive->files = $archive->submission ? $archive->submission->files : collect();
            $archive->generatedDocuments = $archive->submission ? $archive->submission->generatedDocuments : collect();
            $archive->histories = $archive->submission ? $archive->submission->histories : collect();

            // Format data for response
            $archiveData = [
                'id' => $archive->id,
                'submission' => [
                    'id' => $archive->submission->id ?? $archive->id,
                    'submission_number' => $archive->submission->submission_number ?? 'N/A',
                    'user' => $archive->submission->user ? [
                        'name' => $archive->submission->user->name ?? 'N/A',
                        'email' => $archive->submission->user->email ?? 'N/A',
                        'phone' => $archive->submission->user->phone ?? 'N/A'
                    ] : null,
                    'guideline' => $archive->submission->guideline ? [
                        'title' => $archive->submission->guideline->title ?? 'N/A',
                        'type' => $archive->submission->guideline->type ?? 'non_pnbp',
                        'fee' => $archive->submission->guideline->fee ?? 0
                    ] : null,
                    'purpose' => $archive->submission->purpose ?? 'N/A',
                    'start_date' => $archive->submission->start_date ? \Carbon\Carbon::parse($archive->submission->start_date)->format('d/m/Y') : 'N/A',
                    'end_date' => $archive->submission->end_date ? \Carbon\Carbon::parse($archive->submission->end_date)->format('d/m/Y') : 'N/A',
                    'status' => $archive->submission->status ?? 'Selesai',
                    'created_at' => $archive->submission->created_at ? \Carbon\Carbon::parse($archive->submission->created_at)->format('d/m/Y H:i') : 'N/A',
                    'updated_at' => $archive->submission->updated_at ? \Carbon\Carbon::parse($archive->submission->updated_at)->format('d/m/Y H:i') : 'N/A',
                    'payment' => $archive->submission->payment ? [
                        'id' => $archive->submission->payment->id,
                        'amount' => $archive->submission->payment->amount ?? 0,
                        'status' => $archive->submission->payment->status ?? 'pending',
                        'method' => $archive->submission->payment->payment_method ?? null,
                        'reference' => $archive->submission->payment->payment_reference ?? null,
                        'paid_at' => $archive->submission->payment->paid_at ? \Carbon\Carbon::parse($archive->submission->payment->paid_at)->format('d/m/Y H:i') : null,
                        'e_billing_path' => $archive->submission->payment->e_billing_path ?? null,
                        'e_billing_filename' => $archive->submission->payment->e_billing_filename ?? null
                    ] : null,
                    'files' => $archive->files ? $archive->files->map(function ($file) {
                        return [
                            'id' => $file->id,
                            'file_name' => $file->file_name ?? 'Unknown',
                            'document_name' => $file->document_name ?? 'Document',
                            'file_size' => $file->file_size ?? 0,
                            'file_size_human' => $this->formatFileSize($file->file_size ?? 0),
                            'file_type' => $file->file_type ?? 'application/octet-stream',
                            'created_at' => $file->created_at ? \Carbon\Carbon::parse($file->created_at)->format('d/m/Y H:i') : 'N/A'
                        ];
                    }) : [],
                    'generatedDocuments' => $archive->generatedDocuments ? $archive->generatedDocuments->map(function ($doc) {
                        return [
                            'id' => $doc->id,
                            'document_name' => $doc->document_name ?? 'Document',
                            'document_type' => $doc->document_type ?? 'Unknown',
                            'file_size' => $doc->file_size ?? 0,
                            'formatted_file_size' => $this->formatFileSize($doc->file_size ?? 0),
                            'uploader_name' => 'Admin',
                            'created_at' => $doc->created_at ? \Carbon\Carbon::parse($doc->created_at)->format('d/m/Y H:i') : 'N/A'
                        ];
                    }) : [],
                    'histories' => $archive->histories ? $archive->histories->map(function ($history) {
                        return [
                            'title' => $history->title ?? 'Unknown Action',
                            'description' => $history->description ?? 'No description',
                            'actor' => $history->actor ? $history->actor->name : 'System',
                            'created_at' => $history->created_at ? \Carbon\Carbon::parse($history->created_at)->format('d/m/Y H:i') : 'N/A'
                        ];
                    }) : []
                ],
                'archive_date' => $archive->archive_date ? \Carbon\Carbon::parse($archive->archive_date)->format('d/m/Y H:i') : ($archive->updated_at ? \Carbon\Carbon::parse($archive->updated_at)->format('d/m/Y H:i') : null),
                'notes' => $archive->notes ?? 'Pengajuan selesai diproses dan diarsipkan',
                'is_archive' => $archive->is_archive ?? false
            ];

            return response()->json([
                'success' => true,
                'data' => $archiveData
            ]);
        } catch (\Exception $e) {
            \Log::error('Error loading archive detail: ' . $e->getMessage(), [
                'archive_id' => $id,
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat memuat detail arsip: ' . $e->getMessage()
            ], 500);
        }
    }

    // Helper method to format file size
    private function formatFileSize($bytes)
    {
        if ($bytes === null || $bytes == 0) {
            return '0 bytes';
        }
        if ($bytes >= 1073741824) {
            return number_format($bytes / 1073741824, 2) . ' GB';
        } elseif ($bytes >= 1048576) {
            return number_format($bytes / 1048576, 2) . ' MB';
        } elseif ($bytes >= 1024) {
            return number_format($bytes / 1024, 2) . ' KB';
        } else {
            return $bytes . ' bytes';
        }
    }
}
