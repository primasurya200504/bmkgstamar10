<?php

namespace App\Http\Controllers;

use App\Models\Application;
use App\Models\Guideline;
use App\Models\Payment;
use App\Models\Archive;
use App\Models\User;
use App\Models\GeneratedDocument;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;
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

        // PERBAIKAN: Ubah nama view dari admin_dashboard ke dashboard
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

            $application->update([
                'status' => $request->action === 'approve' ? 'verified' : 'rejected',
                'notes' => $request->notes
            ]);

            // PERBAIKAN: Cek apakah fee > 0 untuk membuat payment
            if ($request->action === 'approve' && $application->guideline->fee > 0) {
                $application->update(['status' => 'payment_pending']);

                // Cek apakah payment sudah ada
                $existingPayment = Payment::where('application_id', $application->id)->first();

                if (!$existingPayment) {
                    Payment::create([
                        'application_id' => $application->id,
                        'amount' => $application->guideline->fee,
                        'status' => 'pending'
                    ]);
                }
            } elseif ($request->action === 'approve' && $application->guideline->fee == 0) {
                // Jika fee 0, langsung ke status paid
                $application->update(['status' => 'paid']);
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
            } else {
                $payment->application->update(['status' => 'payment_pending']);
            }

            return response()->json(['success' => true, 'message' => 'Payment updated successfully']);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to verify payment'], 500);
        }
    }

    // TAMBAHAN: Method documents untuk mendukung dashboard
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

    // Manajemen Upload Dokumen
    public function uploadDocument(Request $request, $id)
    {
        try {
            $request->validate([
                'document' => 'required|file|mimes:pdf,doc,docx|max:10240',
                'document_name' => 'nullable|string|max:255'
            ]);

            $application = Application::findOrFail($id);

            if ($request->hasFile('document')) {
                $file = $request->file('document');
                $filename = time() . '_' . $file->getClientOriginalName();
                $path = $file->storeAs('generated_documents', $filename, 'public');

                GeneratedDocument::create([
                    'application_id' => $application->id,
                    'document_path' => $path,
                    'document_name' => $request->document_name ?? $file->getClientOriginalName()
                ]);

                $application->update(['status' => 'completed']);

                // Auto archive
                Archive::create([
                    'application_id' => $application->id,
                    'archive_date' => now(),
                    'notes' => 'Automatically archived upon completion'
                ]);
            }

            return response()->json(['success' => true, 'message' => 'Document uploaded successfully']);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to upload document'], 500);
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
                'notes' => 'Automatically archived upon completion'
            ]);

            return response()->json(['success' => true, 'message' => 'Application completed']);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to complete application'], 500);
        }
    }

    // Manajemen Panduan
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

    // TAMBAHAN: Method showGuideline
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

    // TAMBAHAN: Method destroyGuideline
    public function destroyGuideline($id)
    {
        try {
            $guideline = Guideline::findOrFail($id);

            // Check if guideline is being used
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

    // Manajemen Arsip
    public function archives(Request $request)
    {
        try {
            $query = Archive::with(['application.user', 'application.guideline']);

            if ($request->month) {
                $query->whereMonth('archive_date', $request->month);
            }

            if ($request->year) {
                $query->whereYear('archive_date', $request->year);
            }

            $archives = $query->orderBy('archive_date', 'desc')->paginate(20);

            return response()->json($archives);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to load archives'], 500);
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

    // TAMBAHAN: CRUD User Methods
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

            // Check if user has applications
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
