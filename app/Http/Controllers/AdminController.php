<?php

namespace App\Http\Controllers;

use App\Models\Submission;
use App\Models\Guideline;
use App\Models\User;
use App\Models\Payment;
use App\Models\GeneratedDocument;
use App\Models\Archive;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use ZipArchive;

class AdminController extends Controller
{
    // Dashboard Admin - Fixed dengan Error Handling
    public function dashboard()
    {
        try {
            // Test database connection
            DB::connection()->getPdo();

            // Enhanced statistics
            $stats = [
                'pending_requests' => Submission::where('status', 'pending')->count(),
                'pending_payments' => Submission::where('status', 'payment_pending')->count(),
                'processing' => Submission::where('status', 'processing')->count(),
                'completed' => Submission::where('status', 'completed')->count(),
                'total_users' => User::where('role', 'user')->count(),
                'total_submissions' => Submission::count(),
                'today_submissions' => Submission::whereDate('created_at', Carbon::today())->count(),
                'this_month_submissions' => Submission::whereMonth('created_at', Carbon::now()->month)->count()
            ];

            // Recent submissions dengan safe handling
            $recentSubmissions = Submission::with(['user', 'guideline'])
                ->orderBy('created_at', 'desc')
                ->take(10)
                ->get()
                ->map(function ($submission) {
                    $user = $submission->user;
                    $guideline = $submission->guideline;
                    return [
                        'id' => $submission->id,
                        'submission_number' => $submission->submission_number ?? 'SUB-' . str_pad($submission->id, 4, '0', STR_PAD_LEFT),
                        'user_name' => $user ? $user->name : 'N/A',
                        'guideline_title' => $guideline ? $guideline->title : 'N/A',
                        'status' => $submission->status,
                        'status_label' => $this->getStatusLabel($submission->status),
                        'created_at_formatted' => $submission->created_at->format('d/m/Y H:i'),
                        'type_label' => ($guideline ? $guideline->type : 'non_pnbp') === 'pnbp' ? 'PNBP (Berbayar)' : 'Non-PNBP (Gratis)'
                    ];
                });

            Log::info('Admin Dashboard loaded successfully', [
                'stats' => $stats,
                'submissions_count' => $recentSubmissions->count()
            ]);

            return view('admin.dashboard', compact('stats', 'recentSubmissions'));
        } catch (\Exception $e) {
            Log::error('Admin Dashboard Error: ' . $e->getMessage());

            // Fallback data
            $stats = [
                'pending_requests' => 0,
                'pending_payments' => 0,
                'processing' => 0,
                'completed' => 0,
                'total_users' => 0,
                'total_submissions' => 0,
                'today_submissions' => 0,
                'this_month_submissions' => 0
            ];

            $recentSubmissions = collect();

            return view('admin.dashboard', compact('stats', 'recentSubmissions'))
                ->with('error', 'Error loading dashboard: ' . $e->getMessage());
        }
    }

    // Get Guidelines - FIXED
    public function getGuidelines()
    {
        try {
            $guidelines = Guideline::orderBy('created_at', 'desc')->get()->map(function ($guideline) {
                $guideline->required_documents = safe_json_decode($guideline->required_documents, []);
                return $guideline;
            });

            Log::info('Guidelines loaded successfully', ['count' => $guidelines->count()]);

            return response()->json([
                'success' => true,
                'data' => $guidelines
            ]);
        } catch (\Exception $e) {
            Log::error('Admin Guidelines Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error loading guidelines: ' . $e->getMessage(),
                'data' => []
            ], 500);
        }
    }

    // Get Submissions - FIXED
    public function getSubmissions()
    {
        try {
            $statusFilter = request()->get('status', '');

            $query = Submission::with(['user', 'guideline'])
                ->orderBy('created_at', 'desc');

            if ($statusFilter) {
                $query->where('status', $statusFilter);
            }

            $submissions = $query->get()
                ->map(function ($submission) {
                    $user = $submission->user;
                    $guideline = $submission->guideline;
                    return [
                        'id' => $submission->id,
                        'submission_number' => $submission->submission_number ?? 'SUB-' . str_pad($submission->id, 4, '0', STR_PAD_LEFT),
                        'user' => [
                            'id' => $user ? $user->id : null,
                            'name' => $user ? $user->name : 'N/A',
                            'email' => $user ? $user->email : 'N/A'
                        ],
                        'guideline' => [
                            'id' => $guideline ? $guideline->id : null,
                            'title' => $guideline ? $guideline->title : 'N/A',
                            'type' => $guideline ? $guideline->type : 'non_pnbp',
                            'fee' => $guideline ? $guideline->fee : 0
                        ],
                        'status' => $submission->status,
                        'status_label' => $this->getStatusLabel($submission->status),
                        'type_label' => ($guideline ? $guideline->type : 'non_pnbp') === 'pnbp' ? 'PNBP' : 'Non-PNBP',
                        'created_at' => $submission->created_at->format('d/m/Y H:i'),
                        'created_at_formatted' => $submission->created_at->format('d/m/Y H:i')
                    ];
                });

            return response()->json([
                'success' => true,
                'data' => $submissions
            ]);
        } catch (\Exception $e) {
            Log::error('Get Submissions Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error loading submissions data',
                'data' => []
            ], 500);
        }
    }

    // Store Guidelines - FIXED
    public function storeGuideline(Request $request)
    {
        try {
            $request->validate([
                'title' => 'required|string|max:255',
                'description' => 'required|string',
                'type' => 'required|in:pnbp,non_pnbp',
                'required_documents' => 'required|array',
                'fee' => 'required|numeric|min:0'
            ]);

            $guideline = Guideline::create([
                'title' => $request->title,
                'description' => $request->description,
                'type' => $request->type,
                'required_documents' => json_encode($request->required_documents),
                'fee' => $request->fee,
                'is_active' => true
            ]);

            Log::info('Guideline created successfully', ['guideline_id' => $guideline->id]);

            return response()->json([
                'success' => true,
                'message' => 'Panduan berhasil dibuat',
                'data' => $guideline
            ]);
        } catch (\Exception $e) {
            Log::error('Create Guideline Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal membuat panduan: ' . $e->getMessage()
            ], 500);
        }
    }

    // Update Guidelines - FIXED
    public function updateGuideline(Request $request, $id)
    {
        try {
            $request->validate([
                'title' => 'required|string|max:255',
                'description' => 'required|string',
                'type' => 'required|in:pnbp,non_pnbp',
                'required_documents' => 'required|array',
                'fee' => 'required|numeric|min:0'
            ]);

            $guideline = Guideline::findOrFail($id);
            $guideline->update([
                'title' => $request->title,
                'description' => $request->description,
                'type' => $request->type,
                'required_documents' => json_encode($request->required_documents),
                'fee' => $request->fee
            ]);

            Log::info('Guideline updated successfully', ['guideline_id' => $id]);

            return response()->json([
                'success' => true,
                'message' => 'Panduan berhasil diperbarui',
                'data' => $guideline
            ]);
        } catch (\Exception $e) {
            Log::error('Update Guideline Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal memperbarui panduan: ' . $e->getMessage()
            ], 500);
        }
    }

    // Show Guideline - FIXED
    public function showGuideline($id)
    {
        try {
            $guideline = Guideline::findOrFail($id);
            $guideline->required_documents = safe_json_decode($guideline->required_documents, []);

            return response()->json([
                'success' => true,
                'data' => $guideline
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Panduan tidak ditemukan'
            ], 404);
        }
    }

    // Delete Guideline - FIXED
    public function destroyGuideline($id)
    {
        try {
            $guideline = Guideline::findOrFail($id);

            // Check if guideline is being used
            if ($guideline->submissions()->exists()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tidak dapat menghapus panduan yang sudah digunakan dalam pengajuan'
                ], 422);
            }

            $guideline->delete();

            Log::info('Guideline deleted successfully', ['guideline_id' => $id]);

            return response()->json([
                'success' => true,
                'message' => 'Panduan berhasil dihapus'
            ]);
        } catch (\Exception $e) {
            Log::error('Delete Guideline Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus panduan: ' . $e->getMessage()
            ], 500);
        }
    }

    // Get Payments - FIXED
    public function getPayments()
    {
        try {
            $payments = Payment::with(['submission.user', 'submission.guideline'])
                ->orderBy('created_at', 'desc')
                ->get()
                ->map(function ($payment) {
                    $submission = $payment->submission;
                    $user = $submission ? $submission->user : null;
                    return [
                        'id' => $payment->id,
                        'submission_id' => $payment->submission_id,
                        'submission_number' => $submission ? ($submission->submission_number ?? 'SUB-' . str_pad($payment->submission_id, 4, '0', STR_PAD_LEFT)) : 'N/A',
                        'user_name' => $user ? $user->name : 'N/A',
                        'amount' => $payment->amount,
                        'status' => $payment->status,
                        'status_label' => $this->getPaymentStatusLabel($payment->status),
                        'payment_method' => $payment->payment_method,
                        'created_at' => $payment->created_at->format('d/m/Y H:i'),
                        'paid_at' => $payment->paid_at ? $payment->paid_at->format('d/m/Y H:i') : null
                    ];
                });

            Log::info('Payments loaded successfully', ['count' => $payments->count()]);

            return response()->json([
                'success' => true,
                'data' => $payments
            ]);
        } catch (\Exception $e) {
            Log::error('Admin Payments Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error loading payments: ' . $e->getMessage(),
                'data' => []
            ], 500);
        }
    }

    // Get Documents - FIXED
    public function getDocuments()
    {
        try {
            $documents = GeneratedDocument::with(['submission.user', 'submission.guideline'])
                ->orderBy('created_at', 'desc')
                ->get()
                ->map(function ($document) {
                    $submission = $document->submission;
                    $user = $submission ? $submission->user : null;
                    return [
                        'id' => $document->id,
                        'submission_id' => $document->submission_id,
                        'submission_number' => $submission ? ($submission->submission_number ?? 'SUB-' . str_pad($document->submission_id, 4, '0', STR_PAD_LEFT)) : 'N/A',
                        'user_name' => $user ? $user->name : 'N/A',
                        'document_name' => $document->document_name,
                        'document_type' => $document->document_type,
                        'file_size' => $this->formatFileSize($document->file_size),
                        'created_at' => $document->created_at->format('d/m/Y H:i'),
                        'url' => \Illuminate\Support\Facades\Storage::url($document->document_path)
                    ];
                });

            Log::info('Documents loaded successfully', ['count' => $documents->count()]);

            return response()->json([
                'success' => true,
                'data' => $documents
            ]);
        } catch (\Exception $e) {
            Log::error('Admin Documents Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error loading documents: ' . $e->getMessage(),
                'data' => []
            ], 500);
        }
    }

    // Get Archives - FIXED
    public function getArchives()
    {
        try {
            $archives = Archive::with(['submission.user', 'submission.guideline'])
                ->orderBy('created_at', 'desc')
                ->get()
                ->map(function ($archive) {
                    $submission = $archive->submission;
                    $user = $submission ? $submission->user : null;
                    return [
                        'id' => $archive->id,
                        'submission_id' => $archive->submission_id,
                        'submission_number' => $submission ? ($submission->submission_number ?? 'SUB-' . str_pad($archive->submission_id, 4, '0', STR_PAD_LEFT)) : 'N/A',
                        'user_name' => $user ? $user->name : 'N/A',
                        'archive_reason' => $archive->archive_reason,
                        'archived_at' => $archive->archived_at ? $archive->archived_at->format('d/m/Y H:i') : null,
                        'created_at' => $archive->created_at->format('d/m/Y H:i')
                    ];
                });

            Log::info('Archives loaded successfully', ['count' => $archives->count()]);

            return response()->json([
                'success' => true,
                'data' => $archives
            ]);
        } catch (\Exception $e) {
            Log::error('Admin Archives Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error loading archives: ' . $e->getMessage(),
                'data' => []
            ], 500);
        }
    }

    // Get Users - FIXED
    public function getUsers()
    {
        try {
            $users = User::where('role', 'user')->orderBy('created_at', 'desc')->get()->map(function ($user) {
                return [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'phone' => $user->phone,
                    'created_at' => $user->created_at->format('d/m/Y H:i'),
                    'status' => 'active' // Assuming all users are active for now
                ];
            });

            Log::info('Users loaded successfully', ['count' => $users->count()]);

            return response()->json([
                'success' => true,
                'data' => $users
            ]);
        } catch (\Exception $e) {
            Log::error('Admin Users Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error loading users: ' . $e->getMessage(),
                'data' => []
            ], 500);
        }
    }

    // Get Submission Detail - NEW
    public function getSubmissionDetail($id)
    {
        try {
            $submission = Submission::with(['user', 'guideline', 'submissionFiles', 'payment', 'submissionHistory'])
                ->findOrFail($id);

            $data = [
                'id' => $submission->id,
                'submission_number' => $submission->submission_number ?? 'SUB-' . str_pad($submission->id, 4, '0', STR_PAD_LEFT),
                'status' => $submission->status,
                'status_label' => $this->getStatusLabel($submission->status),
                'purpose' => $submission->purpose ?? 'N/A',
                'created_at' => $submission->created_at->format('d/m/Y H:i'),
                'user' => [
                    'name' => $submission->user ? $submission->user->name : 'N/A',
                    'email' => $submission->user ? $submission->user->email : 'N/A',
                    'phone' => $submission->user ? $submission->user->phone : 'N/A'
                ],
                'guideline' => [
                    'title' => $submission->guideline ? $submission->guideline->title : 'N/A',
                    'type' => $submission->guideline ? $submission->guideline->type : 'non_pnbp',
                    'fee' => $submission->guideline ? $submission->guideline->fee : 0,
                    'required_documents' => $submission->guideline ? safe_json_decode($submission->guideline->required_documents, []) : []
                ],
                'files' => $submission->submissionFiles->map(function ($file) {
                    return [
                        'id' => $file->id,
                        'file_name' => $file->file_name,
                        'file_path' => \Illuminate\Support\Facades\Storage::url($file->file_path),
                        'file_size' => $this->formatFileSize($file->file_size),
                        'uploaded_at' => $file->created_at->format('d/m/Y H:i')
                    ];
                }),
                'payment' => $submission->payment ? [
                    'id' => $submission->payment->id,
                    'amount' => $submission->payment->amount,
                    'status' => $submission->payment->status,
                    'status_label' => $this->getPaymentStatusLabel($submission->payment->status),
                    'payment_method' => $submission->payment->payment_method,
                    'paid_at' => $submission->payment->paid_at ? $submission->payment->paid_at->format('d/m/Y H:i') : null
                ] : null,
                'history' => $submission->submissionHistory->map(function ($history) {
                    return [
                        'id' => $history->id,
                        'status' => $history->status,
                        'notes' => $history->notes ?? 'N/A',
                        'created_at' => $history->created_at->format('d/m/Y H:i'),
                        'user_name' => $history->user ? $history->user->name : 'Admin'
                    ];
                })->sortByDesc('created_at')
            ];

            Log::info('Submission detail loaded', ['submission_id' => $id]);

            return response()->json([
                'success' => true,
                'data' => $data
            ]);
        } catch (\Exception $e) {
            Log::error('Get Submission Detail Error: ' . $e->getMessage(), ['submission_id' => $id]);
            return response()->json([
                'success' => false,
                'message' => 'Submission not found or error loading details',
                'data' => null
            ], 404);
        }
    }

    // Verify/Approve Submission - NEW
    public function verifySubmission(Request $request, $id)
    {
        try {
            $submission = Submission::findOrFail($id);

            $nextStatus = $submission->guideline && $submission->guideline->type === 'pnbp' ? 'payment_pending' : 'processing';

            $submission->update(['status' => $nextStatus]);

            // Create history entry
            if (class_exists('App\Models\SubmissionHistory')) {
                \App\Models\SubmissionHistory::create([
                    'submission_id' => $id,
                    'user_id' => Auth::id(),
                    'status' => $nextStatus,
                    'notes' => 'Submission verified/approved by admin'
                ]);
            }

            Log::info('Submission verified', ['submission_id' => $id, 'new_status' => $nextStatus]);

            return response()->json([
                'success' => true,
                'message' => 'Submission approved successfully',
                'data' => ['status' => $nextStatus]
            ]);
        } catch (\Exception $e) {
            Log::error('Verify Submission Error: ' . $e->getMessage(), ['submission_id' => $id]);
            return response()->json([
                'success' => false,
                'message' => 'Failed to approve submission: ' . $e->getMessage()
            ], 500);
        }
    }

    // Reject Submission - NEW
    public function rejectSubmission(Request $request, $id)
    {
        try {
            $request->validate([
                'reason' => 'required|string|max:500'
            ]);

            $submission = Submission::findOrFail($id);

            $submission->update([
                'status' => 'rejected',
                'rejected_reason' => $request->reason // Assume column exists, or add to history
            ]);

            // Create history entry
            if (class_exists('App\Models\SubmissionHistory')) {
                \App\Models\SubmissionHistory::create([
                    'submission_id' => $id,
                    'user_id' => Auth::id(),
                    'status' => 'rejected',
                    'notes' => 'Rejected: ' . $request->reason
                ]);
            }

            Log::info('Submission rejected', ['submission_id' => $id, 'reason' => $request->reason]);

            return response()->json([
                'success' => true,
                'message' => 'Submission rejected successfully',
                'data' => ['status' => 'rejected']
            ]);
        } catch (\Exception $e) {
            Log::error('Reject Submission Error: ' . $e->getMessage(), ['submission_id' => $id]);
            return response()->json([
                'success' => false,
                'message' => 'Failed to reject submission: ' . $e->getMessage()
            ], 500);
        }
    }

    // Helper methods
    private function getStatusLabel($status)
    {
        $labels = [
            'pending' => 'Menunggu Verifikasi',
            'verified' => 'Terverifikasi',
            'payment_pending' => 'Menunggu Pembayaran',
            'paid' => 'Pembayaran Diterima',
            'processing' => 'Sedang Diproses',
            'completed' => 'Selesai',
            'rejected' => 'Ditolak'
        ];

        return $labels[$status] ?? 'Status Tidak Dikenal';
    }

    private function getPaymentStatusLabel($status)
    {
        $labels = [
            'pending' => 'Menunggu Pembayaran',
            'uploaded' => 'Bukti Diupload',
            'verified' => 'Pembayaran Diterima',
            'rejected' => 'Pembayaran Ditolak'
        ];

        return $labels[$status] ?? 'Status Tidak Dikenal';
    }

    private function formatFileSize($bytes)
    {
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
