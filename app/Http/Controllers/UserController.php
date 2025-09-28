<?php

namespace App\Http\Controllers;

use App\Models\Submission;
use App\Models\Guideline;
use App\Models\Payment;
use App\Models\GeneratedDocument;
use App\Models\SubmissionHistory;
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
            $guidelines = Guideline::where('is_active', true)->get();

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

            // Fallback data
            $stats = ['pending' => 0, 'in_process' => 0, 'completed' => 0, 'rejected' => 0, 'total' => 0];
            $recentActivities = collect();
            $guidelines = Guideline::where('is_active', true)->get();
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
     * Get user submissions via AJAX
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

            $submissions = $query->orderBy('created_at', 'desc')->get();

            return response()->json([
                'success' => true,
                'data' => $submissions
            ]);
        } catch (\Exception $e) {
            Log::error('Error loading user submissions: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Error loading submissions',
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

            return response()->json([
                'success' => true,
                'data' => $submission
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Submission not found'
            ], 404);
        }
    }

    /**
     * Submit Pengajuan Surat/Data
     */
    public function storeSubmission(Request $request)
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

            return response()->json([
                'success' => true,
                'message' => 'Pengajuan berhasil dikirim',
                'data' => $submission->load('guideline')
            ]);
        } catch (\Exception $e) {
            Log::error('Error creating submission: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Gagal mengirim pengajuan',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Upload Payment Proof
     */
    public function uploadPayment(Request $request, $id)
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
            $submission->update(['status' => 'payment_pending']);

            // Create history
            SubmissionHistory::create([
                'submission_id' => $submission->id,
                'actor_id' => $user->id,
                'actor_type' => 'user',
                'action' => 'payment_uploaded',
                'title' => 'Bukti Pembayaran Diupload',
                'description' => 'User telah mengupload bukti pembayaran'
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Bukti pembayaran berhasil diupload'
            ]);
        } catch (\Exception $e) {
            Log::error('Error uploading payment: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Gagal upload bukti pembayaran',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update Profile
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
                $updateData['password'] = bcrypt($request->password);
            }

            $user->update($updateData);

            return response()->json([
                'success' => true,
                'message' => 'Profil berhasil diperbarui'
            ]);
        } catch (\Exception $e) {
            Log::error('Error updating profile: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Gagal memperbarui profil',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
