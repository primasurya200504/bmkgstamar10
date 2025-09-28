<?php

namespace App\Http\Controllers;

use App\Models\Submission;
use App\Models\Guideline;
use App\Models\Payment;
use App\Models\GeneratedDocument;
use App\Models\SubmissionHistory;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class UserController extends Controller
{
    /**
     * User Dashboard dengan Enhanced Statistics
     */
    public function dashboard()
    {
        try {
            $user = Auth::user();

            // Get submissions dengan error handling yang lebih baik
            $submissions = Submission::where('user_id', $user->id)
                ->with(['guideline', 'payment', 'generatedDocuments', 'histories'])
                ->orderBy('created_at', 'desc')
                ->get();

            // Enhanced statistics
            $stats = [
                'pending' => $submissions->where('status', 'pending')->count(),
                'in_process' => $submissions->whereIn('status', ['verified', 'payment_pending', 'paid', 'processing'])->count(),
                'completed' => $submissions->where('status', 'completed')->count(),
                'rejected' => $submissions->where('status', 'rejected')->count(),
                'total' => $submissions->count()
            ];

            // Recent activities - last 5 submissions dengan safe fallbacks
            $recentActivities = $submissions->take(5)->map(function ($submission) {
                return [
                    'id' => $submission->id,
                    'submission_number' => $submission->submission_number ?? 'SUB-' . str_pad($submission->id, 4, '0', STR_PAD_LEFT),
                    'guideline' => (object)[
                        'title' => $submission->guideline ? $submission->guideline->title : 'N/A'
                    ],
                    'status' => $submission->status,
                    'created_at' => $submission->created_at,
                ];
            });

            // Get all active guidelines - INI PENTING!
            $guidelines = Guideline::where('is_active', true)->get()->map(function ($guideline) {
                // Safe decode required_documents menggunakan helper function
                $guideline->required_documents = safe_json_decode($guideline->required_documents, []);
                return $guideline;
            });

            // Notifications - kosong untuk sekarang
            $notifications = collect();

            Log::info('User Dashboard loaded successfully', [
                'user_id' => $user->id,
                'stats' => $stats,
                'submissions_count' => $submissions->count(),
                'guidelines_count' => $guidelines->count()
            ]);

            return view('user.dashboard', compact(
                'stats',
                'recentActivities',
                'guidelines',
                'notifications'
            ));
        } catch (\Exception $e) {
            Log::error('User Dashboard Error: ' . $e->getMessage(), [
                'user_id' => Auth::id(),
                'trace' => $e->getTraceAsString()
            ]);

            // Fallback data - Pastikan guidelines tetap tersedia
            $stats = ['pending' => 0, 'in_process' => 0, 'completed' => 0, 'rejected' => 0, 'total' => 0];
            $recentActivities = collect();
            $guidelines = Guideline::where('is_active', true)->get()->map(function ($guideline) {
                $guideline->required_documents = safe_json_decode($guideline->required_documents, []);
                return $guideline;
            });
            $notifications = collect();

            return view('user.dashboard', compact(
                'stats',
                'recentActivities',
                'guidelines',
                'notifications'
            ))->with('error', 'Error loading dashboard: ' . $e->getMessage());
        }
    }

    /**
     * Get user submissions via AJAX - FIXED untuk format response yang benar
     */
    public function getSubmissions(Request $request)
    {
        try {
            $user = Auth::user();
            $query = Submission::where('user_id', $user->id)
                ->with(['guideline', 'payment', 'histories']);

            // Apply status filter if provided
            if ($request->has('status') && !empty($request->status)) {
                $query->where('status', $request->status);
            }

            $submissions = $query->orderBy('created_at', 'desc')
                ->get()
                ->map(function ($submission) {
                    return [
                        'id' => $submission->id,
                        'submission_number' => $submission->submission_number ?? 'SUB-' . str_pad($submission->id, 4, '0', STR_PAD_LEFT),
                        'guideline' => [
                            'title' => $submission->guideline->title ?? 'N/A',
                            'type' => $submission->guideline->type ?? 'non_pnbp',
                            'fee' => $submission->guideline->fee ?? 0
                        ],
                        'status' => $submission->status,
                        'type' => $submission->guideline->type ?? 'non_pnbp', // Untuk filtering di frontend
                        'created_at' => $submission->created_at->format('d/m/Y'),
                        'payment' => $submission->payment ? [
                            'amount' => $submission->payment->amount,
                            'status' => $submission->payment->status
                        ] : null
                    ];
                });

            return response()->json([
                'success' => true,
                'data' => $submissions
            ]);
        } catch (\Exception $e) {
            Log::error('Error loading user submissions: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Error loading submissions: ' . $e->getMessage(),
                'data' => []
            ], 500);
        }
    }

    /**
     * Get single submission detail
     */
    public function getSubmission($id)
    {
        try {
            $user = Auth::user();
            $submission = Submission::where('user_id', $user->id)
                ->where('id', $id)
                ->with(['guideline', 'payment', 'histories.actor', 'generatedDocuments'])
                ->firstOrFail();

            // Format data untuk response
            $submissionData = [
                'id' => $submission->id,
                'submission_number' => $submission->submission_number,
                'guideline' => [
                    'title' => $submission->guideline->title,
                    'description' => $submission->guideline->description,
                    'type' => $submission->guideline->type,
                    'fee' => $submission->guideline->fee,
                    'required_documents' => safe_json_decode($submission->guideline->required_documents, [])
                ],
                'purpose' => $submission->purpose,
                'start_date' => $submission->start_date,
                'end_date' => $submission->end_date,
                'status' => $submission->status,
                'status_label' => $this->getStatusLabel($submission->status),
                'created_at' => $submission->created_at->format('d/m/Y H:i'),
                'payment' => $submission->payment ? [
                    'amount' => $submission->payment->amount,
                    'status' => $submission->payment->status,
                    'method' => $submission->payment->payment_method,
                    'reference' => $submission->payment->payment_reference,
                    'paid_at' => $submission->payment->paid_at ? $submission->payment->paid_at->format('d/m/Y H:i') : null
                ] : null,
                'documents' => $submission->generatedDocuments->map(function ($doc) {
                    return [
                        'id' => $doc->id,
                        'name' => $doc->document_name,
                        'type' => $doc->document_type,
                        'size' => $this->formatFileSize($doc->file_size),
                        'url' => Storage::url($doc->document_path)
                    ];
                }),
                'histories' => $submission->histories->map(function ($history) {
                    return [
                        'title' => $history->title,
                        'description' => $history->description,
                        'actor' => $history->actor ? $history->actor->name : 'System',
                        'created_at' => $history->created_at->format('d/m/Y H:i')
                    ];
                })
            ];

            return response()->json([
                'success' => true,
                'data' => $submissionData
            ]);
        } catch (\Exception $e) {
            Log::error('Error loading submission detail: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Submission not found'
            ], 404);
        }
    }

    /**
     * Submit Pengajuan Surat/Data - FIXED dengan nama method yang sesuai route
     */
    public function submitSurat(Request $request)
    {
        try {
            $request->validate([
                'guideline_id' => 'required|exists:guidelines,id',
                'purpose' => 'required|string|max:1000',
                'start_date' => 'required|date',
                'end_date' => 'required|date|after_or_equal:start_date',
                'documents.*' => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:5120'
            ]);

            $user = Auth::user();
            $guideline = Guideline::findOrFail($request->guideline_id);

            DB::beginTransaction();

            // Generate submission number
            $submissionNumber = 'BMKG-' . strtoupper($guideline->type) . '-' .
                date('md') . '-' . date('Y') . '-' .
                str_pad(Submission::count() + 1, 4, '0', STR_PAD_LEFT);

            // Create submission
            $submission = Submission::create([
                'user_id' => $user->id,
                'guideline_id' => $guideline->id,
                'submission_number' => $submissionNumber,
                'type' => $guideline->type,
                'purpose' => $request->purpose,
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
                'status' => 'pending'
            ]);

            // Handle document uploads
            if ($request->hasFile('documents')) {
                foreach ($request->file('documents') as $index => $file) {
                    $path = $file->store('submissions/' . $submission->id, 'public');

                    // Create document record (using GeneratedDocument model)
                    GeneratedDocument::create([
                        'submission_id' => $submission->id,
                        'document_name' => $file->getClientOriginalName(),
                        'document_path' => $path,
                        'document_type' => 'supporting_document',
                        'file_size' => $file->getSize(),
                        'mime_type' => $file->getClientMimeType(),
                        'uploaded_by' => $user->id
                    ]);
                }
            }

            // Create history
            SubmissionHistory::create([
                'submission_id' => $submission->id,
                'actor_id' => $user->id,
                'actor_type' => 'user',
                'action' => 'submitted',
                'title' => 'Pengajuan Disubmit',
                'description' => 'Pengajuan telah disubmit dan menunggu verifikasi admin'
            ]);

            // Create payment record if PNBP
            if ($guideline->type === 'pnbp' && $guideline->fee > 0) {
                Payment::create([
                    'submission_id' => $submission->id,
                    'amount' => $guideline->fee,
                    'status' => 'pending'
                ]);

                $submission->update(['status' => 'payment_pending']);
            }

            DB::commit();

            Log::info('Submission created successfully', ['submission_id' => $submission->id, 'user_id' => $user->id]);

            return response()->json([
                'success' => true,
                'message' => 'Pengajuan berhasil dikirim',
                'data' => $submission->load('guideline')
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error creating submission: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Gagal mengirim pengajuan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Upload Payment Proof - FIXED nama method sesuai route
     */
    public function uploadPaymentProof(Request $request, $id)
    {
        try {
            $request->validate([
                'payment_method' => 'required|string',
                'payment_reference' => 'nullable|string',
                'payment_proof' => 'required|file|mimes:jpg,jpeg,png,pdf|max:5120'
            ]);

            $user = Auth::user();
            $submission = Submission::where('user_id', $user->id)
                ->where('id', $id)
                ->firstOrFail();

            $payment = Payment::where('submission_id', $submission->id)->firstOrFail();

            DB::beginTransaction();

            // Upload payment proof
            $proofPath = $request->file('payment_proof')->store('payments/' . $submission->id, 'public');

            // Update payment
            $payment->update([
                'payment_method' => $request->payment_method,
                'payment_reference' => $request->payment_reference,
                'payment_proof' => $proofPath,
                'status' => 'uploaded',
                'paid_at' => now()
            ]);

            // Update submission status
            $submission->update(['status' => 'paid']);

            // Create history
            SubmissionHistory::create([
                'submission_id' => $submission->id,
                'actor_id' => $user->id,
                'actor_type' => 'user',
                'action' => 'payment_uploaded',
                'title' => 'Bukti Pembayaran Diupload',
                'description' => 'User telah mengupload bukti pembayaran dengan metode: ' . $request->payment_method
            ]);

            DB::commit();

            Log::info('Payment proof uploaded successfully', ['submission_id' => $submission->id, 'payment_id' => $payment->id]);

            return response()->json([
                'success' => true,
                'message' => 'Bukti pembayaran berhasil diupload'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error uploading payment: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Gagal upload bukti pembayaran: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get Guidelines untuk dropdown
     */
    public function guidelines()
    {
        try {
            $guidelines = Guideline::where('is_active', true)
                ->orderBy('title')
                ->get()
                ->map(function ($guideline) {
                    $guideline->required_documents = safe_json_decode($guideline->required_documents, []);
                    return $guideline;
                });

            return response()->json([
                'success' => true,
                'data' => $guidelines
            ]);
        } catch (\Exception $e) {
            Log::error('Error loading guidelines: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error loading guidelines',
                'data' => []
            ], 500);
        }
    }

    /**
     * History Page - View untuk halaman riwayat
     */
    public function history()
    {
        try {
            $user = Auth::user();

            // Get submissions with safe handling
            $submissions = $user->submissions()
                ->with(['guideline', 'payment', 'histories' => function ($q) {
                    $q->with('actor')->orderBy('created_at', 'desc');
                }])
                ->orderBy('created_at', 'desc')
                ->get()
                ->map(function ($submission) {
                    return [
                        'id' => $submission->id,
                        'submission_number' => $submission->submission_number ?? 'SUB-' . str_pad($submission->id, 4, '0', STR_PAD_LEFT),
                        'guideline' => [
                            'title' => $submission->guideline->title ?? 'N/A',
                            'type' => $submission->guideline->type ?? 'non_pnbp',
                            'fee' => $submission->guideline->fee ?? 0
                        ],
                        'status' => $submission->status,
                        'status_label' => $this->getStatusLabel($submission->status),
                        'type_label' => ($submission->guideline->type ?? 'non_pnbp') === 'pnbp' ? 'PNBP (Berbayar)' : 'Non-PNBP (Gratis)',
                        'created_at' => $submission->created_at->format('d/m/Y'),
                        'created_at_formatted' => $submission->created_at->format('d/m/Y H:i'),
                        'time_ago' => $submission->created_at->diffForHumans(),
                        'progress_percentage' => $this->calculateProgressPercentage($submission->status)
                    ];
                });

            Log::info('User history loaded successfully', [
                'user_id' => $user->id,
                'submissions_count' => $submissions->count()
            ]);

            // Return as JSON for AJAX requests
            if (request()->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'data' => $submissions
                ]);
            }

            // Return view for direct access
            return view('user.history', compact('submissions'));
        } catch (\Exception $e) {
            Log::error('User History Error: ' . $e->getMessage(), ['user_id' => Auth::id()]);

            if (request()->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error loading submission history: ' . $e->getMessage(),
                    'data' => []
                ], 500);
            }

            return view('user.history', ['submissions' => collect()])
                ->with('error', 'Error loading submission history: ' . $e->getMessage());
        }
    }

    /**
     * Profile page - View
     */
    public function profile()
    {
        try {
            $user = Auth::user();
            return view('user.profile', compact('user'));
        } catch (\Exception $e) {
            Log::error('Error loading profile: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Error loading profile');
        }
    }

    /**
     * Update Profile - FIXED nama method sesuai route
     */
    public function updateProfile(Request $request)
    {
        try {
            $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:users,email,' . Auth::id(),
                'phone' => 'nullable|string|max:20',
                'password' => 'nullable|confirmed|min:8'
            ]);

            $user = Auth::user();

            $updateData = [
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone
            ];

            if ($request->filled('password')) {
                $updateData['password'] = Hash::make($request->password);
            }

            $user->update($updateData);

            Log::info('Profile updated successfully', ['user_id' => $user->id]);

            return response()->json([
                'success' => true,
                'message' => 'Profil berhasil diperbarui'
            ]);
        } catch (\Exception $e) {
            Log::error('Error updating profile: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Gagal memperbarui profil: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Change Password
     */
    public function changePassword(Request $request)
    {
        try {
            $request->validate([
                'current_password' => 'required',
                'new_password' => 'required|confirmed|min:8'
            ]);

            $user = Auth::user();

            if (!Hash::check($request->current_password, $user->password)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Password lama tidak sesuai'
                ], 422);
            }

            $user->update([
                'password' => Hash::make($request->new_password)
            ]);

            Log::info('Password changed successfully', ['user_id' => $user->id]);

            return response()->json([
                'success' => true,
                'message' => 'Password berhasil diubah'
            ]);
        } catch (\Exception $e) {
            Log::error('Error changing password: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Gagal mengubah password: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Download Generated Document
     */
    public function downloadDocument($id)
    {
        try {
            $user = Auth::user();

            // Get document through user's submissions
            $document = GeneratedDocument::whereHas('submission', function ($q) use ($user) {
                $q->where('user_id', $user->id);
            })->findOrFail($id);

            $filePath = storage_path('app/public/' . $document->document_path);

            if (!file_exists($filePath)) {
                return response()->json([
                    'success' => false,
                    'message' => 'File tidak ditemukan'
                ], 404);
            }

            Log::info('Document downloaded', ['document_id' => $document->id, 'user_id' => $user->id]);

            return response()->download($filePath, $document->document_name);
        } catch (\Exception $e) {
            Log::error('Error downloading document: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal mendownload dokumen'
            ], 500);
        }
    }

    /**
     * Get Submission Status - AJAX untuk check status real-time
     */
    public function getSubmissionStatus(Request $request)
    {
        try {
            $user = Auth::user();
            $submissionId = $request->input('submission_id');

            $submission = Submission::where('user_id', $user->id)
                ->where('id', $submissionId)
                ->with(['guideline', 'payment'])
                ->first();

            if (!$submission) {
                return response()->json([
                    'success' => false,
                    'message' => 'Submission not found'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'status' => $submission->status,
                    'status_label' => $this->getStatusLabel($submission->status),
                    'progress_percentage' => $this->calculateProgressPercentage($submission->status),
                    'can_pay' => $submission->status === 'payment_pending' && $submission->guideline->fee > 0,
                    'can_download' => $submission->status === 'completed'
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Error getting submission status: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error getting status'
            ], 500);
        }
    }

    // ==================== HELPER METHODS ====================

    /**
     * Get status label in Indonesian
     */
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

    /**
     * Calculate progress percentage based on status
     */
    private function calculateProgressPercentage($status)
    {
        $progress = [
            'pending' => 10,
            'verified' => 25,
            'payment_pending' => 40,
            'paid' => 60,
            'processing' => 80,
            'completed' => 100,
            'rejected' => 0
        ];

        return $progress[$status] ?? 0;
    }

    /**
     * Format file size
     */
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
