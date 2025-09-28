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
                    return [
                        'id' => $submission->id,
                        'submission_number' => $submission->submission_number ?? 'SUB-' . str_pad($submission->id, 4, '0', STR_PAD_LEFT),
                        'user_name' => $submission->user->name ?? 'N/A',
                        'guideline_title' => $submission->guideline->title ?? 'N/A',
                        'status' => $submission->status,
                        'status_label' => $this->getStatusLabel($submission->status),
                        'created_at_formatted' => $submission->created_at->format('d/m/Y H:i'),
                        'type_label' => ($submission->guideline->type ?? 'non_pnbp') === 'pnbp' ? 'PNBP (Berbayar)' : 'Non-PNBP (Gratis)'
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

    // Manajemen Guidelines - FIXED
    public function guidelines()
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

    // Get Submissions Data - FIXED
    public function getSubmissionsData()
    {
        try {
            $submissions = Submission::with(['user', 'guideline'])
                ->orderBy('created_at', 'desc')
                ->get()
                ->map(function ($submission) {
                    return [
                        'id' => $submission->id,
                        'submission_number' => $submission->submission_number ?? 'SUB-' . str_pad($submission->id, 4, '0', STR_PAD_LEFT),
                        'user' => [
                            'id' => $submission->user->id ?? null,
                            'name' => $submission->user->name ?? 'N/A',
                            'email' => $submission->user->email ?? 'N/A'
                        ],
                        'guideline' => [
                            'id' => $submission->guideline->id ?? null,
                            'title' => $submission->guideline->title ?? 'N/A',
                            'type' => $submission->guideline->type ?? 'non_pnbp',
                            'fee' => $submission->guideline->fee ?? 0
                        ],
                        'status' => $submission->status,
                        'status_label' => $this->getStatusLabel($submission->status),
                        'type_label' => ($submission->guideline->type ?? 'non_pnbp') === 'pnbp' ? 'PNBP' : 'Non-PNBP',
                        'created_at' => $submission->created_at->format('d/m/Y H:i'),
                        'created_at_formatted' => $submission->created_at->format('d/m/Y H:i')
                    ];
                });

            return response()->json([
                'success' => true,
                'data' => $submissions
            ]);
        } catch (\Exception $e) {
            Log::error('Get Submissions Data Error: ' . $e->getMessage());
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

    // Helper method untuk status label
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
}
