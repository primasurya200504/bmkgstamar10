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
use Carbon\Carbon;

class AdminController extends Controller
{
    public function dashboard()
    {
        $stats = [
            'pending_requests' => Application::where('status', 'pending')->count(),
            'pending_payments' => Application::where('status', 'payment_pending')->count(),
            'processing' => Application::where('status', 'processing')->count(),
            'completed' => Application::where('status', 'completed')->count(),
            'total_users' => User::where('role', 'user')->count()
        ];

        $recent_applications = Application::with(['user', 'guideline'])
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get();

        return view('admin.admin_dashboard', compact('stats', 'recent_applications'));
    }

    // Manajemen Permintaan
    public function requests()
    {
        try {
            $applications = Application::with(['user', 'guideline', 'payment'])
                ->orderBy('created_at', 'desc')
                ->paginate(20);

            return response()->json($applications);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to load requests'], 500);
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

                        // Log billing creation
                        $application->logHistory(
                            'billing_generated',
                            'system',
                            null,
                            'Tagihan Pembayaran Dibuat',
                            "Sistem membuat tagihan pembayaran PNBP sebesar Rp " . number_format($application->guideline->fee),
                            [
                                'amount' => $application->guideline->fee,
                                'billing_type' => 'PNBP',
                                'payment_method' => 'Upload Bukti Transfer'
                            ]
                        );
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
            return response()->json(['error' => 'Failed to verify request'], 500);
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

    // Method documents yang diperbaiki
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

    // Method uploadDocument yang diperbaiki
    public function uploadDocument(Request $request, $id)
    {
        try {
            $request->validate([
                'document' => 'required|file|mimes:pdf,doc,docx,xls,xlsx|max:10240',
                'document_name' => 'required|string|max:255'
            ]);

            $application = Application::findOrFail($id);

            if ($request->hasFile('document')) {
                $file = $request->file('document');
                $filename = time() . '_' . $file->getClientOriginalName();
                $path = $file->storeAs('generated_documents', $filename, 'public');

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
                    "Admin mengupload dokumen '{$request->document_name}' untuk user. File siap diunduh.",
                    [
                        'document_name' => $request->document_name,
                        'file_path' => $path,
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
                        'completed_by' => Auth::user()->name
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
                        'archive_reason' => 'Completion'
                    ]
                );
            }

            return response()->json(['success' => true, 'message' => 'Document uploaded successfully']);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to upload document: ' . $e->getMessage()], 500);
        }
    }

    public function completeApplication($id)
    {
        try {
            $application = Application::findOrFail($id);
            $application->update(['status' => 'completed']);

            // Auto archive
            Archive::create([
                'application_id' => $application->id,
                'archive_date' => now(),
                'notes' => 'Manually completed by admin'
            ]);

            return response()->json(['success' => true, 'message' => 'Application completed']);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to complete application'], 500);
        }
    }

    // Enhanced archives method dengan detailed history
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

            // Filter berdasarkan rentang tanggal
            if ($request->date_from) {
                $query->whereDate('created_at', '>=', $request->date_from);
            }

            if ($request->date_to) {
                $query->whereDate('created_at', '<=', $request->date_to);
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

    // Method baru untuk detail timeline
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

    // Download complete archive (optional)
    public function downloadArchive($id)
    {
        try {
            $application = Application::with(['generatedDocuments', 'payment'])->findOrFail($id);

            // Create ZIP file with all related documents
            $zip = new \ZipArchive();
            $zipFileName = "archive_{$application->application_number}_" . now()->format('Y-m-d') . ".zip";
            $zipPath = storage_path('app/temp/' . $zipFileName);

            if ($zip->open($zipPath, \ZipArchive::CREATE) === TRUE) {
                // Add generated documents
                foreach ($application->generatedDocuments as $doc) {
                    $filePath = storage_path('app/public/' . $doc->document_path);
                    if (file_exists($filePath)) {
                        $zip->addFile($filePath, 'documents/' . $doc->document_name);
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

                return response()->download($zipPath)->deleteFileAfterSend();
            }

            return response()->json(['error' => 'Failed to create archive'], 500);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to download archive'], 500);
        }
    }

    // Rest of the methods remain the same (guidelines, users, etc.)
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

    // Manajemen Pengguna
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
