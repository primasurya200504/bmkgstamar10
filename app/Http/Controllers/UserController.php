<?php

namespace App\Http\Controllers;

use App\Models\Application;
use App\Models\Guideline;
use App\Models\Payment;
use App\Models\GeneratedDocument;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
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

            $applications = $user->applications()
                ->with(['guideline', 'payment', 'generatedDocuments', 'histories'])
                ->orderBy('created_at', 'desc')
                ->get();

            // Enhanced statistics
            $stats = [
                'pending' => $applications->where('status', 'pending')->count(),
                'in_process' => $applications->whereIn('status', [
                    'verified',
                    'payment_pending',
                    'paid',
                    'processing'
                ])->count(),
                'completed' => $applications->where('status', 'completed')->count(),
                'rejected' => $applications->where('status', 'rejected')->count(),
                'total' => $applications->count()
            ];

            // Date-based statistics
            $dateStats = [
                'historical_requests' => $applications->filter(function ($app) {
                    return $app->end_date && Carbon::parse($app->end_date)->lt(Carbon::parse('1990-01-01'));
                })->count(),
                'current_requests' => $applications->filter(function ($app) {
                    $today = Carbon::now()->format('Y-m-d');
                    return $app->start_date >= '1990-01-01' && $app->end_date <= $today;
                })->count(),
                'future_requests' => $applications->filter(function ($app) {
                    $today = Carbon::now()->format('Y-m-d');
                    return $app->start_date > $today;
                })->count(),
                'mixed_requests' => $applications->filter(function ($app) {
                    $today = Carbon::now()->format('Y-m-d');
                    return $app->start_date <= $today && $app->end_date > $today;
                })->count()
            ];

            // Processing time statistics
            $completedApps = $applications->where('status', 'completed');
            $avgProcessingDays = $completedApps->isNotEmpty() ?
                round($completedApps->avg(function ($app) {
                    return $app->created_at->diffInDays($app->updated_at);
                })) : 0;

            // Recent activity
            $recentActivity = $applications->take(5)->map(function ($app) {
                return [
                    'id' => $app->id,
                    'application_number' => $app->application_number,
                    'status' => $app->status,
                    'status_label' => $this->getStatusLabel($app->status),
                    'guideline_title' => $app->guideline->title ?? 'N/A',
                    'created_at' => $app->created_at->format('d/m/Y H:i'),
                    'last_update' => $app->updated_at->diffForHumans(),
                    'can_download' => $app->status === 'completed' && $app->generatedDocuments->isNotEmpty()
                ];
            });

            // Enhanced applications data
            $applications = $applications->map(function ($app) {
                $app->type_label = $app->type === 'pnbp' ? 'PNBP (Berbayar)' : 'Non-PNBP (Gratis)';
                $app->status_label = $this->getStatusLabel($app->status);
                $app->created_at_formatted = $app->created_at->format('d/m/Y H:i');
                $app->processing_days = $app->created_at->diffInDays($app->updated_at);
                $app->can_cancel = $app->status === 'pending';
                $app->can_pay = $app->status === 'payment_pending';
                $app->can_download = $app->status === 'completed' && $app->generatedDocuments->isNotEmpty();
                $app->documents_count = is_array($app->documents) ? count($app->documents) : 0;

                // Date range information
                if ($app->start_date && $app->end_date) {
                    $startDate = Carbon::parse($app->start_date);
                    $endDate = Carbon::parse($app->end_date);
                    $app->date_range_formatted = $startDate->format('d/m/Y') . ' - ' . $endDate->format('d/m/Y');
                    $app->duration_days = $startDate->diffInDays($endDate) + 1;
                    $app->is_historical = $endDate->lt(Carbon::parse('1990-01-01'));
                    $app->is_future = $startDate->gt(Carbon::now());
                } else {
                    $app->date_range_formatted = 'Tidak tersedia';
                    $app->duration_days = 0;
                    $app->is_historical = false;
                    $app->is_future = false;
                }

                return $app;
            });

            return view('user.dashboard', compact(
                'applications',
                'stats',
                'dateStats',
                'avgProcessingDays',
                'recentActivity'
            ));
        } catch (\Exception $e) {
            Log::error('User dashboard error: ' . $e->getMessage(), [
                'user_id' => Auth::id(),
                'trace' => $e->getTraceAsString()
            ]);

            // Fallback data
            return view('user.dashboard', [
                'applications' => collect(),
                'stats' => ['pending' => 0, 'in_process' => 0, 'completed' => 0, 'rejected' => 0, 'total' => 0],
                'dateStats' => ['historical_requests' => 0, 'current_requests' => 0, 'future_requests' => 0, 'mixed_requests' => 0],
                'avgProcessingDays' => 0,
                'recentActivity' => collect()
            ]);
        }
    }

    /**
     * Get Active Guidelines
     */
    public function guidelines(Request $request)
    {
        try {
            $guidelines = Guideline::where('is_active', true)
                ->orderBy('title', 'asc')
                ->get()
                ->map(function ($guideline) {
                    return [
                        'id' => $guideline->id,
                        'title' => $guideline->title,
                        'description' => $guideline->description,
                        'type' => $guideline->type,
                        'type_label' => $guideline->type === 'pnbp' ? 'PNBP (Berbayar)' : 'Non-PNBP (Gratis)',
                        'fee' => $guideline->fee,
                        'fee_formatted' => $guideline->fee > 0 ? 'Rp ' . number_format($guideline->fee, 0, ',', '.') : 'Gratis',
                        'required_documents' => $guideline->required_documents,
                        'documents_count' => is_array($guideline->required_documents) ? count($guideline->required_documents) : 0
                    ];
                });

            return response()->json([
                'success' => true,
                'data' => $guidelines
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to load guidelines: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => 'Failed to load guidelines'
            ], 500);
        }
    }

    /**
     * Submit Application dengan FREE DATE SELECTION dan Enhanced Validation
     */
    public function submitApplication(Request $request)
    {
        try {
            // Enhanced validation rules
            $rules = [
                'guideline_id' => 'required|exists:guidelines,id',
                'type' => 'required|in:pnbp,non_pnbp',
                'tanggal_mulai' => [
                    'required',
                    'date',
                    'after_or_equal:1900-01-01',
                    'before_or_equal:2100-12-31'
                ],
                'tanggal_selesai' => [
                    'required',
                    'date',
                    'after_or_equal:tanggal_mulai',
                    'before_or_equal:2100-12-31'
                ],
                'keperluan' => 'required|string|max:2000|min:20',
            ];

            // Dynamic file validation based on type
            if ($request->type === 'pnbp') {
                $rules['documents.0'] = 'required|file|mimes:pdf,jpg,jpeg,png,doc,docx|max:5120'; // Surat Pengantar
            } else {
                $rules['documents.0'] = 'required|file|mimes:pdf,jpg,jpeg,png,doc,docx|max:5120'; // Surat Pengantar
                $rules['documents.1'] = 'required|file|mimes:pdf,jpg,jpeg,png,doc,docx|max:5120'; // Proposal/KTI
                $rules['documents.2'] = 'required|file|mimes:pdf,jpg,jpeg,png,doc,docx|max:5120'; // Dokumen Pendukung
                $rules['documents.3'] = 'nullable|file|mimes:pdf,jpg,jpeg,png,doc,docx|max:5120'; // Opsional
            }

            $messages = [
                'tanggal_mulai.after_or_equal' => 'Tanggal mulai tidak boleh kurang dari 1 Januari 1900',
                'tanggal_mulai.before_or_equal' => 'Tanggal mulai tidak boleh lebih dari 31 Desember 2100',
                'tanggal_selesai.after_or_equal' => 'Tanggal selesai harus sama atau setelah tanggal mulai',
                'tanggal_selesai.before_or_equal' => 'Tanggal selesai tidak boleh lebih dari 31 Desember 2100',
                'keperluan.min' => 'Keperluan harus dijelaskan minimal 20 karakter',
                'keperluan.max' => 'Keperluan tidak boleh lebih dari 2000 karakter',
                'documents.0.required' => 'Surat Pengantar wajib diupload',
                'documents.1.required' => 'Proposal/Karya Tulis Ilmiah wajib diupload untuk Non-PNBP',
                'documents.2.required' => 'Dokumen Pendukung wajib diupload untuk Non-PNBP',
                'documents.*.max' => 'Ukuran file tidak boleh lebih dari 5MB',
                'documents.*.mimes' => 'Format file harus: PDF, DOC, DOCX, JPG, JPEG, atau PNG'
            ];

            $validator = Validator::make($request->all(), $rules, $messages);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation error',
                    'errors' => $validator->errors()
                ], 422);
            }

            $guideline = Guideline::findOrFail($request->guideline_id);

            // Generate unique application number
            $today = Carbon::today();
            $dailySequence = Application::whereDate('created_at', $today)->count() + 1;
            $applicationNumber = 'BMKG' . $today->format('md') . '/' . $today->format('Y') . '/' . str_pad($dailySequence, 4, '0', STR_PAD_LEFT);

            // Enhanced Document processing
            $documents = [];
            $totalSize = 0;
            $documentLabels = [
                0 => 'Surat Pengantar',
                1 => 'Proposal/Karya Tulis Ilmiah',
                2 => 'Dokumen Pendukung',
                3 => 'Dokumen Tambahan'
            ];

            if ($request->hasFile('documents') && is_array($request->file('documents'))) {
                foreach ($request->file('documents') as $index => $file) {
                    if (!$file || !$file->isValid()) continue;

                    // Check total size
                    $fileSize = $file->getSize();
                    $totalSize += $fileSize;

                    if ($totalSize > (25 * 1024 * 1024)) { // 25MB total limit
                        throw new \Exception('Total ukuran semua file tidak boleh lebih dari 25MB');
                    }

                    // Generate safe filename
                    $originalName = $file->getClientOriginalName();
                    $extension = strtolower($file->getClientOriginalExtension());
                    $safeName = preg_replace('/[^a-zA-Z0-9._-]/', '_', pathinfo($originalName, PATHINFO_FILENAME));
                    $uniqueId = uniqid() . '_' . time();
                    $filename = $uniqueId . '_' . $index . '_' . substr($safeName, 0, 50) . '.' . $extension;

                    // Organized storage path
                    $folderPath = $request->type === 'pnbp' ? 'applications/pnbp' : 'applications/non_pnbp';
                    $yearMonth = $today->format('Y/m');
                    $fullPath = $folderPath . '/' . $yearMonth;

                    // Store file
                    $path = $file->storeAs($fullPath, $filename, 'public');

                    if (!$path) {
                        throw new \Exception('Gagal menyimpan file: ' . $originalName);
                    }

                    // Validate file content (basic check)
                    $mimeType = $file->getClientMimeType();
                    $allowedMimes = [
                        'application/pdf',
                        'application/msword',
                        'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                        'image/jpeg',
                        'image/png',
                        'image/jpg'
                    ];

                    if (!in_array($mimeType, $allowedMimes)) {
                        Storage::disk('public')->delete($path);
                        throw new \Exception('File ' . $originalName . ' memiliki format yang tidak diizinkan');
                    }

                    $documents[] = [
                        'index' => $index,
                        'label' => $documentLabels[$index] ?? 'Dokumen ' . ($index + 1),
                        'original_name' => $originalName,
                        'stored_name' => $filename,
                        'path' => $path,
                        'size' => $fileSize,
                        'size_mb' => round($fileSize / 1024 / 1024, 2),
                        'mime_type' => $mimeType,
                        'extension' => $extension,
                        'folder' => $fullPath,
                        'uploaded_at' => now()->toISOString(),
                        'unique_id' => $uniqueId,
                        'is_valid' => true
                    ];
                }
            }

            // Validate minimum documents
            $minDocuments = $request->type === 'pnbp' ? 1 : 3;
            if (count($documents) < $minDocuments) {
                // Clean up uploaded files
                foreach ($documents as $doc) {
                    Storage::disk('public')->delete($doc['path']);
                }
                throw new \Exception("Minimal {$minDocuments} dokumen harus diupload untuk " . strtoupper($request->type));
            }

            // Enhanced Date context analysis
            $today = Carbon::now();
            $startDate = Carbon::parse($request->tanggal_mulai);
            $endDate = Carbon::parse($request->tanggal_selesai);

            $dateContext = [
                'start_date_type' => $this->getDateType($startDate, $today),
                'end_date_type' => $this->getDateType($endDate, $today),
                'date_range_days' => $startDate->diffInDays($endDate) + 1,
                'is_historical_request' => $endDate->lt(Carbon::parse('1990-01-01')),
                'is_current_request' => $startDate->gte(Carbon::parse('1990-01-01')) && $endDate->lte($today),
                'is_future_request' => $startDate->gt($today),
                'is_mixed_request' => $startDate->lte($today) && $endDate->gt($today),
                'data_availability_status' => $this->assessDataAvailability($startDate, $endDate, $today),
                'period_classification' => $this->classifyPeriod($startDate, $endDate),
                'estimated_processing_days' => $this->estimateProcessingTime($startDate, $endDate, $request->type),
                'complexity_score' => $this->calculateComplexityScore($startDate, $endDate, $request->type, count($documents))
            ];

            // Create application
            $application = Application::create([
                'user_id' => Auth::id(),
                'guideline_id' => $request->guideline_id,
                'application_number' => $applicationNumber,
                'type' => $request->type,
                'documents' => $documents,
                'start_date' => $startDate->format('Y-m-d'),
                'end_date' => $endDate->format('Y-m-d'),
                'purpose' => $request->keperluan,
                'status' => 'pending',
                'notes' => json_encode($dateContext)
            ]);

            // Enhanced logging
            Log::info('Application submitted successfully', [
                'user_id' => Auth::id(),
                'application_id' => $application->id,
                'application_number' => $applicationNumber,
                'type' => $request->type,
                'guideline_id' => $request->guideline_id,
                'documents_count' => count($documents),
                'storage_folder' => $fullPath ?? 'unknown',
                'date_range' => $startDate->format('Y-m-d') . ' to ' . $endDate->format('Y-m-d'),
                'date_context' => $dateContext,
                'total_file_size_mb' => round($totalSize / 1024 / 1024, 2)
            ]);

            // Log history with comprehensive information
            $application->logHistory(
                'submitted',
                'user',
                Auth::id(),
                'Pengajuan Surat/Data Disubmit',
                "User mengajukan {$application->guideline->title} untuk periode {$startDate->format('d/m/Y')} s/d {$endDate->format('d/m/Y')}. Total {$dateContext['date_range_days']} hari data.",
                array_merge([
                    'guideline_title' => $application->guideline->title,
                    'documents_uploaded' => count($documents),
                    'type' => $request->type,
                    'storage_folder' => $fullPath ?? 'unknown',
                    'purpose_length' => strlen($request->keperluan),
                    'date_range' => $startDate->format('Y-m-d') . ' to ' . $endDate->format('Y-m-d'),
                    'total_file_size_bytes' => $totalSize,
                    'documents_detail' => array_map(function ($doc) {
                        return [
                            'label' => $doc['label'],
                            'name' => $doc['original_name'],
                            'size_mb' => $doc['size_mb'],
                            'type' => $doc['extension']
                        ];
                    }, $documents)
                ], $dateContext)
            );

            return response()->json([
                'success' => true,
                'message' => 'Pengajuan berhasil disubmit',
                'data' => [
                    'application_id' => $application->id,
                    'application_number' => $applicationNumber,
                    'storage_path' => $fullPath ?? null,
                    'documents_count' => count($documents),
                    'total_size_mb' => round($totalSize / 1024 / 1024, 2),
                    'date_info' => [
                        'range_days' => $dateContext['date_range_days'],
                        'is_historical' => $dateContext['is_historical_request'],
                        'is_future' => $dateContext['is_future_request'],
                        'complexity' => $dateContext['complexity_score'],
                        'estimated_processing' => $dateContext['estimated_processing_days'] . ' hari kerja'
                    ]
                ]
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Application submission failed', [
                'user_id' => Auth::id(),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'request_data' => $request->except(['documents', 'password'])
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Gagal submit pengajuan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Upload Payment Proof dengan Enhanced Validation
     */
    public function uploadPaymentProof(Request $request, $id)
    {
        try {
            $validator = Validator::make($request->all(), [
                'payment_proof' => [
                    'required',
                    'file',
                    'mimes:pdf,jpg,jpeg,png',
                    'max:3072' // 3MB
                ]
            ], [
                'payment_proof.required' => 'File bukti pembayaran wajib diupload',
                'payment_proof.mimes' => 'Format file harus: PDF, JPG, JPEG, atau PNG',
                'payment_proof.max' => 'Ukuran file tidak boleh lebih dari 3MB'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation error',
                    'errors' => $validator->errors()
                ], 422);
            }

            $application = Application::where('user_id', Auth::id())->findOrFail($id);

            if (!in_array($application->status, ['verified', 'payment_pending'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Aplikasi tidak dalam status yang memungkinkan upload pembayaran'
                ], 422);
            }

            // Find or create payment record
            $payment = Payment::where('application_id', $application->id)->first();

            if (!$payment) {
                $fee = ($application->type === 'non_pnbp') ? 0 : $application->guideline->fee;
                $payment = Payment::create([
                    'application_id' => $application->id,
                    'amount' => $fee,
                    'status' => 'pending'
                ]);
            }

            if ($request->hasFile('payment_proof')) {
                $file = $request->file('payment_proof');

                // Validate file integrity
                if (!$file->isValid()) {
                    throw new \Exception('File bukti pembayaran tidak valid atau rusak');
                }

                // Generate safe filename
                $originalName = $file->getClientOriginalName();
                $extension = strtolower($file->getClientOriginalExtension());
                $safeName = preg_replace('/[^a-zA-Z0-9._-]/', '_', pathinfo($originalName, PATHINFO_FILENAME));
                $uniqueId = uniqid() . '_' . time();
                $filename = 'payment_' . str_replace(['/', '\\', ' '], '_', $application->application_number) . '_' . $uniqueId . '.' . $extension;

                // Organized storage
                $folderPath = $application->type === 'pnbp' ? 'payments/pnbp' : 'payments/non_pnbp';
                $yearMonth = Carbon::now()->format('Y/m');
                $fullPath = $folderPath . '/' . $yearMonth;

                // Delete old payment proof if exists
                if ($payment->payment_proof && Storage::disk('public')->exists($payment->payment_proof)) {
                    Storage::disk('public')->delete($payment->payment_proof);
                }

                $path = $file->storeAs($fullPath, $filename, 'public');

                if (!$path) {
                    throw new \Exception('Gagal menyimpan file bukti pembayaran');
                }

                // Update payment record
                $payment->update([
                    'payment_proof' => $path,
                    'status' => 'pending',
                    'paid_at' => now(),
                    'upload_metadata' => [
                        'original_name' => $originalName,
                        'file_size' => $file->getSize(),
                        'mime_type' => $file->getClientMimeType(),
                        'uploaded_at' => now(),
                        'user_agent' => $request->userAgent(),
                        'ip_address' => $request->ip()
                    ]
                ]);

                $application->update(['status' => 'payment_pending']);

                // Enhanced logging
                $application->logHistory(
                    'payment_uploaded',
                    'user',
                    Auth::id(),
                    'Bukti Pembayaran Diupload',
                    "User mengupload bukti pembayaran sebesar Rp " . number_format($payment->amount) . " untuk aplikasi " . $application->application_number,
                    [
                        'payment_id' => $payment->id,
                        'amount' => $payment->amount,
                        'file_path' => $path,
                        'storage_folder' => $fullPath,
                        'file_size_bytes' => $file->getSize(),
                        'file_size_mb' => round($file->getSize() / 1024 / 1024, 2),
                        'file_original_name' => $originalName,
                        'file_extension' => $extension,
                        'mime_type' => $file->getClientMimeType(),
                        'upload_ip' => $request->ip(),
                        'user_agent' => $request->userAgent()
                    ]
                );

                Log::info('Payment proof uploaded successfully', [
                    'application_id' => $application->id,
                    'payment_id' => $payment->id,
                    'user_id' => Auth::id(),
                    'file_path' => $path,
                    'file_size' => $file->getSize()
                ]);

                return response()->json([
                    'success' => true,
                    'message' => 'Bukti pembayaran berhasil diupload dan sedang menunggu verifikasi admin',
                    'data' => [
                        'payment_id' => $payment->id,
                        'file_path' => $path,
                        'storage_folder' => $fullPath,
                        'file_size_mb' => round($file->getSize() / 1024 / 1024, 2),
                        'status' => 'Menunggu Verifikasi'
                    ]
                ]);
            }

            throw new \Exception('File bukti pembayaran tidak ditemukan');
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Aplikasi tidak ditemukan'
            ], 404);
        } catch (\Exception $e) {
            Log::error('Payment proof upload failed', [
                'application_id' => $id,
                'user_id' => Auth::id(),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Gagal upload bukti pembayaran: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Download Document dengan Enhanced Security dan Logging
     */
    public function downloadDocument($id)
    {
        try {
            $document = GeneratedDocument::whereHas('application', function ($query) {
                $query->where('user_id', Auth::id());
            })->findOrFail($id);

            $filePath = storage_path('app/public/' . $document->document_path);

            // Enhanced file validation
            if (!file_exists($filePath)) {
                Log::warning('Document file not found', [
                    'document_id' => $id,
                    'user_id' => Auth::id(),
                    'file_path' => $filePath,
                    'document_path' => $document->document_path
                ]);
                abort(404, 'File dokumen tidak ditemukan');
            }

            // Check file integrity
            if (!is_readable($filePath)) {
                Log::error('Document file not readable', [
                    'document_id' => $id,
                    'user_id' => Auth::id(),
                    'file_path' => $filePath
                ]);
                abort(403, 'File dokumen tidak dapat dibaca');
            }

            $fileSize = filesize($filePath);
            if ($fileSize === false || $fileSize === 0) {
                Log::error('Document file is empty or corrupted', [
                    'document_id' => $id,
                    'user_id' => Auth::id(),
                    'file_path' => $filePath
                ]);
                abort(500, 'File dokumen kosong atau rusak');
            }

            // Enhanced mime type detection
            $documentName = $document->document_name;
            $fileExtension = strtolower(pathinfo($documentName, PATHINFO_EXTENSION));

            $mimeTypes = [
                'pdf' => 'application/pdf',
                'doc' => 'application/msword',
                'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                'xls' => 'application/vnd.ms-excel',
                'xlsx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                'ppt' => 'application/vnd.ms-powerpoint',
                'pptx' => 'application/vnd.openxmlformats-officedocument.presentationml.presentation',
                'jpg' => 'image/jpeg',
                'jpeg' => 'image/jpeg',
                'png' => 'image/png',
                'gif' => 'image/gif',
                'txt' => 'text/plain',
                'csv' => 'text/csv',
                'zip' => 'application/zip',
                'rar' => 'application/x-rar-compressed'
            ];

            $mimeType = $mimeTypes[$fileExtension] ?? 'application/octet-stream';

            // Sanitize filename for download
            $safeFileName = preg_replace('/[^a-zA-Z0-9._-]/', '_', $documentName);
            if (empty($safeFileName)) {
                $safeFileName = 'document_' . $document->id . '.' . $fileExtension;
            }

            // Enhanced headers for secure download
            $headers = [
                'Content-Type' => $mimeType,
                'Content-Disposition' => 'attachment; filename="' . $safeFileName . '"',
                'Content-Description' => 'File Transfer',
                'Content-Transfer-Encoding' => 'binary',
                'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
                'Pragma' => 'public',
                'Expires' => '0',
                'Content-Length' => $fileSize,
                'X-Content-Type-Options' => 'nosniff',
                'X-Frame-Options' => 'DENY',
                'X-XSS-Protection' => '1; mode=block'
            ];

            // Enhanced download activity logging
            Log::info('Document downloaded', [
                'document_id' => $id,
                'user_id' => Auth::id(),
                'application_id' => $document->application_id,
                'application_number' => $document->application->application_number,
                'document_name' => $documentName,
                'file_path' => $document->document_path,
                'mime_type' => $mimeType,
                'file_extension' => $fileExtension,
                'file_size_bytes' => $fileSize,
                'file_size_mb' => round($fileSize / 1024 / 1024, 2),
                'download_ip' => request()->ip(),
                'user_agent' => request()->userAgent(),
                'download_timestamp' => now()
            ]);

            // Log download in application history
            $document->application->logHistory(
                'document_downloaded',
                'user',
                Auth::id(),
                'Dokumen Diunduh',
                "User mengunduh dokumen '{$documentName}' dari aplikasi {$document->application->application_number}",
                [
                    'document_id' => $id,
                    'document_name' => $documentName,
                    'file_size_mb' => round($fileSize / 1024 / 1024, 2),
                    'download_ip' => request()->ip(),
                    'download_timestamp' => now()
                ]
            );

            // Use proper download response
            return response()->download($filePath, $safeFileName, $headers);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            Log::warning('Document not found for download', [
                'document_id' => $id,
                'user_id' => Auth::id()
            ]);
            abort(404, 'Dokumen tidak ditemukan atau bukan milik Anda');
        } catch (\Exception $e) {
            Log::error('Document download failed', [
                'document_id' => $id,
                'user_id' => Auth::id(),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            abort(500, 'Gagal mengunduh dokumen');
        }
    }

    /**
     * Get Application History dengan Enhanced Data
     */
    public function history(Request $request)
    {
        try {
            $user = Auth::user();

            $query = $user->applications()
                ->with(['guideline', 'payment', 'generatedDocuments', 'histories' => function ($q) {
                    $q->orderBy('created_at', 'desc');
                }]);

            // Apply filters
            if ($request->filled('status')) {
                $query->where('status', $request->status);
            }

            if ($request->filled('type')) {
                $query->where('type', $request->type);
            }

            if ($request->filled('year')) {
                $query->whereYear('created_at', $request->year);
            }

            if ($request->filled('month')) {
                $query->whereMonth('created_at', $request->month);
            }

            $applications = $query->orderBy('created_at', 'desc')->get();

            // Transform data dengan enhanced information
            $applications = $applications->map(function ($app) {
                return [
                    'id' => $app->id,
                    'application_number' => $app->application_number,
                    'guideline' => [
                        'title' => $app->guideline->title ?? 'N/A',
                        'type' => $app->guideline->type ?? $app->type
                    ],
                    'type' => $app->type,
                    'type_label' => $app->type === 'pnbp' ? 'PNBP (Berbayar)' : 'Non-PNBP (Gratis)',
                    'status' => $app->status,
                    'status_label' => $this->getStatusLabel($app->status),
                    'purpose' => $app->purpose,
                    'date_range' => ($app->start_date && $app->end_date) ? [
                        'start' => Carbon::parse($app->start_date)->format('d/m/Y'),
                        'end' => Carbon::parse($app->end_date)->format('d/m/Y'),
                        'duration_days' => Carbon::parse($app->start_date)->diffInDays(Carbon::parse($app->end_date)) + 1
                    ] : null,
                    'created_at' => $app->created_at->format('d/m/Y H:i'),
                    'updated_at' => $app->updated_at->format('d/m/Y H:i'),
                    'processing_days' => $app->created_at->diffInDays($app->updated_at),
                    'documents_count' => is_array($app->documents) ? count($app->documents) : 0,
                    'payment' => $app->payment ? [
                        'amount' => $app->payment->amount,
                        'amount_formatted' => 'Rp ' . number_format($app->payment->amount, 0, ',', '.'),
                        'status' => $app->payment->status,
                        'paid_at' => $app->payment->paid_at ? Carbon::parse($app->payment->paid_at)->format('d/m/Y H:i') : null
                    ] : null,
                    'generated_documents' => $app->generatedDocuments->map(function ($doc) {
                        return [
                            'id' => $doc->id,
                            'name' => $doc->document_name,
                            'created_at' => $doc->created_at->format('d/m/Y H:i'),
                            'can_download' => true
                        ];
                    }),
                    'recent_histories' => $app->histories->take(3)->map(function ($history) {
                        return [
                            'action' => $history->action,
                            'title' => $history->title ?? $this->getHistoryTitle($history->action),
                            'description' => $history->description,
                            'created_at' => $history->created_at->format('d/m/Y H:i'),
                            'created_at_relative' => $history->created_at->diffForHumans()
                        ];
                    }),
                    'actions' => [
                        'can_cancel' => $app->status === 'pending',
                        'can_pay' => $app->status === 'payment_pending',
                        'can_download' => $app->status === 'completed' && $app->generatedDocuments->isNotEmpty(),
                        'can_view_details' => true
                    ]
                ];
            });

            return response()->json([
                'success' => true,
                'data' => $applications,
                'summary' => [
                    'total' => $applications->count(),
                    'by_status' => $applications->groupBy('status')->map->count(),
                    'by_type' => $applications->groupBy('type')->map->count()
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to load user history: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => 'Failed to load history'
            ], 500);
        }
    }

    /**
     * Get User Profile dengan Enhanced Data
     */
    public function profile()
    {
        try {
            $user = Auth::user();

            // Calculate user statistics
            $applications = $user->applications;
            $completedApplications = $applications->where('status', 'completed');

            $profileData = [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'phone' => $user->phone,
                'role' => $user->role,
                'created_at' => $user->created_at->format('d/m/Y H:i'),
                'email_verified_at' => $user->email_verified_at ? $user->email_verified_at->format('d/m/Y H:i') : null,
                'is_verified' => $user->email_verified_at !== null,
                'statistics' => [
                    'total_applications' => $applications->count(),
                    'completed_applications' => $completedApplications->count(),
                    'average_processing_days' => $completedApplications->isNotEmpty() ?
                        round($completedApplications->avg(function ($app) {
                            return $app->created_at->diffInDays($app->updated_at);
                        })) : 0,
                    'total_documents_downloaded' => GeneratedDocument::whereHas('application', function ($q) {
                        $q->where('user_id', Auth::id());
                    })->count(),
                    'member_since_days' => $user->created_at->diffInDays(now()),
                    'last_activity' => $user->updated_at->diffForHumans()
                ]
            ];

            return response()->json([
                'success' => true,
                'data' => $profileData
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to load user profile: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => 'Failed to load profile'
            ], 500);
        }
    }

    /**
     * Update User Profile dengan Enhanced Validation
     */
    public function updateProfile(Request $request)
    {
        try {
            $user = Auth::user();

            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255|min:2',
                'email' => 'required|email|unique:users,email,' . $user->id,
                'phone' => 'nullable|string|max:15|regex:/^[0-9+\-\s()]+$/'
            ], [
                'name.min' => 'Nama harus minimal 2 karakter',
                'name.max' => 'Nama tidak boleh lebih dari 255 karakter',
                'email.unique' => 'Email sudah digunakan oleh pengguna lain',
                'phone.regex' => 'Format nomor telepon tidak valid (gunakan angka, +, -, spasi, atau tanda kurung)'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation error',
                    'errors' => $validator->errors()
                ], 422);
            }

            $oldData = [
                'name' => $user->name,
                'email' => $user->email,
                'phone' => $user->phone
            ];

            $updateData = [
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone
            ];

            // Check if email changed - require verification
            $emailChanged = $user->email !== $request->email;
            if ($emailChanged) {
                $updateData['email_verified_at'] = null;
            }

            $user->update($updateData);

            Log::info('User profile updated', [
                'user_id' => $user->id,
                'old_data' => $oldData,
                'new_data' => $updateData,
                'email_changed' => $emailChanged,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent()
            ]);

            return response()->json([
                'success' => true,
                'message' => $emailChanged ?
                    'Profil berhasil diupdate. Silakan verifikasi email baru Anda.' :
                    'Profil berhasil diupdate',
                'data' => [
                    'user' => $user->only(['name', 'email', 'phone']),
                    'email_changed' => $emailChanged,
                    'requires_verification' => $emailChanged
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Profile update failed: ' . $e->getMessage(), [
                'user_id' => Auth::id(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'success' => false,
                'error' => 'Failed to update profile'
            ], 500);
        }
    }

    /**
     * Change Password dengan Enhanced Security
     */
    public function changePassword(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'current_password' => 'required',
                'new_password' => [
                    'required',
                    'min:8',
                    'confirmed',
                    'different:current_password',
                    'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]/'
                ]
            ], [
                'new_password.min' => 'Password baru harus minimal 8 karakter',
                'new_password.confirmed' => 'Konfirmasi password tidak cocok',
                'new_password.different' => 'Password baru harus berbeda dari password lama',
                'new_password.regex' => 'Password harus mengandung huruf besar, huruf kecil, angka, dan karakter khusus'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation error',
                    'errors' => $validator->errors()
                ], 422);
            }

            $user = Auth::user();

            if (!Hash::check($request->current_password, $user->password)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Password lama tidak benar'
                ], 422);
            }

            // Additional security: check if new password was used recently
            // This would require a password history table in production

            $user->update([
                'password' => Hash::make($request->new_password),
                'password_changed_at' => now()
            ]);

            // Log password change
            Log::info('User password changed', [
                'user_id' => $user->id,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'changed_at' => now()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Password berhasil diubah. Silakan login ulang untuk keamanan.'
            ]);
        } catch (\Exception $e) {
            Log::error('Password change failed: ' . $e->getMessage(), [
                'user_id' => Auth::id(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'success' => false,
                'error' => 'Failed to change password'
            ], 500);
        }
    }

    /**
     * Cancel Application
     */
    public function cancelApplication($id)
    {
        try {
            $application = Application::where('user_id', Auth::id())
                ->where('status', 'pending')
                ->findOrFail($id);

            $application->update(['status' => 'cancelled']);

            // Log cancellation
            $application->logHistory(
                'cancelled',
                'user',
                Auth::id(),
                'Pengajuan Dibatalkan',
                'User membatalkan pengajuan yang masih dalam status pending',
                [
                    'cancelled_at' => now(),
                    'reason' => 'User cancellation',
                    'ip_address' => request()->ip()
                ]
            );

            Log::info('Application cancelled by user', [
                'application_id' => $id,
                'user_id' => Auth::id(),
                'application_number' => $application->application_number
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Pengajuan berhasil dibatalkan'
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Pengajuan tidak ditemukan atau tidak dapat dibatalkan'
            ], 404);
        } catch (\Exception $e) {
            Log::error('Failed to cancel application', [
                'application_id' => $id,
                'user_id' => Auth::id(),
                'error' => $e->getMessage()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Gagal membatalkan pengajuan'
            ], 500);
        }
    }

    /**
     * Get Application Details
     */
    public function getApplicationDetails($id)
    {
        try {
            $application = Application::where('user_id', Auth::id())
                ->with(['guideline', 'payment', 'generatedDocuments', 'histories.actor'])
                ->findOrFail($id);

            // Enhanced application details
            $details = [
                'id' => $application->id,
                'application_number' => $application->application_number,
                'guideline' => $application->guideline,
                'type' => $application->type,
                'type_label' => $application->type === 'pnbp' ? 'PNBP (Berbayar)' : 'Non-PNBP (Gratis)',
                'status' => $application->status,
                'status_label' => $this->getStatusLabel($application->status),
                'purpose' => $application->purpose,
                'date_range' => [
                    'start_date' => $application->start_date,
                    'end_date' => $application->end_date,
                    'start_date_formatted' => Carbon::parse($application->start_date)->format('d/m/Y'),
                    'end_date_formatted' => Carbon::parse($application->end_date)->format('d/m/Y'),
                    'duration_days' => Carbon::parse($application->start_date)->diffInDays(Carbon::parse($application->end_date)) + 1
                ],
                'documents' => $application->documents,
                'payment' => $application->payment,
                'generated_documents' => $application->generatedDocuments,
                'histories' => $application->histories->map(function ($history) {
                    return [
                        'action' => $history->action,
                        'title' => $history->title ?? $this->getHistoryTitle($history->action),
                        'description' => $history->description,
                        'actor_name' => $history->actor ? $history->actor->name : 'System',
                        'created_at' => $history->created_at->format('d/m/Y H:i'),
                        'created_at_relative' => $history->created_at->diffForHumans()
                    ];
                }),
                'created_at' => $application->created_at,
                'updated_at' => $application->updated_at,
                'processing_days' => $application->created_at->diffInDays($application->updated_at),
                'actions' => [
                    'can_cancel' => $application->status === 'pending',
                    'can_pay' => $application->status === 'payment_pending',
                    'can_download' => $application->status === 'completed' && $application->generatedDocuments->isNotEmpty()
                ]
            ];

            return response()->json([
                'success' => true,
                'data' => $details
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Application not found'
            ], 404);
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
            'document_downloaded' => 'Dokumen Diunduh',
            'completed' => 'Pengajuan Selesai',
            'cancelled' => 'Pengajuan Dibatalkan'
        ];

        return $titles[$action] ?? ucwords(str_replace('_', ' ', $action));
    }

    /**
     * Get Date Type Helper
     */
    private function getDateType($date, $today)
    {
        if ($date->gt($today)) {
            return 'future';
        } elseif ($date->gte(Carbon::parse('1990-01-01'))) {
            return 'available';
        } else {
            return 'historical';
        }
    }

    /**
     * Assess Data Availability Helper
     */
    private function assessDataAvailability($startDate, $endDate, $today)
    {
        if ($endDate->lt(Carbon::parse('1990-01-01'))) {
            return 'very_limited';
        } elseif ($startDate->gte(Carbon::parse('1990-01-01')) && $endDate->lte($today)) {
            return 'fully_available';
        } elseif ($startDate->gt($today)) {
            return 'projection_needed';
        } elseif ($startDate->lte($today) && $endDate->gt($today)) {
            return 'mixed_historical_projection';
        } else {
            return 'needs_assessment';
        }
    }

    /**
     * Classify Period Helper
     */
    private function classifyPeriod($startDate, $endDate)
    {
        $diffDays = $startDate->diffInDays($endDate) + 1;

        if ($diffDays <= 7) {
            return 'weekly';
        } elseif ($diffDays <= 31) {
            return 'monthly';
        } elseif ($diffDays <= 366) {
            return 'yearly';
        } elseif ($diffDays <= 1826) { // 5 years
            return 'multi_year';
        } else {
            return 'long_term';
        }
    }

    /**
     * Estimate Processing Time Helper
     */
    private function estimateProcessingTime($startDate, $endDate, $type)
    {
        $diffDays = $startDate->diffInDays($endDate) + 1;
        $baseTime = $type === 'pnbp' ? 3 : 5; // Base processing days
        $today = Carbon::now();

        // Add complexity based on date range
        if ($diffDays > 1826) { // > 5 years
            $baseTime += 7;
        } elseif ($diffDays > 366) { // > 1 year
            $baseTime += 3;
        } elseif ($diffDays > 31) { // > 1 month
            $baseTime += 1;
        }

        // Add complexity for historical data
        if ($endDate->lt(Carbon::parse('1990-01-01'))) {
            $baseTime += 5;
        }

        // Add complexity for future projection
        if ($startDate->gt($today)) {
            $baseTime += 3;
        }

        return min($baseTime, 21); // Max 21 working days
    }

    /**
     * Calculate Complexity Score Helper
     */
    private function calculateComplexityScore($startDate, $endDate, $type, $documentsCount)
    {
        $score = 0;
        $diffDays = $startDate->diffInDays($endDate) + 1;
        $today = Carbon::now();

        // Date range complexity
        if ($diffDays > 1826) $score += 5;
        elseif ($diffDays > 366) $score += 3;
        elseif ($diffDays > 31) $score += 1;

        // Historical data complexity
        if ($endDate->lt(Carbon::parse('1990-01-01'))) $score += 4;

        // Future data complexity
        if ($startDate->gt($today)) $score += 3;

        // Type complexity
        if ($type === 'non_pnbp') $score += 2;

        // Documents complexity
        if ($documentsCount > 3) $score += 1;

        return min($score, 10); // Scale 0-10
    }
}
