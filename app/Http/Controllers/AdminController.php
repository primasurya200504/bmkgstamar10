<?php

namespace App\Http\Controllers;

use App\Models\Application;
use App\Models\ApplicationHistory;
use App\Models\Guideline;
use App\Models\Payment;
use App\Models\Archive;
use App\Models\User;
use App\Models\GeneratedDocument;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use ZipArchive;

class AdminController extends Controller
{
    /**
     * Dashboard Admin - Tampilkan statistik dan aplikasi terbaru
     */
    public function dashboard()
    {
        try {
            $stats = [
                'pending_requests' => Application::where('status', 'pending')->count(),
                'pending_payments' => Application::where('status', 'payment_pending')->count(),
                'processing' => Application::where('status', 'processing')->count(),
                'completed' => Application::where('status', 'completed')->count(),
                'total_users' => User::where('role', 'user')->count()
            ];

            // Enhanced recent applications dengan computed attributes
            $recent_applications = Application::with(['user', 'guideline'])
                ->orderBy('created_at', 'desc')
                ->take(10)
                ->get();

            // Tambahkan computed attributes
            $recent_applications->each(function ($app) {
                $app->type_label = $app->type === 'pnbp' ? 'PNBP (Berbayar)' : 'Non-PNBP (Gratis)';
                $app->status_label = $this->getStatusLabel($app->status);
                $app->created_at_formatted = $app->created_at->format('d/m/Y H:i');
                $app->date_range_display = ($app->start_date && $app->end_date)
                    ? Carbon::parse($app->start_date)->format('d/m/Y') . ' - ' . Carbon::parse($app->end_date)->format('d/m/Y')
                    : 'Tanggal tidak tersedia';
                $app->duration_days = ($app->start_date && $app->end_date)
                    ? Carbon::parse($app->start_date)->diffInDays(Carbon::parse($app->end_date)) + 1
                    : 0;
                $app->documents_count = is_array($app->documents) ? count($app->documents) : 0;
            });

            return view('admin.admin_dashboard', compact('stats', 'recent_applications'));
        } catch (\Exception $e) {
            Log::error('Dashboard error: ' . $e->getMessage());

            // Fallback data jika ada error
            $stats = [
                'pending_requests' => 0,
                'pending_payments' => 0,
                'processing' => 0,
                'completed' => 0,
                'total_users' => 0
            ];
            $recent_applications = collect([]);

            return view('admin.admin_dashboard', compact('stats', 'recent_applications'));
        }
    }

    /**
     * PENTING: Method untuk mendapatkan detail aplikasi (untuk modal review)
     */
    public function getApplicationDetail($id)
    {
        try {
            $application = Application::with([
                'user:id,name,email,phone',
                'guideline:id,title,description,type,fee,required_documents',
                'histories' => function ($query) {
                    $query->orderBy('created_at', 'desc')->take(10);
                },
                'payment',
                'generatedDocuments'
            ])->findOrFail($id);

            // Parse date context dari notes jika ada
            $dateContext = null;
            if ($application->notes) {
                try {
                    $dateContext = json_decode($application->notes, true);
                } catch (\Exception $e) {
                    Log::warning('Failed to parse application notes JSON', [
                        'application_id' => $id,
                        'notes' => $application->notes,
                        'error' => $e->getMessage()
                    ]);
                }
            }

            // Calculate duration dan format dates
            $startDate = $application->start_date ? Carbon::parse($application->start_date) : null;
            $endDate = $application->end_date ? Carbon::parse($application->end_date) : null;
            $duration = ($startDate && $endDate) ? $startDate->diffInDays($endDate) + 1 : 0;

            // Get document details from JSON
            $documentsDetail = [];
            if ($application->documents && is_array($application->documents)) {
                $documentsDetail = $application->documents;
            }

            // Build comprehensive response data
            $responseData = [
                'id' => $application->id,
                'application_number' => $application->application_number,
                'user' => [
                    'id' => $application->user->id,
                    'name' => $application->user->name,
                    'email' => $application->user->email,
                    'phone' => $application->user->phone ?? 'Tidak ada'
                ],
                'guideline' => [
                    'id' => $application->guideline->id,
                    'title' => $application->guideline->title,
                    'description' => $application->guideline->description,
                    'fee' => $application->guideline->fee,
                    'type' => $application->guideline->type ?? $application->type
                ],
                'type' => $application->type,
                'type_label' => $application->type === 'pnbp' ? 'PNBP (Berbayar)' : 'Non-PNBP (Gratis)',
                'start_date' => $application->start_date,
                'start_date_formatted' => $startDate ? $startDate->format('d/m/Y') : 'Tidak ada',
                'end_date' => $application->end_date,
                'end_date_formatted' => $endDate ? $endDate->format('d/m/Y') : 'Tidak ada',
                'duration_days' => $duration,
                'purpose' => $application->purpose,
                'status' => $application->status,
                'status_label' => $this->getStatusLabel($application->status),
                'created_at' => $application->created_at->format('d/m/Y H:i'),
                'updated_at' => $application->updated_at->format('d/m/Y H:i'),
                'documents' => $documentsDetail,
                'documents_count' => count($documentsDetail),
                'payment' => $application->payment ? [
                    'id' => $application->payment->id,
                    'amount' => $application->payment->amount,
                    'amount_formatted' => 'Rp ' . number_format($application->payment->amount, 0, ',', '.'),
                    'status' => $application->payment->status,
                    'payment_proof' => $application->payment->payment_proof,
                    'paid_at' => $application->payment->paid_at ?
                        Carbon::parse($application->payment->paid_at)->format('d/m/Y H:i') : null
                ] : null,
                'generated_documents' => $application->generatedDocuments->map(function ($doc) {
                    return [
                        'id' => $doc->id,
                        'document_name' => $doc->document_name,
                        'document_type' => $doc->document_type ?? 'document',
                        'document_path' => $doc->document_path,
                        'created_at' => $doc->created_at->format('d/m/Y H:i')
                    ];
                }),
                'histories' => $application->histories->map(function ($history) {
                    return [
                        'id' => $history->id,
                        'action' => $history->action,
                        'title' => $history->title ?? $this->getHistoryTitle($history->action),
                        'description' => $history->description,
                        'actor_type' => $history->actor_type,
                        'actor_name' => $history->actor ? $history->actor->name : 'System',
                        'created_at' => $history->created_at->format('d/m/Y H:i'),
                        'metadata' => $history->metadata
                    ];
                }),
                'date_context' => $dateContext,
                'is_historical' => $endDate && $endDate->lt(Carbon::parse('1990-01-01')),
                'is_future' => $startDate && $startDate->gt(Carbon::now()),
                'is_mixed' => ($startDate && $endDate) ?
                    ($startDate->lte(Carbon::now()) && $endDate->gt(Carbon::now())) : false,
                'fee_required' => $application->type === 'pnbp' && $application->guideline->fee > 0,
                'can_be_processed' => in_array($application->status, ['pending', 'verified', 'payment_pending']),
                'processing_notes' => $application->notes ?
                    (is_string($application->notes) ? $application->notes : null) : null
            ];

            return response()->json([
                'success' => true,
                'data' => $responseData
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            Log::warning('Application not found for detail view', [
                'application_id' => $id,
                'user_id' => Auth::id()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Aplikasi tidak ditemukan'
            ], 404);
        } catch (\Exception $e) {
            Log::error('Failed to get application detail', [
                'application_id' => $id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat memuat detail aplikasi'
            ], 500);
        }
    }

    /**
     * Manajemen Permintaan - List semua aplikasi
     */
    public function requests()
    {
        try {
            $applications = Application::with(['user', 'guideline', 'payment'])
                ->orderBy('created_at', 'desc')
                ->get();

            // Transform data untuk konsistensi
            $applications->transform(function ($app) {
                $app->type_label = $app->type === 'pnbp' ? 'PNBP (Berbayar)' : 'Non-PNBP (Gratis)';
                $app->status_label = $this->getStatusLabel($app->status);
                $app->created_at_formatted = $app->created_at->format('d/m/Y H:i');
                $app->documents_count = is_array($app->documents) ? count($app->documents) : 0;
                $app->user_name = $app->user ? $app->user->name : 'N/A';
                $app->guideline_title = $app->guideline ? $app->guideline->title : 'N/A';
                return $app;
            });

            return response()->json([
                'success' => true,
                'data' => $applications
            ]);
        } catch (\Exception $e) {
            Log::error('Requests loading error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => 'Failed to load requests: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Verifikasi Request (Approve/Reject)
     */
    public function verifyRequest(Request $request, $id)
    {
        try {
            $application = Application::findOrFail($id);

            // Validate request
            $request->validate([
                'action' => 'required|in:approve,reject',
                'notes' => 'nullable|string|max:1000'
            ]);

            if ($request->action === 'approve') {
                if ($application->type === 'pnbp' && $application->guideline && $application->guideline->fee > 0) {
                    // PNBP dengan biaya -> ke payment_pending
                    $application->update([
                        'status' => 'payment_pending',
                        'notes' => $request->notes
                    ]);

                    // Log approval dengan payment
                    $application->logHistory(
                        'approved_with_payment',
                        'admin',
                        Auth::id(),
                        'Pengajuan Disetujui - Menunggu Pembayaran',
                        "Admin menyetujui pengajuan. Biaya PNBP: Rp " . number_format($application->guideline->fee) . ". " . ($request->notes ?: ''),
                        [
                            'admin_notes' => $request->notes,
                            'fee_amount' => $application->guideline->fee,
                            'payment_required' => true,
                            'approved_by' => Auth::user()->name,
                            'approved_at' => now()
                        ]
                    );

                    // Create atau update payment record
                    $existingPayment = Payment::where('application_id', $application->id)->first();
                    if (!$existingPayment) {
                        Payment::create([
                            'application_id' => $application->id,
                            'amount' => $application->guideline->fee,
                            'status' => 'pending'
                        ]);
                    }
                } else {
                    // Non-PNBP atau PNBP gratis -> langsung ke paid
                    $application->update([
                        'status' => 'paid',
                        'notes' => $request->notes
                    ]);

                    // Log approval tanpa payment
                    $application->logHistory(
                        'approved_no_payment',
                        'admin',
                        Auth::id(),
                        'Pengajuan Disetujui - Non-PNBP',
                        'Admin menyetujui pengajuan Non-PNBP (gratis untuk penelitian/akademik). ' . ($request->notes ?: ''),
                        [
                            'admin_notes' => $request->notes,
                            'fee_amount' => 0,
                            'payment_required' => false,
                            'approved_by' => Auth::user()->name,
                            'approved_at' => now()
                        ]
                    );
                }
            } else {
                // Rejected
                $application->update([
                    'status' => 'rejected',
                    'notes' => $request->notes
                ]);

                // Log rejection
                $application->logHistory(
                    'rejected',
                    'admin',
                    Auth::id(),
                    'Pengajuan Ditolak',
                    'Admin menolak pengajuan dengan alasan: ' . ($request->notes ?: 'Tidak memenuhi persyaratan'),
                    [
                        'admin_notes' => $request->notes,
                        'rejection_reason' => $request->notes,
                        'rejected_by' => Auth::user()->name,
                        'rejected_at' => now()
                    ]
                );
            }

            return response()->json([
                'success' => true,
                'message' => 'Request berhasil ' . ($request->action === 'approve' ? 'disetujui' : 'ditolak')
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Verify request error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => 'Failed to verify request: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Manajemen Pembayaran
     */
    public function payments()
    {
        try {
            $payments = Payment::with(['application.user', 'application.guideline'])
                ->whereNotNull('payment_proof')
                ->orderBy('created_at', 'desc')
                ->get();

            // Transform data
            $payments->transform(function ($payment) {
                $payment->status_label = $this->getPaymentStatusLabel($payment->status);
                $payment->amount_formatted = 'Rp ' . number_format($payment->amount, 0, ',', '.');
                $payment->created_at_formatted = $payment->created_at->format('d/m/Y H:i');
                return $payment;
            });

            return response()->json([
                'success' => true,
                'data' => $payments
            ]);
        } catch (\Exception $e) {
            Log::error('Payments loading error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => 'Failed to load payments'
            ], 500);
        }
    }

    /**
     * Verifikasi Pembayaran
     */
    public function verifyPayment(Request $request, $id)
    {
        try {
            $request->validate([
                'action' => 'required|in:approve,reject',
                'notes' => 'nullable|string|max:500'
            ]);

            $payment = Payment::findOrFail($id);

            if ($request->action === 'approve') {
                $payment->update([
                    'status' => 'verified',
                    'paid_at' => now()
                ]);

                $payment->application->update(['status' => 'paid']);

                // Log payment verification
                $payment->application->logHistory(
                    'payment_verified',
                    'admin',
                    Auth::id(),
                    'Pembayaran Diverifikasi',
                    "Admin memverifikasi pembayaran PNBP sebesar Rp " . number_format($payment->amount),
                    [
                        'amount' => $payment->amount,
                        'payment_method' => 'Upload Bukti Transfer',
                        'verified_at' => now(),
                        'verified_by' => Auth::user()->name,
                        'payment_proof_file' => $payment->payment_proof,
                        'admin_notes' => $request->notes
                    ]
                );
            } else {
                $payment->update([
                    'status' => 'rejected'
                ]);

                $payment->application->update(['status' => 'payment_pending']);

                // Log payment rejection
                $payment->application->logHistory(
                    'payment_rejected',
                    'admin',
                    Auth::id(),
                    'Pembayaran Ditolak',
                    'Admin menolak bukti pembayaran: ' . ($request->notes ?: 'Bukti pembayaran tidak valid'),
                    [
                        'amount' => $payment->amount,
                        'rejection_reason' => $request->notes ?: 'Bukti pembayaran tidak valid',
                        'rejected_by' => Auth::user()->name,
                        'rejected_at' => now()
                    ]
                );
            }

            return response()->json([
                'success' => true,
                'message' => 'Payment berhasil ' . ($request->action === 'approve' ? 'diverifikasi' : 'ditolak')
            ]);
        } catch (\Exception $e) {
            Log::error('Verify payment error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => 'Failed to verify payment'
            ], 500);
        }
    }

    /**
     * Documents yang perlu upload
     */
    public function documents()
    {
        try {
            $applications = Application::with(['user', 'guideline'])
                ->where('status', 'paid')
                ->whereDoesntHave('generatedDocuments')
                ->orderBy('created_at', 'desc')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $applications
            ]);
        } catch (\Exception $e) {
            Log::error('Documents loading error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => 'Failed to load documents'
            ], 500);
        }
    }

    /**
     * Upload Document untuk User
     */
    public function uploadDocument(Request $request, $id)
    {
        try {
            $request->validate([
                'document' => 'required|file|mimes:pdf,doc,docx,xls,xlsx|max:10240', // 10MB
                'document_name' => 'required|string|max:255'
            ]);

            $application = Application::findOrFail($id);

            if ($application->status !== 'paid') {
                return response()->json([
                    'success' => false,
                    'message' => 'Aplikasi belum dalam status yang bisa diupload dokumen'
                ], 422);
            }

            if ($request->hasFile('document')) {
                $file = $request->file('document');
                $originalName = $file->getClientOriginalName();
                $extension = $file->getClientOriginalExtension();

                // Generate safe filename
                $filename = 'result_' . str_replace(['/', '\\', ' '], '_', $application->application_number) . '_' . time() . '.' . $extension;

                // Organized storage
                $folderPath = $application->type === 'pnbp' ? 'results/pnbp' : 'results/non_pnbp';
                $yearMonth = date('Y/m');
                $fullPath = $folderPath . '/' . $yearMonth;

                // Store file
                $path = $file->storeAs($fullPath, $filename, 'public');

                if (!$path) {
                    throw new \Exception('Gagal menyimpan file dokumen');
                }

                // Save to generated_documents table
                $generatedDoc = GeneratedDocument::create([
                    'application_id' => $application->id,
                    'document_path' => $path,
                    'document_name' => $request->document_name,
                    'document_type' => 'result',
                    'file_size' => $file->getSize(),
                    'mime_type' => $file->getMimeType(),
                    'uploaded_by' => Auth::id()
                ]);

                // Log document upload
                $application->logHistory(
                    'document_uploaded',
                    'admin',
                    Auth::id(),
                    'Dokumen Data/Surat Diupload',
                    "Admin mengupload dokumen '{$request->document_name}' untuk user",
                    [
                        'document_id' => $generatedDoc->id,
                        'document_name' => $request->document_name,
                        'file_path' => $path,
                        'storage_folder' => $fullPath,
                        'file_original_name' => $originalName,
                        'file_size' => $file->getSize(),
                        'mime_type' => $file->getMimeType(),
                        'uploaded_by' => Auth::user()->name
                    ]
                );

                // Update status ke completed
                $application->update(['status' => 'completed']);

                // Log completion
                $application->logHistory(
                    'completed',
                    'admin',
                    Auth::id(),
                    'Pengajuan Selesai',
                    'Pengajuan telah selesai diproses dan dokumen siap diunduh',
                    [
                        'completion_date' => now(),
                        'total_process_days' => $application->created_at->diffInDays(now()),
                        'completed_by' => Auth::user()->name,
                        'document_location' => $fullPath,
                        'final_document_count' => $application->generatedDocuments()->count()
                    ]
                );

                // Auto archive
                Archive::updateOrCreate(
                    ['application_id' => $application->id],
                    [
                        'archive_date' => now(),
                        'notes' => 'Auto archived on completion',
                        'archived_by' => Auth::id()
                    ]
                );

                return response()->json([
                    'success' => true,
                    'message' => 'Document uploaded successfully',
                    'document' => [
                        'id' => $generatedDoc->id,
                        'name' => $request->document_name,
                        'path' => $path,
                        'storage_folder' => $fullPath
                    ]
                ]);
            }

            throw new \Exception('File tidak ditemukan');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Document upload failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => 'Failed to upload document: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Complete Application (manual completion tanpa upload)
     */
    public function completeApplication(Request $request, $id)
    {
        try {
            $application = Application::findOrFail($id);

            if (!in_array($application->status, ['paid', 'processing'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Aplikasi tidak dalam status yang bisa diselesaikan'
                ], 422);
            }

            $application->update([
                'status' => 'completed',
                'notes' => $request->notes
            ]);

            // Log completion
            $application->logHistory(
                'completed_manual',
                'admin',
                Auth::id(),
                'Pengajuan Diselesaikan Manual',
                'Admin menyelesaikan pengajuan secara manual tanpa upload dokumen. ' . ($request->notes ?: ''),
                [
                    'completion_type' => 'manual',
                    'completion_date' => now(),
                    'total_process_days' => $application->created_at->diffInDays(now()),
                    'completed_by' => Auth::user()->name,
                    'admin_notes' => $request->notes
                ]
            );

            return response()->json([
                'success' => true,
                'message' => 'Application completed successfully'
            ]);
        } catch (\Exception $e) {
            Log::error('Complete application error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => 'Failed to complete application'
            ], 500);
        }
    }

    /**
     * Archives dengan filtering
     */
    public function archives(Request $request)
    {
        try {
            $query = Application::with([
                'user',
                'guideline',
                'histories.actor',
                'payment',
                'generatedDocuments'
            ])->where('status', 'completed');

            // Apply filters
            if ($request->filled('month')) {
                $query->whereMonth('created_at', $request->month);
            }

            if ($request->filled('year')) {
                $query->whereYear('created_at', $request->year);
            }

            if ($request->filled('type')) {
                $query->where('type', $request->type);
            }

            $applications = $query->orderBy('created_at', 'desc')
                ->get();

            // Transform data
            $applications->transform(function ($app) {
                $app->type_label = $app->type === 'pnbp' ? 'PNBP (Berbayar)' : 'Non-PNBP (Gratis)';
                $app->completed_at_formatted = $app->updated_at->format('d/m/Y H:i');
                $app->process_duration = $app->created_at->diffInDays($app->updated_at);
                return $app;
            });

            return response()->json([
                'success' => true,
                'data' => $applications
            ]);
        } catch (\Exception $e) {
            Log::error('Archives loading error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => 'Failed to load archives'
            ], 500);
        }
    }

    /**
     * Get Application Timeline
     */
    public function getApplicationTimeline($id)
    {
        try {
            $application = Application::with([
                'user',
                'guideline',
                'histories.actor',
                'payment',
                'generatedDocuments'
            ])->findOrFail($id);

            $timeline = $application->histories()
                ->with('actor')
                ->orderBy('created_at', 'asc')
                ->get()
                ->map(function ($history) {
                    return [
                        'id' => $history->id,
                        'action' => $history->action,
                        'title' => $history->title ?? $this->getHistoryTitle($history->action),
                        'description' => $history->description,
                        'actor_type' => $history->actor_type,
                        'actor_name' => $history->actor ? $history->actor->name : 'System',
                        'created_at' => $history->created_at,
                        'created_at_formatted' => $history->created_at->format('d/m/Y H:i'),
                        'created_at_relative' => $history->created_at->diffForHumans(),
                        'metadata' => $history->metadata
                    ];
                });

            return response()->json([
                'success' => true,
                'application' => $application,
                'timeline' => $timeline
            ]);
        } catch (\Exception $e) {
            Log::error('Timeline loading error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => 'Failed to load timeline'
            ], 500);
        }
    }

    /**
     * Download Archive ZIP
     */
    public function downloadArchive($id)
    {
        try {
            $application = Application::with(['generatedDocuments', 'payment', 'user'])->findOrFail($id);

            // Create temporary directory
            $tempDir = storage_path('app/temp/archives');
            if (!file_exists($tempDir)) {
                mkdir($tempDir, 0755, true);
            }

            // Create ZIP file
            $zip = new ZipArchive();
            $zipFileName = "archive_{$application->application_number}_" . now()->format('Y-m-d_H-i-s') . ".zip";
            $zipPath = $tempDir . '/' . $zipFileName;

            if ($zip->open($zipPath, ZipArchive::CREATE) === TRUE) {
                // Add application info as JSON
                $appInfo = [
                    'application_number' => $application->application_number,
                    'user_name' => $application->user->name,
                    'user_email' => $application->user->email,
                    'guideline' => $application->guideline->title ?? 'N/A',
                    'type' => $application->type,
                    'start_date' => $application->start_date,
                    'end_date' => $application->end_date,
                    'purpose' => $application->purpose,
                    'status' => $application->status,
                    'created_at' => $application->created_at,
                    'completed_at' => $application->updated_at,
                    'archived_at' => now(),
                    'archived_by' => Auth::user()->name
                ];
                $zip->addFromString('application_info.json', json_encode($appInfo, JSON_PRETTY_PRINT));

                // Add original user documents
                if ($application->documents && is_array($application->documents)) {
                    foreach ($application->documents as $index => $doc) {
                        if (isset($doc['path'])) {
                            $filePath = storage_path('app/public/' . $doc['path']);
                            if (file_exists($filePath)) {
                                $zip->addFile($filePath, "user_documents/" . ($doc['original_name'] ?? "document_" . ($index + 1)));
                            }
                        }
                    }
                }

                // Add generated documents (results)
                foreach ($application->generatedDocuments as $doc) {
                    $filePath = storage_path('app/public/' . $doc->document_path);
                    if (file_exists($filePath)) {
                        $zip->addFile($filePath, 'results/' . $doc->document_name);
                    }
                }

                // Add payment proof if exists
                if ($application->payment && $application->payment->payment_proof) {
                    $paymentProofPath = storage_path('app/public/' . $application->payment->payment_proof);
                    if (file_exists($paymentProofPath)) {
                        $zip->addFile($paymentProofPath, 'payment_proof/' . basename($paymentProofPath));
                    }
                }

                $zip->close();

                if (file_exists($zipPath)) {
                    return response()->download($zipPath, $zipFileName)->deleteFileAfterSend(true);
                }
            }

            throw new \Exception('Failed to create archive ZIP file');
        } catch (\Exception $e) {
            Log::error('Archive download error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => 'Failed to download archive: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Guidelines Management
     */
    public function guidelines()
    {
        try {
            $guidelines = Guideline::orderBy('created_at', 'desc')->get();
            return response()->json([
                'success' => true,
                'data' => $guidelines
            ]);
        } catch (\Exception $e) {
            Log::error('Guidelines loading error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => 'Failed to load guidelines'
            ], 500);
        }
    }

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
                'required_documents' => $request->required_documents,
                'fee' => $request->fee,
                'is_active' => true
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Guideline created successfully',
                'data' => $guideline
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Create guideline error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => 'Failed to create guideline'
            ], 500);
        }
    }

    public function showGuideline($id)
    {
        try {
            $guideline = Guideline::findOrFail($id);
            return response()->json([
                'success' => true,
                'data' => $guideline
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Guideline not found'
            ], 404);
        }
    }

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
            $guideline->update($request->all());

            return response()->json([
                'success' => true,
                'message' => 'Guideline updated successfully',
                'data' => $guideline
            ]);
        } catch (\Exception $e) {
            Log::error('Update guideline error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => 'Failed to update guideline'
            ], 500);
        }
    }

    public function destroyGuideline($id)
    {
        try {
            $guideline = Guideline::findOrFail($id);

            if ($guideline->applications()->exists()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot delete guideline that is being used in applications'
                ], 422);
            }

            $guideline->delete();
            return response()->json([
                'success' => true,
                'message' => 'Guideline deleted successfully'
            ]);
        } catch (\Exception $e) {
            Log::error('Delete guideline error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => 'Failed to delete guideline'
            ], 500);
        }
    }

    /**
     * AJAX Data Methods for Admin Dashboard
     */
    public function getRequestsData()
    {
        try {
            $applications = Application::with(['user', 'guideline', 'payment'])
                ->orderBy('created_at', 'desc')
                ->take(10)
                ->get();

            // Transform data untuk konsistensi
            $applications->transform(function ($app) {
                $app->type_label = $app->type === 'pnbp' ? 'PNBP (Berbayar)' : 'Non-PNBP (Gratis)';
                $app->status_label = $this->getStatusLabel($app->status);
                $app->created_at_formatted = $app->created_at->format('d/m/Y H:i');
                $app->documents_count = is_array($app->documents) ? count($app->documents) : 0;
                $app->user_name = $app->user ? $app->user->name : 'N/A';
                $app->guideline_title = $app->guideline ? $app->guideline->title : 'N/A';
                return $app;
            });

            return response()->json($applications);
        } catch (\Exception $e) {
            Log::error('Requests data loading error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => 'Failed to load requests data'
            ], 500);
        }
    }

    public function getPaymentsData()
    {
        try {
            $payments = Payment::with(['application.user', 'application.guideline'])
                ->whereNotNull('payment_proof')
                ->orderBy('created_at', 'desc')
                ->take(10)
                ->get();

            // Transform data
            $payments->transform(function ($payment) {
                $payment->status_label = $this->getPaymentStatusLabel($payment->status);
                $payment->amount_formatted = 'Rp ' . number_format($payment->amount, 0, ',', '.');
                $payment->created_at_formatted = $payment->created_at->format('d/m/Y H:i');
                return $payment;
            });

            return response()->json($payments);
        } catch (\Exception $e) {
            Log::error('Payments data loading error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => 'Failed to load payments data'
            ], 500);
        }
    }

    public function getGuidelinesData()
    {
        try {
            $guidelines = Guideline::orderBy('created_at', 'desc')->get();
            return response()->json($guidelines);
        } catch (\Exception $e) {
            Log::error('Guidelines data loading error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => 'Failed to load guidelines data'
            ], 500);
        }
    }

    public function getUsersData()
    {
        try {
            $users = User::where('role', 'user')
                ->select('id', 'name', 'email', 'phone', 'created_at')
                ->orderBy('created_at', 'desc')
                ->get();

            return response()->json([
                'data' => $users
            ]);
        } catch (\Exception $e) {
            Log::error('Users data loading error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => 'Failed to load users data'
            ], 500);
        }
    }

    public function getStatistics()
    {
        try {
            $stats = [
                'pending_requests' => Application::where('status', 'pending')->count(),
                'pending_payments' => Application::where('status', 'payment_pending')->count(),
                'processing' => Application::where('status', 'processing')->count(),
                'completed' => Application::where('status', 'completed')->count(),
                'total_users' => User::where('role', 'user')->count()
            ];

            return response()->json($stats);
        } catch (\Exception $e) {
            Log::error('Statistics loading error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => 'Failed to load statistics'
            ], 500);
        }
    }

    /**
     * User Management
     */
    public function users()
    {
        try {
            $users = User::where('role', 'user')
                ->select('id', 'name', 'email', 'phone', 'created_at', 'email_verified_at')
                ->orderBy('created_at', 'desc')
                ->paginate(20);

            // Transform data
            $users->getCollection()->transform(function ($user) {
                $user->created_at_formatted = $user->created_at->format('d/m/Y');
                $user->is_verified = $user->email_verified_at !== null;
                return $user;
            });

            return response()->json([
                'success' => true,
                'data' => $users
            ]);
        } catch (\Exception $e) {
            Log::error('Users loading error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => 'Failed to load users'
            ], 500);
        }
    }

    public function createUser(Request $request)
    {
        try {
            $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:users,email',
                'phone' => 'nullable|string|max:15',
                'password' => 'required|min:6',
                'role' => 'required|in:admin,user'
            ]);

            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
                'password' => Hash::make($request->password),
                'role' => $request->role,
                'email_verified_at' => now(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'User created successfully',
                'data' => $user
            ]);
        } catch (\Exception $e) {
            Log::error('Create user error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => 'Failed to create user'
            ], 500);
        }
    }

    public function updateUser(Request $request, $id)
    {
        try {
            $user = User::findOrFail($id);

            $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:users,email,' . $id,
                'phone' => 'nullable|string|max:15',
                'role' => 'required|in:admin,user'
            ]);

            $updateData = $request->only(['name', 'email', 'phone', 'role']);

            if ($request->filled('password')) {
                $updateData['password'] = Hash::make($request->password);
            }

            $user->update($updateData);

            return response()->json([
                'success' => true,
                'message' => 'User updated successfully',
                'data' => $user
            ]);
        } catch (\Exception $e) {
            Log::error('Update user error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => 'Failed to update user'
            ], 500);
        }
    }

    public function deleteUser($id)
    {
        try {
            $user = User::findOrFail($id);

            if ($user->applications()->exists()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot delete user with existing applications'
                ], 422);
            }

            $user->delete();

            return response()->json([
                'success' => true,
                'message' => 'User deleted successfully'
            ]);
        } catch (\Exception $e) {
            Log::error('Delete user error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => 'Failed to delete user'
            ], 500);
        }
    }

    // ===== PRIVATE HELPER METHODS =====

    /**
     * Get Status Label Helper
     */
    private function getStatusLabel($status)
    {
        $labels = [
            'pending' => 'Menunggu Verifikasi',
            'verified' => 'Terverifikasi',
            'payment_pending' => 'Menunggu Pembayaran',
            'paid' => 'Sudah Bayar',
            'processing' => 'Sedang Diproses',
            'completed' => 'Selesai',
            'rejected' => 'Ditolak',
            'cancelled' => 'Dibatalkan'
        ];

        return $labels[$status] ?? ucfirst($status);
    }

    /**
     * Get Payment Status Label Helper
     */
    private function getPaymentStatusLabel($status)
    {
        $labels = [
            'pending' => 'Menunggu Verifikasi',
            'verified' => 'Terverifikasi',
            'rejected' => 'Ditolak'
        ];

        return $labels[$status] ?? ucfirst($status);
    }

    /**
     * Get History Title Helper
     */
    private function getHistoryTitle($action)
    {
        $titles = [
            'submitted' => 'Pengajuan Disubmit',
            'approved_with_payment' => 'Disetujui - Menunggu Pembayaran',
            'approved_no_payment' => 'Disetujui - Non-PNBP',
            'rejected' => 'Pengajuan Ditolak',
            'payment_uploaded' => 'Bukti Pembayaran Diupload',
            'payment_verified' => 'Pembayaran Diverifikasi',
            'payment_rejected' => 'Pembayaran Ditolak',
            'document_uploaded' => 'Dokumen Diupload',
            'completed' => 'Pengajuan Selesai',
            'completed_manual' => 'Diselesaikan Manual',
            'archived' => 'Diarsipkan',
            'cancelled' => 'Dibatalkan'
        ];

        return $titles[$action] ?? ucwords(str_replace('_', ' ', $action));
    }
}
