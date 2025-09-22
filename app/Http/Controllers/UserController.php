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
        $type = $request->input('type', 'all');

        $guidelines = Guideline::where('is_active', true);

        if ($type !== 'all') {
            $guidelines = $guidelines->where('type', $type);
        }

        $guidelines = $guidelines->get();

        return response()->json($guidelines);
    }

    public function submitApplication(Request $request)
    {
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

        Application::create([
            'user_id' => Auth::id(),
            'guideline_id' => $request->guideline_id,
            'type' => $request->type,
            'documents' => $documents,
            'status' => 'pending'
        ]);

        return response()->json(['success' => true, 'message' => 'Application submitted successfully']);
    }

    // Upload bukti pembayaran
    public function uploadPaymentProof(Request $request, $id)
    {
        $request->validate([
            'payment_proof' => 'required|file|mimes:pdf,jpg,jpeg,png|max:5120'
        ]);

        $application = Application::where('user_id', Auth::id())->findOrFail($id);
        $payment = $application->payment;

        if (!$payment) {
            return response()->json(['error' => 'No payment record found'], 404);
        }

        if ($request->hasFile('payment_proof')) {
            $file = $request->file('payment_proof');
            $filename = time() . '_payment_' . $file->getClientOriginalName();
            $path = $file->storeAs('payment_proofs', $filename, 'public');

            $payment->update(['payment_proof' => $path]);
        }

        return response()->json(['success' => true, 'message' => 'Payment proof uploaded successfully']);
    }

    // Download dokumen yang dihasilkan
    public function downloadDocument($id)
    {
        $document = \App\Models\GeneratedDocument::whereHas('application', function ($query) {
            $query->where('user_id', Auth::id());
        })->findOrFail($id);

        return Storage::disk('public')->download($document->document_path, $document->document_name);
    }

    // Fitur Profil
    public function profile()
    {
        return response()->json(Auth::user()->only(['name', 'email', 'phone']));
    }

    public function updateProfile(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . Auth::id(),
            'phone' => 'nullable|string|max:15'
        ]);

        Auth::user()->update($request->only(['name', 'email', 'phone']));

        return response()->json(['success' => true, 'message' => 'Profile updated successfully']);
    }

    public function changePassword(Request $request)
    {
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
    }
}
