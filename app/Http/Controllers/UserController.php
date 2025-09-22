<?php

namespace App\Http\Controllers;

use App\Models\Application;
use App\Models\Guideline;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class UserController extends Controller
{
    public function dashboard()
    {
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

        return view('user.dashboard', compact('applications', 'stats'));
    }

    // Fitur Pengajuan
    public function guidelines(Request $request)
    {
        try {
            $type = $request->input('type', 'all');

            $guidelines = Guideline::where('is_active', true);

            if ($type !== 'all') {
                $guidelines = $guidelines->where('type', $type);
            }

            $guidelines = $guidelines->get();

            return response()->json($guidelines);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to load guidelines'], 500);
        }
    }

    public function submitApplication(Request $request)
    {
        try {
            $request->validate([
                'guideline_id' => 'required|exists:guidelines,id',
                'type' => 'required|in:pnbp,non_pnbp',
                'documents.*' => 'required|file|mimes:pdf,jpg,jpeg,png,doc,docx|max:5120'
            ]);

            $guideline = Guideline::findOrFail($request->guideline_id);

            $documents = [];
            if ($request->hasFile('documents')) {
                foreach ($request->file('documents') as $key => $file) {
                    $filename = time() . '_' . $key . '_' . $file->getClientOriginalName();
                    $path = $file->storeAs('user_documents', $filename, 'public');
                    $documents[$key] = $path;
                }
            }

            // PERBAIKAN: Generate application_number
            $lastApplication = Application::latest()->first();
            $lastNumber = $lastApplication ? intval(substr($lastApplication->application_number, -4)) : 0;
            $newNumber = str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
            $applicationNumber = 'BMKG' . date('md') . '/' . date('Y') . '/' . $newNumber;

            Application::create([
                'user_id' => Auth::id(),
                'guideline_id' => $request->guideline_id,
                'application_number' => $applicationNumber,
                'type' => $request->type,
                'documents' => $documents,
                'status' => 'pending'
            ]);

            return response()->json(['success' => true, 'message' => 'Application submitted successfully']);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to submit application: ' . $e->getMessage()], 500);
        }
    }

    // Upload bukti pembayaran
    public function uploadPaymentProof(Request $request, $id)
    {
        try {
            $request->validate([
                'payment_proof' => 'required|file|mimes:pdf,jpg,jpeg,png|max:5120'
            ]);

            $application = Application::where('user_id', Auth::id())->findOrFail($id);

            // PERBAIKAN: Cari atau buat payment record
            $payment = Payment::where('application_id', $application->id)->first();

            if (!$payment) {
                $payment = Payment::create([
                    'application_id' => $application->id,
                    'amount' => $application->guideline->fee,
                    'status' => 'pending'
                ]);
            }

            if ($request->hasFile('payment_proof')) {
                $file = $request->file('payment_proof');
                $filename = time() . '_payment_' . $file->getClientOriginalName();
                $path = $file->storeAs('payment_proofs', $filename, 'public');

                $payment->update([
                    'payment_proof' => $path,
                    'status' => 'pending'
                ]);

                // Update application status
                $application->update(['status' => 'payment_pending']);
            }

            return response()->json(['success' => true, 'message' => 'Payment proof uploaded successfully']);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to upload payment proof: ' . $e->getMessage()], 500);
        }
    }

    // Download dokumen yang dihasilkan
    public function downloadDocument($id)
    {
        try {
            $document = \App\Models\GeneratedDocument::whereHas('application', function ($query) {
                $query->where('user_id', Auth::id());
            })->findOrFail($id);

            $path = storage_path('app/public/' . $document->document_path);

            if (!file_exists($path)) {
                abort(404, 'File not found');
            }

            return response()->download($path, $document->document_name);
        } catch (\Exception $e) {
            abort(404, 'Document not found');
        }
    }

    // TAMBAHAN: Method history
    public function history()
    {
        try {
            $applications = Auth::user()->applications()
                ->with(['guideline', 'payment', 'generatedDocuments'])
                ->orderBy('created_at', 'desc')
                ->get();

            return response()->json(['data' => $applications]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to load history'], 500);
        }
    }

    // Fitur Profil
    public function profile()
    {
        try {
            return response()->json(Auth::user()->only(['name', 'email', 'phone']));
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to load profile'], 500);
        }
    }

    public function updateProfile(Request $request)
    {
        try {
            $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:users,email,' . Auth::id(),
                'phone' => 'nullable|string|max:15'
            ]);

            Auth::user()->update($request->only(['name', 'email', 'phone']));

            return response()->json(['success' => true, 'message' => 'Profile updated successfully']);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to update profile'], 500);
        }
    }

    public function changePassword(Request $request)
    {
        try {
            $request->validate([
                'current_password' => 'required',
                'new_password' => 'required|min:8|confirmed'
            ]);

            if (!Hash::check($request->current_password, Auth::user()->password)) {
                return response()->json(['error' => 'Current password is incorrect'], 422);
            }

            Auth::user()->update([
                'password' => Hash::make($request->new_password)
            ]);

            return response()->json(['success' => true, 'message' => 'Password changed successfully']);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to change password'], 500);
        }
    }
}
