<?php

namespace App\Http\Controllers;

use App\Models\Submission;
use App\Models\Guideline;
use App\Models\Payment;
use App\Models\GeneratedDocument;
use App\Models\SubmissionHistory;
use App\Models\SubmissionFile;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
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
                'in_process' => $submissions->whereIn('status', ['Diproses', 'verified', 'payment_pending', 'proof_uploaded', 'paid', 'processing'])->count(),
                'completed' => $submissions->where('status', 'completed')->count(),
                'rejected' => $submissions->where('status', 'rejected')->count(),
                'total' => $submissions->count()
            ];

            // Recent activities - last 5 submissions dengan safe fallbacks
            $recentActivities = $submissions->take(5)->map(function ($submission) {
                return [
                    'id' => $submission->id,
                    'submission_number' => $submission->submission_number ?? 'SUB-' . str_pad($submission->id, 4, '0', STR_PAD_LEFT),
                    'guideline' => [
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

            return view('user.dashboard', compact('submissions', 'stats', 'recentActivities', 'guidelines'));
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
            $submissions = collect();

            return view('user.dashboard', compact('submissions', 'stats', 'recentActivities', 'guidelines'))->with('error', 'Error loading dashboard: ' . $e->getMessage());
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
                    $guideline = $submission->guideline;
                    return [
                        'id' => $submission->id,
                        'submission_number' => $submission->submission_number ?? 'SUB-' . str_pad($submission->id, 4, '0', STR_PAD_LEFT),
                        'guideline' => [
                            'title' => $guideline ? $guideline->title : 'N/A',
                            'type' => $guideline ? $guideline->type : 'non_pnbp',
                            'fee' => $guideline ? $guideline->fee : 0
                        ],
                        'status' => $submission->status,
                        'type' => $guideline ? $guideline->type : 'non_pnbp', // Untuk filtering di frontend
                        'created_at' => $submission->created_at->format('d/m/Y'),
                        'rejection_note' => $submission->rejection_note ?: '',
                        'payment' => $submission->payment ? [
                            'amount' => $submission->payment->amount,
                            'status' => $submission->payment->status,
                            'rejection_reason' => $submission->payment->rejection_reason ?: null,
                            'e_billing_path' => $submission->payment->e_billing_path,
                            'e_billing_filename' => $submission->payment->e_billing_filename
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
            Log::info('Loading submission detail', ['submission_id' => $id, 'user_id' => Auth::id()]);

            $user = Auth::user();
            $submission = Submission::where('user_id', $user->id)
                ->where('id', $id)
                ->with(['guideline', 'payment', 'histories' => function ($query) {
                    $query->with(['actor' => function ($q) {
                        $q->select('id', 'name');
                    }])->orderBy('created_at', 'desc')->limit(10);
                }, 'generatedDocuments' => function ($query) {
                    $query->orderBy('created_at', 'desc')->limit(10);
                }, 'files' => function ($query) {
                    $query->orderBy('created_at', 'desc')->limit(10);
                }])
                ->first();

            if (!$submission) {
                Log::warning('Submission not found for user', ['submission_id' => $id, 'user_id' => $user->id]);
                return response()->json(['success' => false, 'message' => 'Pengajuan tidak ditemukan'], 404);
            }

            Log::info('Submission found', ['submission_id' => $submission->id, 'status' => $submission->status]);

            $guideline = $submission->guideline;

            // Safe handling for guideline data
            $guidelineData = [
                'title' => 'N/A',
                'description' => 'N/A',
                'type' => 'non_pnbp',
                'fee' => 0,
                'required_documents' => []
            ];

            if ($guideline) {
                $guidelineData = [
                    'title' => $guideline->title ?: 'N/A',
                    'description' => Str::limit($guideline->description ?: 'N/A', 300),
                    'type' => $guideline->type ?: 'non_pnbp',
                    'fee' => $guideline->fee ?: 0
                ];
            }

            // Format data untuk response
            $submissionData = [
                'id' => $submission->id,
                'submission_number' => $submission->submission_number ?: 'SUB-' . str_pad($submission->id, 4, '0', STR_PAD_LEFT),
                'guideline' => $guidelineData,
                'purpose' => Str::limit($submission->purpose ?: '', 500),
                'start_date' => $this->safeDateFormat($submission->start_date, 'Y-m-d') ?: '',
                'end_date' => $this->safeDateFormat($submission->end_date, 'Y-m-d') ?: '',
                'status' => $submission->status ?: 'pending',
                'status_label' => $this->getStatusLabel($submission->status ?: 'pending'),
                'created_at' => $this->safeDateFormat($submission->created_at, 'd/m/Y H:i') ?: now()->format('d/m/Y H:i'),
                'payment' => null,
                'uploaded_files' => [],
                'documents' => [],
                'histories' => []
            ];

            // Safe payment data handling
            if ($submission->payment) {
                $submissionData['payment'] = [
                    'amount' => $submission->payment->amount ?: 0,
                    'status' => $submission->payment->status ?: 'pending',
                    'method' => $submission->payment->payment_method ?: null,
                    'reference' => $submission->payment->payment_reference ?: null,
                    'paid_at' => $this->safeDateFormat($submission->payment->paid_at, 'd/m/Y H:i'),
                    'e_billing_path' => $submission->payment->e_billing_path ?: null,
                    'e_billing_filename' => $submission->payment->e_billing_filename ?: null
                ];
            }

            // Safe uploaded files handling
            if ($submission->files) {
                $submissionData['uploaded_files'] = $submission->files->filter(function ($file) {
                    return $file->id && $file->file_path;
                })->map(function ($file) use ($submission) {
                    try {
                        return [
                            'id' => $file->id,
                            'name' => Str::limit($file->file_name ?: 'Unknown', 100),
                            'document_name' => Str::limit($file->document_name ?: 'Document', 100),
                            'type' => $file->file_type ?: 'application/octet-stream',
                            'size' => $this->formatFileSize($file->file_size ?: 0),
                            'download_url' => route('submission.file.download', ['submissionId' => $submission->id, 'fileId' => $file->id]),
                            'uploaded_at' => $this->safeDateFormat($file->created_at, 'd/m/Y H:i') ?: now()->format('d/m/Y H:i')
                        ];
                    } catch (\Exception $routeException) {
                        Log::warning('Error generating download URL for file', [
                            'file_id' => $file->id,
                            'submission_id' => $submission->id,
                            'error' => $routeException->getMessage()
                        ]);
                        return [
                            'id' => $file->id,
                            'name' => Str::limit($file->file_name ?: 'Unknown', 100),
                            'document_name' => Str::limit($file->document_name ?: 'Document', 100),
                            'type' => $file->file_type ?: 'application/octet-stream',
                            'size' => $this->formatFileSize($file->file_size ?: 0),
                            'download_url' => '#',
                            'uploaded_at' => $this->safeDateFormat($file->created_at, 'd/m/Y H:i') ?: now()->format('d/m/Y H:i')
                        ];
                    }
                })->toArray();
            }

            // Safe generated documents handling
            if ($submission->generatedDocuments) {
                $submissionData['documents'] = $submission->generatedDocuments->filter(function ($doc) {
                    return $doc->id && $doc->document_path;
                })->map(function ($doc) use ($submission) {
                    return [
                        'id' => $doc->id,
                        'name' => Str::limit($doc->document_name ?: 'Document', 100),
                        'type' => $doc->document_type ?: 'Unknown',
                        'size' => $this->formatFileSize($doc->file_size ?: 0),
                        'download_url' => '/storage/' . $doc->document_path
                    ];
                })->toArray();
            }

            // Safe histories handling
            if ($submission->histories) {
                $submissionData['histories'] = $submission->histories->map(function ($history) {
                    return [
                        'title' => $history->title ?: 'Unknown Action',
                        'description' => Str::limit($history->description ?: 'No description', 200),
                        'actor' => $history->actor ? $history->actor->name : ($history->actor_type === 'admin' ? 'Admin' : 'System'),
                        'created_at' => $this->safeDateFormat($history->created_at, 'd/m/Y H:i') ?: now()->format('d/m/Y H:i')
                    ];
                })->toArray();
            }

            Log::info('Submission detail loaded successfully', ['submission_id' => $submission->id]);

            return response()->json([
                'success' => true,
                'data' => $submissionData
            ]);
        } catch (\Exception $e) {
            Log::error('Error loading submission detail', [
                'submission_id' => $id,
                'user_id' => Auth::id(),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error loading submission details: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store Submission - FIXED dengan pencegahan double submit menyeluruh
     */
    public function storeSubmission(Request $request)
    {
        try {
            $request->validate([
                'guideline_id' => 'required|exists:guidelines,id',
                'purpose' => 'required|string|max:1000',
                'start_date' => 'required|date',
                'end_date' => 'required|date|after_or_equal:start_date',
                'files.*' => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:5120'
            ]);

            $user = Auth::user();

            // CEK DOUBLE SUBMIT MENYELURUH: Pencegahan multi-level
            // 1. Check recent submissions with same guideline in last 30 seconds
            $recentSubmission = Submission::where('user_id', $user->id)
                ->where('guideline_id', $request->guideline_id)
                ->where('status', 'pending')
                ->where('created_at', '>=', now()->subSeconds(30))
                ->first();

            if ($recentSubmission) {
                Log::warning('Double submit attempt detected - Recent submission', [
                    'user_id' => $user->id,
                    'guideline_id' => $request->guideline_id,
                    'recent_submission_id' => $recentSubmission->id,
                    'recent_submission_created' => $recentSubmission->created_at
                ]);

                return response()->json([
                    'success' => false,
                    'message' => 'Pengajuan serupa sudah dikirim dalam 30 detik terakhir. Silakan tunggu sebentar sebelum mengajukan lagi.'
                ], 429);
            }

            // 2. Check for identical submission data in last 60 seconds (content-based duplicate prevention)
            $identicalSubmission = Submission::where('user_id', $user->id)
                ->where('guideline_id', $request->guideline_id)
                ->where('purpose', $request->purpose)
                ->where('start_date', $request->start_date)
                ->where('end_date', $request->end_date)
                ->where('created_at', '>=', now()->subMinutes(1))
                ->first();

            if ($identicalSubmission) {
                Log::warning('Double submit attempt detected - Identical content', [
                    'user_id' => $user->id,
                    'guideline_id' => $request->guideline_id,
                    'identical_submission_id' => $identicalSubmission->id,
                    'purpose_hash' => md5($request->purpose)
                ]);

                return response()->json([
                    'success' => false,
                    'message' => 'Pengajuan dengan data identik sudah dikirim. Silakan periksa riwayat pengajuan Anda.'
                ], 429);
            }

            $guideline = Guideline::findOrFail($request->guideline_id);

            // Decode required documents safely
            $requiredDocs = safe_json_decode($guideline->required_documents, []);

            // Generate UNIQUE submission number dengan atomic increment
            try {
                // Use a simpler approach - get the next ID from database
                $lastSubmission = Submission::orderBy('id', 'desc')->first();
                $nextNumber = $lastSubmission ? $lastSubmission->id + 1 : 1;

                $submissionNumber = 'BMKG-' . strtoupper($guideline->type) . '-' .
                    date('md') . '-' . date('Y') . '-' .
                    str_pad($nextNumber, 4, '0', STR_PAD_LEFT);

                // Double-check uniqueness
                $existing = Submission::where('submission_number', $submissionNumber)->exists();
                if ($existing) {
                    // Fallback: use timestamp-based unique number
                    $submissionNumber = 'BMKG-' . strtoupper($guideline->type) . '-' .
                        date('mdHis') . '-' . str_pad($user->id, 3, '0', STR_PAD_LEFT);
                }
            } catch (\Exception $e) {
                throw new \Exception('Gagal generate nomor pengajuan unik: ' . $e->getMessage());
            }

            // Create submission with additional safety checks
            $submissionData = [
                'user_id' => $user->id,
                'guideline_id' => $guideline->id,
                'submission_number' => $submissionNumber,
                'type' => $guideline->type,
                'purpose' => $request->purpose,
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
                'status' => 'pending',
                'created_at' => now(),
                'updated_at' => now()
            ];

            // Use DB transaction for atomic operation
            DB::beginTransaction();
            $submissionId = DB::table('submissions')->insertGetId($submissionData);

            if (!$submissionId) {
                throw new \Exception('Gagal menyimpan pengajuan ke database. Silakan coba lagi.');
            }

            // Load the submission model
            $submission = Submission::find($submissionId);
            if (!$submission) {
                throw new \Exception('Gagal memuat data pengajuan setelah penyimpanan.');
            }

            // Handle file uploads based on required documents
            $expectedFileCount = count($requiredDocs);
            $uploadedFiles = $request->file('files', []);

            if ($expectedFileCount > 0 && count($uploadedFiles) !== $expectedFileCount) {
                throw new \Exception("Jumlah file yang diupload tidak sesuai. Diperlukan {$expectedFileCount} file untuk layanan ini.");
            }

            foreach ($uploadedFiles as $index => $file) {
                $documentName = isset($requiredDocs[$index]) ? $requiredDocs[$index] : 'Dokumen ' . ($index + 1);
                $path = $file->store('submissions/' . $submission->id, 'public');

                // Create file record using SubmissionFile model
                SubmissionFile::create([
                    'submission_id' => $submission->id,
                    'file_name' => $file->getClientOriginalName(),
                    'file_path' => $path,
                    'file_type' => $file->getClientMimeType(),
                    'file_size' => $file->getSize(),
                    'document_name' => $documentName
                ]);
            }

            // Create history
            SubmissionHistory::create([
                'submission_id' => $submission->id,
                'actor_id' => $user->id,
                'actor_type' => 'user',
                'action' => 'submitted',
                'title' => 'Pengajuan Disubmit',
                'description' => 'Pengajuan telah disubmit dan menunggu verifikasi admin',
                'created_at' => now(),
                'updated_at' => now()
            ]);

            // Commit transaction
            DB::commit();

            // NOTE: Payment record creation is now handled by AdminController::uploadEBilling()
            // after admin verification, not automatically during user submission

            Log::info('Submission created successfully', [
                'submission_id' => $submission->id,
                'submission_number' => $submissionNumber,
                'user_id' => $user->id
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Pengajuan berhasil dikirim! Silakan tunggu verifikasi dari admin.',
                'data' => $submission->load('guideline')
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            Log::error('Validation error creating submission', ['errors' => $e->errors()]);
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal: ' . implode(', ', collect($e->errors())->flatten()->toArray())
            ], 422);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error creating submission', [
                'message' => $e->getMessage(),
                'user_id' => Auth::id(),
                'guideline_id' => $request->guideline_id ?? null,
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Gagal mengirim pengajuan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Upload Payment Proof - FIXED nama method sesuai route
     */
    public function uploadPayment(Request $request, $id)
    {
        try {
            Log::info('Starting payment upload', ['submission_id' => $id, 'user_id' => Auth::id()]);

            $request->validate([
                'payment_method' => 'required|string',
                'payment_reference' => 'nullable|string',
                'payment_proof' => 'required|file|mimes:jpg,jpeg,png,pdf|max:5120'
            ]);

            $user = Auth::user();
            $submission = Submission::where('user_id', $user->id)
                ->where('id', $id)
                ->firstOrFail();

            Log::info('Found submission', ['submission_id' => $submission->id, 'status' => $submission->status]);

            $payment = Payment::where('submission_id', $submission->id)->firstOrFail();

            Log::info('Found payment record', ['payment_id' => $payment->id, 'status' => $payment->status]);

            DB::beginTransaction();

            // Check if file was uploaded
            if (!$request->hasFile('payment_proof')) {
                throw new \Exception('File bukti pembayaran tidak ditemukan');
            }

            $file = $request->file('payment_proof');
            Log::info('File details', [
                'original_name' => $file->getClientOriginalName(),
                'mime_type' => $file->getMimeType(),
                'size' => $file->getSize()
            ]);

            // Upload payment proof
            $proofPath = $file->store('payments/' . $submission->id, 'public');
            Log::info('File stored at', ['path' => $proofPath]);

            // Update payment
            $payment->update([
                'payment_method' => $request->payment_method,
                'payment_reference' => $request->payment_reference,
                'payment_proof' => $proofPath,
                'status' => 'proof_uploaded',
                'paid_at' => now(),
                'rejection_reason' => null // Clear rejection reason after successful upload
            ]);

            // Update submission status to indicate proof has been uploaded but needs admin verification
            $submission->update(['status' => 'proof_uploaded']);

            // Create history
            SubmissionHistory::create([
                'submission_id' => $submission->id,
                'actor_id' => $user->id,
                'actor_type' => 'user',
                'action' => 'payment_uploaded',
                'title' => 'Bukti Pembayaran Diupload',
                'description' => 'User telah mengupload bukti pembayaran dengan metode: ' . $request->payment_method,
                'created_at' => now(),
                'updated_at' => now()
            ]);

            DB::commit();

            Log::info('Payment proof uploaded successfully', ['submission_id' => $submission->id, 'payment_id' => $payment->id]);

            return response()->json([
                'success' => true,
                'message' => 'Bukti pembayaran berhasil diupload'
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Validation error uploading payment', ['errors' => $e->errors()]);
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal: ' . implode(', ', collect($e->errors())->flatten()->toArray())
            ], 422);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error uploading payment', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'submission_id' => $id,
                'user_id' => Auth::id()
            ]);

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
            /** @var \App\Models\User $user */
            $user = Auth::user();

            // Get submissions with safe handling
            $submissions = $user->submissions()
                ->with(['guideline', 'payment', 'histories' => function ($q) {
                    $q->with('actor')->orderBy('created_at', 'desc');
                }])
                ->orderBy('created_at', 'desc')
                ->get()
                ->map(function ($submission) {
                    $guideline = $submission->guideline;
                    return [
                        'id' => $submission->id,
                        'submission_number' => $submission->submission_number ?? 'SUB-' . str_pad($submission->id, 4, '0', STR_PAD_LEFT),
                        'guideline' => [
                            'title' => $guideline ? $guideline->title : 'N/A',
                            'type' => $guideline ? $guideline->type : 'non_pnbp',
                            'fee' => $guideline ? $guideline->fee : 0
                        ],
                        'status' => $submission->status,
                        'status_label' => $this->getStatusLabel($submission->status),
                        'type_label' => ($guideline ? $guideline->type : 'non_pnbp') === 'pnbp' ? 'PNBP (Berbayar)' : 'Non-PNBP (Gratis)',
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

            /** @var \App\Models\User $user */
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

            /** @var \App\Models\User $user */
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

            return response()->download($filePath, $document->document_name, [
                'Content-Type' => $document->mime_type ?: mime_content_type($filePath),
                'Content-Disposition' => 'attachment; filename="' . $document->document_name . '"'
            ]);
        } catch (\Exception $e) {
            Log::error('Error downloading document: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal mendownload dokumen'
            ], 500);
        }
    }

    /**
     * Download Generated Document
     */
    public function downloadGeneratedDocument($submissionId, $documentId)
    {
        try {
            $user = Auth::user();

            // Get document through user's submissions
            $document = GeneratedDocument::whereHas('submission', function ($q) use ($user) {
                $q->where('user_id', $user->id);
            })->where('id', $documentId)->firstOrFail();

            if (!$document->document_path || !Storage::disk('public')->exists($document->document_path)) {
                return response()->json([
                    'success' => false,
                    'message' => 'File tidak ditemukan'
                ], 404);
            }

            // Use the actual filename from storage path to ensure correct extension
            $downloadName = $document->file_name ?: basename($document->document_path);

            $filePath = storage_path('app/public/' . $document->document_path);

            Log::info('Generated document downloaded', [
                'document_id' => $document->id,
                'submission_id' => $submissionId,
                'user_id' => $user->id
            ]);

            return response()->download($filePath, $downloadName, [
                'Content-Type' => $document->mime_type ?: mime_content_type($filePath),
                'Content-Disposition' => 'attachment; filename="' . $downloadName . '"'
            ]);
        } catch (\Exception $e) {
            Log::error('Error downloading generated document: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal mendownload dokumen'
            ], 500);
        }
    }

    /**
     * Upload Files to Existing Submission (for rejected submissions)
     */
    public function uploadFilesToSubmission(Request $request, $id)
    {
        try {
            $request->validate([
                'files.*' => 'required|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:5120',
                'document_names.*' => 'required|string|max:255'
            ]);

            $user = Auth::user();
            $submission = Submission::where('user_id', $user->id)
                ->where('id', $id)
                ->firstOrFail();

            // Only allow uploading to rejected submissions
            if ($submission->status !== 'rejected') {
                return response()->json([
                    'success' => false,
                    'message' => 'Hanya pengajuan yang ditolak yang dapat diupload ulang'
                ], 403);
            }

            $uploadedFiles = $request->file('files', []);
            $documentNames = $request->input('document_names', []);

            DB::beginTransaction();

            foreach ($uploadedFiles as $index => $file) {
                $documentName = $documentNames[$index] ?? 'Dokumen ' . ($index + 1);
                $path = $file->store('submissions/' . $submission->id, 'public');

                // Create file record
                SubmissionFile::create([
                    'submission_id' => $submission->id,
                    'file_name' => $file->getClientOriginalName(),
                    'file_path' => $path,
                    'file_type' => $file->getClientMimeType(),
                    'file_size' => $file->getSize(),
                    'document_name' => $documentName
                ]);
            }

            // Log history
            SubmissionHistory::create([
                'submission_id' => $submission->id,
                'actor_id' => $user->id,
                'actor_type' => 'user',
                'action' => 'files_uploaded',
                'title' => 'File Ditambahkan',
                'description' => 'User menambahkan file baru ke pengajuan yang ditolak',
                'created_at' => now(),
                'updated_at' => now()
            ]);

            DB::commit();

            Log::info('Files uploaded to rejected submission', [
                'submission_id' => $submission->id,
                'user_id' => $user->id,
                'files_count' => count($uploadedFiles)
            ]);

            return response()->json([
                'success' => true,
                'message' => 'File berhasil diupload'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error uploading files to submission: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Gagal upload file: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Resubmit Rejected Submission
     */
    public function resubmitSubmission(Request $request, $id)
    {
        try {
            $user = Auth::user();
            $submission = Submission::where('user_id', $user->id)
                ->where('id', $id)
                ->firstOrFail();

            // Only allow resubmitting rejected submissions
            if ($submission->status !== 'rejected') {
                return response()->json([
                    'success' => false,
                    'message' => 'Hanya pengajuan yang ditolak yang dapat dikirim ulang'
                ], 403);
            }

            DB::beginTransaction();

            // Update submission status
            $submission->update([
                'status' => 'pending',
                'rejection_note' => null // Clear rejection note
            ]);

            // Log history
            SubmissionHistory::create([
                'submission_id' => $submission->id,
                'actor_id' => $user->id,
                'actor_type' => 'user',
                'action' => 'resubmitted',
                'title' => 'Pengajuan Dikirim Ulang',
                'description' => 'User mengirim ulang pengajuan yang sebelumnya ditolak',
                'created_at' => now(),
                'updated_at' => now()
            ]);

            DB::commit();

            Log::info('Submission resubmitted', [
                'submission_id' => $submission->id,
                'user_id' => $user->id
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Pengajuan berhasil dikirim ulang'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error resubmitting submission: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Gagal mengirim ulang pengajuan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Download Uploaded File
     */
    public function downloadUploadedFile($submissionId, $fileId)
    {
        try {
            $user = Auth::user();

            // Get submission and ensure it belongs to the user
            $submission = Submission::where('user_id', $user->id)
                ->where('id', $submissionId)
                ->firstOrFail();

            // Get the specific file from the submission
            $file = $submission->files()->where('id', $fileId)->firstOrFail();

            $filePath = storage_path('app/public/' . $file->file_path);

            if (!file_exists($filePath)) {
                return response()->json([
                    'success' => false,
                    'message' => 'File tidak ditemukan'
                ], 404);
            }

            Log::info('Uploaded file downloaded', [
                'submission_id' => $submission->id,
                'file_id' => $file->id,
                'user_id' => $user->id
            ]);

            return response()->download($filePath, $file->file_name, [
                'Content-Type' => $file->file_type ?: mime_content_type($filePath),
                'Content-Disposition' => 'attachment; filename="' . $file->file_name . '"'
            ]);
        } catch (\Exception $e) {
            Log::error('Error downloading uploaded file: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal mendownload file'
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

            $guideline = $submission->guideline;
            return response()->json([
                'success' => true,
                'data' => [
                    'status' => $submission->status,
                    'status_label' => $this->getStatusLabel($submission->status),
                    'progress_percentage' => $this->calculateProgressPercentage($submission->status),
                    'can_pay' => ($submission->status === 'payment_pending' || $submission->status === 'proof_uploaded') && $guideline && $guideline->fee > 0,
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
            'pending' => 'Menunggu Review',
            'Diproses' => 'Menunggu Upload e-Billing',
            'verified' => 'Terverifikasi',
            'payment_pending' => 'Menunggu Pembayaran',
            'proof_uploaded' => 'Bukti Pembayaran Diupload',
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
            'proof_uploaded' => 55,
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

    /**
     * Safely format date
     */
    private function safeDateFormat($date, $format)
    {
        if (!$date) return null;
        try {
            return \Carbon\Carbon::parse($date)->format($format);
        } catch (\Exception $e) {
            return null;
        }
    }
}
