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
    // PERBAIKAN UTAMA: Method dashboard untuk mengembalikan object, bukan array
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

            // PERBAIKAN: Kembalikan Collection object, bukan array
            $recent_applications = Application::with(['user', 'guideline'])
                ->orderBy('created_at', 'desc')
                ->take(10)
                ->get();

            // Tambahkan computed attributes ke setiap object agar view bisa mengaksesnya
            $recent_applications->each(function ($app) {
                // Tambahkan attributes virtual yang bisa diakses di view
                $app->type_label = $app->type === 'pnbp' ? 'PNBP (Berbayar)' : 'Non-PNBP (Gratis)';
                $app->status_label = $this->getStatusLabel($app->status);
                $app->created_at_formatted = $app->created_at->format('d/m/Y H:i');
                $app->date_range_display = ($app->start_date && $app->end_date)
                    ? $app->start_date->format('d/m/Y') . ' - ' . $app->end_date->format('d/m/Y')
                    : 'Tanggal tidak tersedia';
                $app->duration_days = ($app->start_date && $app->end_date)
                    ? $app->start_date->diffInDays($app->end_date) + 1
                    : 0;
                $app->documents_count = is_array($app->documents) ? count($app->documents) : 0;
            });

            return view('admin.admin_dashboard', compact('stats', 'recent_applications'));
        } catch (\Exception $e) {
            Log::error('Dashboard error: ' . $e->getMessage());

            // Fallback jika ada error
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

    // TAMBAHAN: Helper method untuk status label
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

    // TAMBAHAN: Method untuk mendapatkan detail aplikasi (untuk modal)
    public function getApplicationDetail($id)
    {
        try {
            $application = Application::with([
                'user:id,name,email,phone',
                'guideline:id,title,description,type,fee,required_documents',
                'histories' => function ($query) {
                    $query->orderBy('created_at', 'desc')->take(5);
                },
                'payment',
                'generatedDocuments'
            ])->findOrFail($id);

            return response()->json([
                'success' => true,
                'data' => [
                    'id' => $application->id,
                    'application_number' => $application->application_number,
                    'user' => $application->user,
                    'guideline' => $application->guideline,
                    'type' => $application->type,
                    'type_label' => $application->type === 'pnbp' ? 'PNBP (Berbayar)' : 'Non-PNBP (Gratis)',
                    'status' => $application->status,
                    'status_label' => $this->getStatusLabel($application->status),
                    'purpose' => $application->purpose,
                    'start_date' => $application->start_date ? $application->start_date->format('d/m/Y') : '-',
                    'end_date' => $application->end_date ? $application->end_date->format('d/m/Y') : '-',
                    'date_range_display' => ($application->start_date && $application->end_date)
                        ? $application->start_date->format('d/m/Y') . ' - ' . $application->end_date->format('d/m/Y')
                        : 'Tanggal tidak tersedia',
                    'documents' => $application->documents,
                    'documents_count' => is_array($application->documents) ? count($application->documents) : 0,
                    'notes' => $application->notes,
                    'created_at' => $application->created_at->format('d/m/Y H:i:s'),
                    'updated_at' => $application->updated_at->format('d/m/Y H:i:s'),
                    'histories' => $application->histories,
                    'payment' => $application->payment,
                    'generated_documents' => $application->generatedDocuments
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Application not found: ' . $e->getMessage()
            ], 404);
        }
    }

    // Manajemen Permintaan - DIPERBAIKI
    public function requests()
    {
        try {
            $applications = Application::with(['user', 'guideline', 'payment'])
                ->orderBy('created_at', 'desc')
                ->paginate(20);

            // Transform data untuk konsistensi
            $applications->getCollection()->transform(function ($app) {
                $app->type_label = $app->type === 'pnbp' ? 'PNBP (Berbayar)' : 'Non-PNBP (Gratis)';
                $app->status_label = $this->getStatusLabel($app->status);
                $app->created_at_formatted = $app->created_at->format('d/m/Y H:i');
                $app->documents_count = is_array($app->documents) ? count($app->documents) : 0;
                return $app;
            });

            return response()->json($applications);
        } catch (\Exception $e) {
            Log::error('Requests loading error: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to load requests: ' . $e->getMessage()], 500);
        }
    }

    public function verifyRequest(Request $request, $id)
    {
        try {
            $application = Application::findOrFail($id);

            if ($request->action === 'approve') {
                if ($application->type === 'pnbp' && $application->guideline->fee > 0) {
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
                            'approved_by' => Auth::user()->name
                        ]
                    );

                    // Cek apakah payment sudah ada
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
                            'approved_by' => Auth::user()->name
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
                        'rejected_by' => Auth::user()->name
                    ]
                );
            }

            return response()->json(['success' => true, 'message' => 'Request updated successfully']);
        } catch (\Exception $e) {
            Log::error('Verify request error: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to verify request: ' . $e->getMessage()], 500);
        }
    }

    // Manajemen Pembayaran
    public function payments()
    {
        try {
            $payments = Payment::with(['application.user', 'application.guideline'])
                ->whereNotNull('payment_proof')
                ->orderBy('created_at', 'desc')
                ->paginate(20);

            return response()->json($payments);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to load payments'], 500);
        }
    }

    public function verifyPayment(Request $request, $id)
    {
        try {
            $payment = Payment::findOrFail($id);

            $payment->update([
                'status' => $request->action === 'approve' ? 'verified' : 'rejected',
                'paid_at' => $request->action === 'approve' ? now() : null
            ]);

            if ($request->action === 'approve') {
                $payment->application->update(['status' => 'paid']);

                // Log payment verification
                $payment->application->logHistory(
                    'payment_verified',
                    'admin',
                    Auth::id(),
                    'Pembayaran Diverifikasi',
                    "Admin memverifikasi pembayaran PNBP sebesar Rp " . number_format($payment->amount) . " dan mengubah status menjadi 'Sudah Bayar'",
                    [
                        'amount' => $payment->amount,
                        'payment_method' => 'Upload Bukti Transfer',
                        'verified_at' => now(),
                        'verified_by' => Auth::user()->name,
                        'payment_proof_file' => $payment->payment_proof
                    ]
                );
            } else {
                $payment->application->update(['status' => 'payment_pending']);

                // Log payment rejection
                $payment->application->logHistory(
                    'payment_rejected',
                    'admin',
                    Auth::id(),
                    'Pembayaran Ditolak',
                    'Admin menolak bukti pembayaran yang diupload user karena tidak valid atau tidak sesuai',
                    [
                        'amount' => $payment->amount,
                        'rejection_reason' => 'Bukti pembayaran tidak valid',
                        'rejected_by' => Auth::user()->name
                    ]
                );
            }

            return response()->json(['success' => true, 'message' => 'Payment updated successfully']);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to verify payment'], 500);
        }
    }

    // Documents method yang diperbaiki
    public function documents()
    {
        try {
            $applications = Application::with(['user', 'guideline'])
                ->where('status', 'paid')
                ->whereDoesntHave('generatedDocuments')
                ->orderBy('created_at', 'desc')
                ->get();

            return response()->json(['data' => $applications]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to load documents'], 500);
        }
    }

    // UPDATED: Upload Document dengan storage terorganisir
    public function uploadDocument(Request $request, $id)
    {
        try {
            $request->validate([
                'document' => 'required|file|mimes:pdf,doc,docx,xls,xlsx|max:10240', // 10MB
                'document_name' => 'required|string|max:255'
            ]);

            $application = Application::findOrFail($id);

            if ($request->hasFile('document')) {
                $file = $request->file('document');
                $filename = 'result_' . $application->application_number . '_' . time() . '.' . $file->getClientOriginalExtension();

                // STORAGE TERORGANISIR: Folder terpisah berdasarkan tipe dan bulan
                $folderPath = $application->type === 'pnbp' ? 'results/pnbp' : 'results/non_pnbp';
                $yearMonth = date('Y/m');
                $fullPath = $folderPath . '/' . $yearMonth;

                // Store file dengan path terorganisir
                $path = $file->storeAs($fullPath, $filename, 'public');

                // Save to generated_documents table
                GeneratedDocument::create([
                    'application_id' => $application->id,
                    'document_path' => $path,
                    'document_name' => $request->document_name
                ]);

                // Log document upload
                $application->logHistory(
                    'document_uploaded',
                    'admin',
                    Auth::id(),
                    'Dokumen Data/Surat Diupload',
                    "Admin mengupload dokumen '{$request->document_name}' untuk user. File disimpan di folder {$fullPath}.",
                    [
                        'document_name' => $request->document_name,
                        'file_path' => $path,
                        'storage_folder' => $fullPath,
                        'file_original_name' => $file->getClientOriginalName(),
                        'file_size' => $file->getSize(),
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
                    'Seluruh proses pengajuan telah selesai. User dapat mengunduh dokumen dari dashboard.',
                    [
                        'completion_date' => now(),
                        'total_process_days' => $application->created_at->diffInDays(now()),
                        'completed_by' => Auth::user()->name,
                        'document_location' => $fullPath
                    ]
                );

                // Auto archive
                Archive::create([
                    'application_id' => $application->id,
                    'archive_date' => now(),
                    'notes' => 'Automatically archived upon completion'
                ]);

                // Log archiving
                $application->logHistory(
                    'archived',
                    'system',
                    null,
                    'Pengajuan Diarsipkan',
                    'Sistem otomatis mengarsipkan pengajuan yang telah selesai ke dalam database arsip.',
                    [
                        'archive_date' => now(),
                        'auto_archived' => true,
                        'archive_reason' => 'Completion',
                        'storage_location' => $fullPath
                    ]
                );

                return response()->json([
                    'success' => true,
                    'message' => 'Document uploaded successfully',
                    'path' => $path,
                    'storage_folder' => $fullPath
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Document upload failed: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to upload document: ' . $e->getMessage()], 500);
        }
    }

    // Enhanced archives method
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

            // Filter berdasarkan bulan
            if ($request->month) {
                $query->whereMonth('created_at', $request->month);
            }

            // Filter berdasarkan tahun
            if ($request->year) {
                $query->whereYear('created_at', $request->year);
            }

            // Filter berdasarkan tipe
            if ($request->type) {
                $query->where('type', $request->type);
            }

            $applications = $query->orderBy('created_at', 'desc')->paginate(20);

            return response()->json($applications);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to load archives'], 500);
        }
    }

    // Method untuk detail timeline
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

            return response()->json([
                'application' => $application,
                'timeline' => $application->histories
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to load timeline'], 500);
        }
    }

    // UPDATED: Download complete archive dengan file terorganisir
    public function downloadArchive($id)
    {
        try {
            $application = Application::with(['generatedDocuments', 'payment', 'user'])->findOrFail($id);

            // Create temporary directory
            $tempDir = storage_path('app/temp');
            if (!file_exists($tempDir)) {
                mkdir($tempDir, 0755, true);
            }

            // Create ZIP file
            $zip = new ZipArchive();
            $zipFileName = "archive_{$application->application_number}_" . now()->format('Y-m-d') . ".zip";
            $zipPath = $tempDir . '/' . $zipFileName;

            if ($zip->open($zipPath, ZipArchive::CREATE) === TRUE) {
                // Add application info as JSON
                $appInfo = [
                    'application_number' => $application->application_number,
                    'user_name' => $application->user->name,
                    'user_email' => $application->user->email,
                    'guideline' => $application->guideline->title,
                    'type' => $application->type,
                    'start_date' => $application->start_date,
                    'end_date' => $application->end_date,
                    'purpose' => $application->purpose,
                    'status' => $application->status,
                    'created_at' => $application->created_at,
                    'completed_at' => $application->updated_at
                ];
                $zip->addFromString('application_info.json', json_encode($appInfo, JSON_PRETTY_PRINT));

                // Add original user documents
                if ($application->documents && is_array($application->documents)) {
                    foreach ($application->documents as $index => $docPath) {
                        $filePath = storage_path('app/public/' . $docPath);
                        if (file_exists($filePath)) {
                            $zip->addFile($filePath, "user_documents/document_" . ($index + 1) . "_" . basename($filePath));
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

                return response()->download($zipPath)->deleteFileAfterSend(true);
            }

            return response()->json(['error' => 'Failed to create archive'], 500);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to download archive: ' . $e->getMessage()], 500);
        }
    }

    // Rest of methods remain the same
    public function guidelines()
    {
        try {
            $guidelines = Guideline::orderBy('created_at', 'desc')->get();
            return response()->json($guidelines);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to load guidelines'], 500);
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

            Guideline::create($request->all());

            return response()->json(['success' => true, 'message' => 'Guideline created successfully']);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to create guideline'], 500);
        }
    }

    public function showGuideline($id)
    {
        try {
            $guideline = Guideline::findOrFail($id);
            return response()->json($guideline);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Guideline not found'], 404);
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

            return response()->json(['success' => true, 'message' => 'Guideline updated successfully']);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to update guideline'], 500);
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
            return response()->json(['success' => true, 'message' => 'Guideline deleted successfully']);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error deleting guideline: ' . $e->getMessage()
            ], 500);
        }
    }

    // User management methods
    public function users()
    {
        try {
            $users = User::where('role', 'user')
                ->select('id', 'name', 'email', 'phone', 'created_at')
                ->orderBy('created_at', 'desc')
                ->paginate(20);

            return response()->json($users);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to load users'], 500);
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

            User::create([
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
                'password' => Hash::make($request->password),
                'role' => $request->role,
                'email_verified_at' => now(),
            ]);

            return response()->json(['success' => true, 'message' => 'User created successfully']);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to create user'], 500);
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

            if ($request->password) {
                $updateData['password'] = Hash::make($request->password);
            }

            $user->update($updateData);

            return response()->json(['success' => true, 'message' => 'User updated successfully']);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to update user'], 500);
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

            return response()->json(['success' => true, 'message' => 'User deleted successfully']);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to delete user'], 500);
        }
    }
}
