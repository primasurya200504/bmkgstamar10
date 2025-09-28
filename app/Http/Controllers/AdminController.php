<?php

namespace App\Http\Controllers;

use App\Models\Submission;
use App\Models\SubmissionHistory;
use App\Models\Guideline;
use App\Models\Payment;
use App\Models\User;
use App\Models\GeneratedDocument;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AdminController extends Controller
{
    /**
     * Dashboard Admin
     */
    public function dashboard()
    {
        try {
            // Test database connection
            DB::connection()->getPdo();

            $stats = [
                'pending_requests' => Submission::where('status', 'pending')->count(),
                'pending_payments' => Payment::where('status', 'uploaded')->count(),
                'processing' => Submission::where('status', 'processing')->count(),
                'completed' => Submission::where('status', 'completed')->count(),
                'total_users' => User::where('role', 'user')->count()
            ];

            // Get recent submissions
            $recentSubmissions = Submission::with(['user', 'guideline'])
                ->orderBy('created_at', 'desc')
                ->take(10)
                ->get();

            Log::info('Admin Dashboard loaded successfully', [
                'stats' => $stats,
                'submissions_count' => $recentSubmissions->count()
            ]);

            return view('admin.admin_dashboard', compact('stats', 'recentSubmissions'));
        } catch (\Exception $e) {
            Log::error('Admin Dashboard Error: ' . $e->getMessage());

            $stats = [
                'pending_requests' => 0,
                'pending_payments' => 0,
                'processing' => 0,
                'completed' => 0,
                'total_users' => 0
            ];

            $recentSubmissions = collect();

            return view('admin.admin_dashboard', compact('stats', 'recentSubmissions'))
                ->with('error', 'Database connection issue: ' . $e->getMessage());
        }
    }

    // AJAX Methods untuk Admin Dashboard
    public function getSubmissions()
    {
        try {
            $submissions = Submission::with(['user', 'guideline', 'payment'])
                ->orderBy('created_at', 'desc')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $submissions
            ]);
        } catch (\Exception $e) {
            Log::error('Error loading submissions: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Error loading submissions',
                'data' => []
            ], 500);
        }
    }

    public function getPayments()
    {
        try {
            $payments = Payment::with(['submission.user', 'submission.guideline'])
                ->whereHas('submission')
                ->orderBy('created_at', 'desc')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $payments
            ]);
        } catch (\Exception $e) {
            Log::error('Error loading payments: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Error loading payments',
                'data' => []
            ], 500);
        }
    }

    public function getDocuments()
    {
        try {
            $submissions = Submission::with(['user', 'guideline'])
                ->where('status', 'paid')
                ->orderBy('created_at', 'desc')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $submissions
            ]);
        } catch (\Exception $e) {
            Log::error('Error loading documents: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Error loading documents',
                'data' => []
            ], 500);
        }
    }

    public function getGuidelines()
    {
        try {
            $guidelines = Guideline::orderBy('created_at', 'desc')->get();

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

    public function getArchives(Request $request)
    {
        try {
            $query = Submission::with(['user', 'guideline'])
                ->where('status', 'completed');

            // Apply filters
            if ($request->has('year') && !empty($request->year)) {
                $query->whereYear('updated_at', $request->year);
            }

            if ($request->has('month') && !empty($request->month)) {
                $query->whereMonth('updated_at', $request->month);
            }

            if ($request->has('type') && !empty($request->type)) {
                $query->where('type', $request->type);
            }

            $submissions = $query->orderBy('updated_at', 'desc')->get();

            return response()->json([
                'success' => true,
                'data' => $submissions
            ]);
        } catch (\Exception $e) {
            Log::error('Error loading archives: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Error loading archives',
                'data' => []
            ], 500);
        }
    }

    public function getUsers()
    {
        try {
            $users = User::where('role', 'user')
                ->orderBy('created_at', 'desc')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $users
            ]);
        } catch (\Exception $e) {
            Log::error('Error loading users: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Error loading users',
                'data' => []
            ], 500);
        }
    }

    // Detail Methods
    public function getSubmissionDetail($id)
    {
        try {
            $submission = Submission::with(['user', 'guideline', 'payment'])
                ->findOrFail($id);

            return response()->json([
                'success' => true,
                'data' => [
                    'submission' => $submission,
                    'user' => $submission->user,
                    'guideline' => $submission->guideline
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Submission not found'
            ], 404);
        }
    }

    public function getPaymentDetail($id)
    {
        try {
            $payment = Payment::with(['submission.user', 'submission.guideline'])
                ->findOrFail($id);

            return response()->json([
                'success' => true,
                'data' => [
                    'payment' => $payment,
                    'submission' => $payment->submission,
                    'user' => $payment->submission->user
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Payment not found'
            ], 404);
        }
    }

    // Action Methods
    public function verifySubmission(Request $request, $id)
    {
        try {
            $submission = Submission::findOrFail($id);

            if ($request->action === 'approve') {
                $submission->update(['status' => 'verified']);

                // Create payment record if PNBP
                if ($submission->type === 'pnbp' && $submission->guideline->fee > 0) {
                    $submission->update(['status' => 'payment_pending']);
                }

                SubmissionHistory::create([
                    'submission_id' => $submission->id,
                    'actor_id' => Auth::id(),
                    'actor_type' => 'admin',
                    'action' => 'verified',
                    'title' => 'Pengajuan Disetujui',
                    'description' => 'Pengajuan telah diverifikasi dan disetujui oleh admin'
                ]);
            } else {
                $submission->update(['status' => 'rejected']);

                SubmissionHistory::create([
                    'submission_id' => $submission->id,
                    'actor_id' => Auth::id(),
                    'actor_type' => 'admin',
                    'action' => 'rejected',
                    'title' => 'Pengajuan Ditolak',
                    'description' => $request->notes ?? 'Pengajuan ditolak oleh admin'
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Submission updated successfully'
            ]);
        } catch (\Exception $e) {
            Log::error('Error verifying submission: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Error updating submission'
            ], 500);
        }
    }

    public function verifyPayment(Request $request, $id)
    {
        try {
            $payment = Payment::findOrFail($id);
            $submission = $payment->submission;

            if ($request->action === 'approve') {
                $payment->update(['status' => 'verified']);
                $submission->update(['status' => 'paid']);

                SubmissionHistory::create([
                    'submission_id' => $submission->id,
                    'actor_id' => Auth::id(),
                    'actor_type' => 'admin',
                    'action' => 'payment_verified',
                    'title' => 'Pembayaran Diverifikasi',
                    'description' => 'Bukti pembayaran telah diverifikasi oleh admin'
                ]);
            } else {
                $payment->update(['status' => 'rejected']);
                $submission->update(['status' => 'payment_pending']);

                SubmissionHistory::create([
                    'submission_id' => $submission->id,
                    'actor_id' => Auth::id(),
                    'actor_type' => 'admin',
                    'action' => 'payment_rejected',
                    'title' => 'Pembayaran Ditolak',
                    'description' => $request->notes ?? 'Bukti pembayaran ditolak oleh admin'
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Payment updated successfully'
            ]);
        } catch (\Exception $e) {
            Log::error('Error verifying payment: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Error updating payment'
            ], 500);
        }
    }

    public function uploadDocument(Request $request, $id)
    {
        try {
            $request->validate([
                'documentname' => 'required|string|max:255',
                'document' => 'required|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:10240'
            ]);

            $submission = Submission::findOrFail($id);

            if ($request->hasFile('document')) {
                $file = $request->file('document');
                $originalName = $request->documentname . '.' . $file->getClientOriginalExtension();
                $path = $file->store('generated_documents/' . $submission->id, 'public');

                GeneratedDocument::create([
                    'submission_id' => $submission->id,
                    'document_path' => $path,
                    'document_name' => $originalName,
                    'document_type' => 'generated_document',
                    'file_size' => $file->getSize(),
                    'mime_type' => $file->getClientMimeType(),
                    'uploaded_by' => Auth::id()
                ]);

                // Update submission status to completed
                $submission->update(['status' => 'completed']);

                // Create history
                SubmissionHistory::create([
                    'submission_id' => $submission->id,
                    'actor_id' => Auth::id(),
                    'actor_type' => 'admin',
                    'action' => 'document_uploaded',
                    'title' => 'Dokumen Diunggah',
                    'description' => 'Admin mengunggah dokumen: ' . $originalName
                ]);

                return response()->json([
                    'success' => true,
                    'message' => 'Dokumen berhasil diunggah'
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'Gagal mengunggah dokumen'
            ], 500);
        } catch (\Exception $e) {
            Log::error('Error uploading document: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Gagal mengunggah dokumen: ' . $e->getMessage()
            ], 500);
        }
    }
}
