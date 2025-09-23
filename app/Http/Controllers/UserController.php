<?php

namespace App\Http\Controllers;

use App\Models\Application;
use App\Models\Guideline;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class UserController extends Controller
{
    public function dashboard()
    {
        try {
            $applications = Auth::user()->applications()
                ->with(['guideline', 'payment', 'generatedDocuments'])
                ->orderBy('created_at', 'desc')
                ->get();

            $stats = [
                'pending' => $applications->where('status', 'pending')->count(),
                'in_process' => $applications->whereIn('status', ['verified', 'payment_pending', 'paid', 'processing'])->count(),
                'completed' => $applications->where('status', 'completed')->count(),
                'rejected' => $applications->where('status', 'rejected')->count()
            ];

            // ADDED: Enhanced stats dengan date context
            $dateStats = [
                'historical_requests' => $applications->filter(function ($app) {
                    return $app->end_date && $app->end_date < '1990-01-01';
                })->count(),
                'current_requests' => $applications->filter(function ($app) {
                    $today = now()->format('Y-m-d');
                    return $app->start_date >= '1990-01-01' && $app->end_date <= $today;
                })->count(),
                'future_requests' => $applications->filter(function ($app) {
                    $today = now()->format('Y-m-d');
                    return $app->start_date > $today;
                })->count()
            ];

            return view('user.dashboard', compact('applications', 'stats', 'dateStats'));
        } catch (\Exception $e) {
            Log::error('Dashboard load failed: ' . $e->getMessage());
            return view('user.dashboard', [
                'applications' => collect(),
                'stats' => ['pending' => 0, 'in_process' => 0, 'completed' => 0, 'rejected' => 0],
                'dateStats' => ['historical_requests' => 0, 'current_requests' => 0, 'future_requests' => 0]
            ]);
        }
    }

    public function guidelines(Request $request)
    {
        try {
            $guidelines = Guideline::where('is_active', true)
                ->orderBy('title', 'asc')
                ->get();

            return response()->json($guidelines);
        } catch (\Exception $e) {
            Log::error('Failed to load guidelines: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to load guidelines'], 500);
        }
    }

    // UPDATED: Submit application dengan FREE DATE SELECTION dan enhanced error handling
    public function submitApplication(Request $request)
    {
        try {
            // UPDATED: Free date validation dengan range yang lebih luas
            $rules = [
                'guideline_id' => 'required|exists:guidelines,id',
                'type' => 'required|in:pnbp,non_pnbp',
                'tanggal_mulai' => 'required|date|after_or_equal:1900-01-01|before_or_equal:2100-12-31',
                'tanggal_selesai' => 'required|date|after_or_equal:tanggal_mulai|before_or_equal:2100-12-31',
                'keperluan' => 'required|string|max:1000|min:10',
            ];

            // File validation based on type
            if ($request->type === 'pnbp') {
                $rules['documents.0'] = 'required|file|mimes:pdf,jpg,jpeg,png,doc,docx|max:5120';
            } else {
                $rules['documents.0'] = 'required|file|mimes:pdf,jpg,jpeg,png,doc,docx|max:5120';
                $rules['documents.1'] = 'required|file|mimes:pdf,jpg,jpeg,png,doc,docx|max:5120';
                $rules['documents.2'] = 'required|file|mimes:pdf,jpg,jpeg,png,doc,docx|max:5120';
                $rules['documents.3'] = 'nullable|file|mimes:pdf,jpg,jpeg,png,doc,docx|max:5120';
            }

            $request->validate($rules, [
                'tanggal_mulai.after_or_equal' => 'Tanggal mulai tidak boleh kurang dari 1 Januari 1900',
                'tanggal_mulai.before_or_equal' => 'Tanggal mulai tidak boleh lebih dari 31 Desember 2100',
                'tanggal_selesai.after_or_equal' => 'Tanggal selesai harus sama atau setelah tanggal mulai',
                'tanggal_selesai.before_or_equal' => 'Tanggal selesai tidak boleh lebih dari 31 Desember 2100',
                'keperluan.min' => 'Keperluan harus dijelaskan minimal 10 karakter',
                'documents.0.required' => 'Dokumen pertama (Surat Pengantar) wajib diupload',
                'documents.1.required' => 'Dokumen kedua (Proposal/Karya Ilmiah) wajib diupload untuk Non-PNBP',
                'documents.2.required' => 'Dokumen ketiga (Dokumen Pendukung) wajib diupload untuk Non-PNBP',
                'documents.*.max' => 'Ukuran file tidak boleh lebih dari 5MB',
                'documents.*.mimes' => 'Format file harus: PDF, DOC, DOCX, JPG, JPEG, atau PNG'
            ]);

            $guideline = Guideline::findOrFail($request->guideline_id);

            // Generate unique application number dengan format yang lebih robust
            $lastApplication = Application::whereDate('created_at', today())->latest()->first();
            $dailySequence = $lastApplication ?
                intval(substr($lastApplication->application_number, -4)) + 1 : 1;
            $applicationNumber = 'BMKG' . date('md') . '/' . date('Y') . '/' . str_pad($dailySequence, 4, '0', STR_PAD_LEFT);

            // ENHANCED: Document processing dengan lebih banyak metadata
            $documents = [];
            $totalSize = 0;

            if ($request->hasFile('documents') && is_array($request->file('documents'))) {
                foreach ($request->file('documents') as $index => $file) {
                    if (!$file) continue;

                    // Validate total size
                    $totalSize += $file->getSize();
                    if ($totalSize > (20 * 1024 * 1024)) {
                        throw new \Exception('Total ukuran semua file tidak boleh lebih dari 20MB');
                    }

                    // Generate safe filename dengan timestamp yang unik
                    $originalName = $file->getClientOriginalName();
                    $extension = $file->getClientOriginalExtension();
                    $safeName = preg_replace('/[^a-zA-Z0-9._-]/', '_', pathinfo($originalName, PATHINFO_FILENAME));
                    $uniqueId = uniqid();
                    $filename = time() . '_' . $uniqueId . '_' . $index . '_' . $safeName . '.' . $extension;

                    // Folder structure yang lebih terorganisir
                    $folderPath = $request->type === 'pnbp' ? 'applications/pnbp' : 'applications/non_pnbp';
                    $yearMonth = date('Y/m');
                    $fullPath = $folderPath . '/' . $yearMonth;

                    // Store dengan error handling
                    $path = $file->storeAs($fullPath, $filename, 'public');

                    if (!$path) {
                        throw new \Exception('Gagal menyimpan file: ' . $originalName);
                    }

                    $documents[] = [
                        'index' => $index,
                        'original_name' => $originalName,
                        'stored_name' => $filename,
                        'path' => $path,
                        'size' => $file->getSize(),
                        'size_mb' => round($file->getSize() / 1024 / 1024, 2),
                        'type' => $file->getClientMimeType(),
                        'extension' => $extension,
                        'folder' => $fullPath,
                        'uploaded_at' => now()->toISOString(),
                        'unique_id' => $uniqueId
                    ];
                }
            }

            // Validate minimum documents
            $minDocuments = $request->type === 'pnbp' ? 1 : 3;
            if (count($documents) < $minDocuments) {
                throw new \Exception("Minimal {$minDocuments} dokumen harus diupload untuk " . strtoupper($request->type));
            }

            // ENHANCED: Date context analysis
            $today = now()->format('Y-m-d');
            $startDate = $request->tanggal_mulai;
            $endDate = $request->tanggal_selesai;

            $dateContext = [
                'start_date_type' => $this->getDateType($startDate, $today),
                'end_date_type' => $this->getDateType($endDate, $today),
                'date_range_days' => Carbon::parse($startDate)->diffInDays(Carbon::parse($endDate)) + 1,
                'is_historical_request' => $endDate < '1990-01-01',
                'is_current_request' => $startDate >= '1990-01-01' && $endDate <= $today,
                'is_future_request' => $startDate > $today,
                'is_mixed_request' => $startDate <= $today && $endDate > $today,
                'data_availability_status' => $this->assessDataAvailability($startDate, $endDate, $today),
                'period_classification' => $this->classifyPeriod($startDate, $endDate),
                'estimated_processing_days' => $this->estimateProcessingTime($startDate, $endDate, $request->type)
            ];

            // Create application
            $application = Application::create([
                'user_id' => Auth::id(),
                'guideline_id' => $request->guideline_id,
                'application_number' => $applicationNumber,
                'type' => $request->type,
                'documents' => $documents,
                'start_date' => $startDate,
                'end_date' => $endDate,
                'purpose' => $request->keperluan,
                'status' => 'pending',
                'notes' => json_encode($dateContext) // Store additional context
            ]);

            // Enhanced logging
            Log::info('Application submitted with free date selection', [
                'user_id' => Auth::id(),
                'application_number' => $applicationNumber,
                'type' => $request->type,
                'documents_count' => count($documents),
                'storage_folder' => $fullPath ?? 'unknown',
                'date_range' => $startDate . ' to ' . $endDate,
                'date_context' => $dateContext,
                'total_file_size_mb' => round($totalSize / 1024 / 1024, 2)
            ]);

            // Log history with enhanced information
            $application->logHistory(
                'submitted',
                'user',
                Auth::id(),
                'Pengajuan Surat/Data Disubmit',
                "User mengajukan {$application->guideline->title} untuk periode {$startDate} s/d {$endDate}",
                array_merge([
                    'guideline_title' => $application->guideline->title,
                    'documents_uploaded' => count($documents),
                    'type' => $request->type,
                    'storage_folder' => $fullPath ?? 'unknown',
                    'purpose' => $request->keperluan,
                    'date_range' => $startDate . ' to ' . $endDate,
                    'total_file_size' => $totalSize,
                    'documents_detail' => array_map(function ($doc) {
                        return [
                            'name' => $doc['original_name'],
                            'size_mb' => $doc['size_mb']
                        ];
                    }, $documents)
                ], $dateContext)
            );

            return response()->json([
                'success' => true,
                'message' => 'Application submitted successfully',
                'application_number' => $applicationNumber,
                'storage_path' => $fullPath ?? null,
                'documents_count' => count($documents),
                'total_size' => number_format($totalSize / 1024 / 1024, 2) . ' MB',
                'date_info' => $dateContext,
                'estimated_processing' => $dateContext['estimated_processing_days'] . ' hari kerja'
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
                'request_data' => $request->except('documents')
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to submit application: ' . $e->getMessage()
            ], 500);
        }
    }

    // ENHANCED: Helper methods
    private function getDateType($date, $today)
    {
        if ($date > $today) {
            return 'future';
        } elseif ($date >= '1990-01-01') {
            return 'available';
        } else {
            return 'historical';
        }
    }

    private function assessDataAvailability($startDate, $endDate, $today)
    {
        if ($endDate < '1990-01-01') {
            return 'very_limited';
        } elseif ($startDate >= '1990-01-01' && $endDate <= $today) {
            return 'fully_available';
        } elseif ($startDate > $today) {
            return 'projection_needed';
        } elseif ($startDate <= $today && $endDate > $today) {
            return 'mixed_historical_projection';
        } else {
            return 'needs_assessment';
        }
    }

    // ADDED: Additional helper methods
    private function classifyPeriod($startDate, $endDate)
    {
        $diffDays = Carbon::parse($startDate)->diffInDays(Carbon::parse($endDate)) + 1;

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

    private function estimateProcessingTime($startDate, $endDate, $type)
    {
        $diffDays = Carbon::parse($startDate)->diffInDays(Carbon::parse($endDate)) + 1;
        $baseTime = $type === 'pnbp' ? 3 : 5; // Base processing days
        $today = now()->format('Y-m-d');

        // Add complexity based on date range
        if ($diffDays > 1826) { // > 5 years
            $baseTime += 7;
        } elseif ($diffDays > 366) { // > 1 year
            $baseTime += 3;
        }

        // Add complexity for historical data
        if ($endDate < '1990-01-01') {
            $baseTime += 5;
        }

        // Add complexity for future projection
        if ($startDate > $today) {
            $baseTime += 3;
        }

        return $baseTime;
    }

    // REST OF THE METHODS REMAIN THE SAME...
    public function uploadPaymentProof(Request $request, $id)
    {
        try {
            $request->validate([
                'payment_proof' => 'required|file|mimes:pdf,jpg,jpeg,png|max:2048'
            ], [
                'payment_proof.required' => 'File bukti pembayaran wajib diupload',
                'payment_proof.mimes' => 'Format file harus: PDF, JPG, JPEG, atau PNG',
                'payment_proof.max' => 'Ukuran file tidak boleh lebih dari 2MB'
            ]);

            $application = Application::where('user_id', Auth::id())->findOrFail($id);

            if (!in_array($application->status, ['verified', 'payment_pending'])) {
                throw new \Exception('Aplikasi ini tidak dalam status yang memungkinkan upload pembayaran');
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

                // Generate safe filename
                $extension = $file->getClientOriginalExtension();
                $filename = 'payment_' . str_replace(['/', '\\'], '_', $application->application_number) . '_' . time() . '.' . $extension;

                // Organized storage
                $folderPath = $application->type === 'pnbp' ? 'payments/pnbp' : 'payments/non_pnbp';
                $yearMonth = date('Y/m');
                $fullPath = $folderPath . '/' . $yearMonth;

                // Delete old payment proof if exists
                if ($payment->payment_proof && Storage::disk('public')->exists($payment->payment_proof)) {
                    Storage::disk('public')->delete($payment->payment_proof);
                }

                $path = $file->storeAs($fullPath, $filename, 'public');

                if (!$path) {
                    throw new \Exception('Gagal menyimpan file bukti pembayaran');
                }

                $payment->update([
                    'payment_proof' => $path,
                    'status' => 'pending',
                    'paid_at' => now()
                ]);

                $application->update(['status' => 'payment_pending']);

                // Log history
                $application->logHistory(
                    'payment_uploaded',
                    'user',
                    Auth::id(),
                    'Bukti Pembayaran Diupload',
                    "User mengupload bukti pembayaran sebesar Rp " . number_format($payment->amount),
                    [
                        'amount' => $payment->amount,
                        'file_path' => $path,
                        'storage_folder' => $fullPath,
                        'file_size' => $file->getSize(),
                        'file_original_name' => $file->getClientOriginalName()
                    ]
                );

                Log::info('Payment proof uploaded', [
                    'application_id' => $application->id,
                    'user_id' => Auth::id(),
                    'file_path' => $path
                ]);

                return response()->json([
                    'success' => true,
                    'message' => 'Payment proof uploaded successfully',
                    'path' => $path,
                    'storage_folder' => $fullPath
                ]);
            }

            throw new \Exception('Tidak ada file yang diupload');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Payment proof upload failed', [
                'application_id' => $id,
                'user_id' => Auth::id(),
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to upload payment proof: ' . $e->getMessage()
            ], 500);
        }
    }

    // Continue with the rest of your existing methods...
    public function downloadDocument($id)
    {
        try {
            $document = \App\Models\GeneratedDocument::whereHas('application', function ($query) {
                $query->where('user_id', Auth::id());
            })->findOrFail($id);

            $path = storage_path('app/public/' . $document->document_path);

            if (!file_exists($path)) {
                Log::warning('Document file not found', [
                    'document_id' => $id,
                    'user_id' => Auth::id(),
                    'path' => $path
                ]);
                abort(404, 'File not found');
            }

            // Log download activity
            Log::info('Document downloaded', [
                'document_id' => $id,
                'user_id' => Auth::id(),
                'application_number' => $document->application->application_number
            ]);

            return response()->download($path, $document->document_name);
        } catch (\Exception $e) {
            Log::error('Document download failed', [
                'document_id' => $id,
                'user_id' => Auth::id(),
                'error' => $e->getMessage()
            ]);
            abort(404, 'Document not found');
        }
    }

    public function history()
    {
        try {
            $applications = Auth::user()->applications()
                ->with(['guideline', 'payment', 'generatedDocuments', 'histories' => function ($query) {
                    $query->orderBy('created_at', 'desc');
                }])
                ->orderBy('created_at', 'desc')
                ->get();

            return response()->json(['data' => $applications]);
        } catch (\Exception $e) {
            Log::error('Failed to load history: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to load history'], 500);
        }
    }

    public function profile()
    {
        try {
            $user = Auth::user();
            $profileData = [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'phone' => $user->phone,
                'role' => $user->role,
                'created_at' => $user->created_at->format('d/m/Y H:i'),
                'email_verified_at' => $user->email_verified_at ? $user->email_verified_at->format('d/m/Y H:i') : null
            ];

            return response()->json($profileData);
        } catch (\Exception $e) {
            Log::error('Failed to load profile: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to load profile'], 500);
        }
    }

    public function updateProfile(Request $request)
    {
        try {
            $request->validate([
                'name' => 'required|string|max:255|min:2',
                'email' => 'required|email|unique:users,email,' . Auth::id(),
                'phone' => 'nullable|string|max:15|regex:/^[0-9+\-\s]+$/'
            ], [
                'name.min' => 'Nama harus minimal 2 karakter',
                'email.unique' => 'Email sudah digunakan oleh user lain',
                'phone.regex' => 'Format nomor telepon tidak valid'
            ]);

            $user = Auth::user();
            $oldData = [
                'name' => $user->name,
                'email' => $user->email,
                'phone' => $user->phone
            ];

            $user->update($request->only(['name', 'email', 'phone']));

            Log::info('Profile updated', [
                'user_id' => $user->id,
                'old_data' => $oldData,
                'new_data' => $request->only(['name', 'email', 'phone'])
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Profile updated successfully',
                'user' => $user->only(['name', 'email', 'phone'])
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Profile update failed: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to update profile'], 500);
        }
    }

    public function changePassword(Request $request)
    {
        try {
            $request->validate([
                'current_password' => 'required',
                'new_password' => 'required|min:8|confirmed|different:current_password'
            ], [
                'new_password.min' => 'Password baru harus minimal 8 karakter',
                'new_password.confirmed' => 'Konfirmasi password tidak cocok',
                'new_password.different' => 'Password baru harus berbeda dari password lama'
            ]);

            if (!Hash::check($request->current_password, Auth::user()->password)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Password lama tidak benar'
                ], 422);
            }

            Auth::user()->update([
                'password' => Hash::make($request->new_password)
            ]);

            Log::info('Password changed', ['user_id' => Auth::id()]);

            return response()->json([
                'success' => true,
                'message' => 'Password berhasil diubah'
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Password change failed: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to change password'], 500);
        }
    }

    // Additional methods...
    public function getApplicationDetails($id)
    {
        try {
            $application = Application::where('user_id', Auth::id())
                ->with(['guideline', 'payment', 'generatedDocuments'])
                ->findOrFail($id);

            return response()->json([
                'success' => true,
                'application' => $application
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Application not found'
            ], 404);
        }
    }

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
                ['cancelled_at' => now()]
            );

            return response()->json([
                'success' => true,
                'message' => 'Application cancelled successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to cancel application'
            ], 500);
        }
    }

    public function getDateStatistics()
    {
        try {
            $applications = Auth::user()->applications()->get();
            $today = now()->format('Y-m-d');

            $stats = [
                'historical_requests' => $applications->filter(function ($app) {
                    return $app->end_date && $app->end_date < '1990-01-01';
                })->count(),
                'available_data_requests' => $applications->filter(function ($app) use ($today) {
                    return $app->start_date >= '1990-01-01' && $app->end_date <= $today;
                })->count(),
                'future_requests' => $applications->filter(function ($app) use ($today) {
                    return $app->start_date > $today;
                })->count(),
                'mixed_requests' => $applications->filter(function ($app) use ($today) {
                    return $app->start_date <= $today && $app->end_date > $today;
                })->count(),
                'average_range_days' => $applications->avg(function ($app) {
                    return $app->duration_days;
                })
            ];

            return response()->json([
                'success' => true,
                'statistics' => $stats
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to get statistics'
            ], 500);
        }
    }
}
