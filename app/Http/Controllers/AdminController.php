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
        $applications = Application::with(['user', 'guideline'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return response()->json($applications);
    }

    public function verifyRequest(Request $request, $id)
    {
        $application = Application::findOrFail($id);
        
        $application->update([
            'status' => $request->action === 'approve' ? 'verified' : 'rejected',
            'notes' => $request->notes
        ]);

        if ($request->action === 'approve' && $application->guideline->fee > 0) {
            $application->update(['status' => 'payment_pending']);
            
            Payment::create([
                'application_id' => $application->id,
                'amount' => $application->guideline->fee,
                'status' => 'pending'
            ]);
        }

        return response()->json(['success' => true, 'message' => 'Request updated successfully']);
    }

    // Manajemen Pembayaran
    public function payments()
    {
        $payments = Payment::with(['application.user', 'application.guideline'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return response()->json($payments);
    }

    public function verifyPayment(Request $request, $id)
    {
        $payment = Payment::findOrFail($id);
        
        $payment->update([
            'status' => $request->action === 'approve' ? 'verified' : 'rejected',
            'paid_at' => $request->action === 'approve' ? now() : null
        ]);

        if ($request->action === 'approve') {
            $payment->application->update(['status' => 'paid']);
        }

        return response()->json(['success' => true, 'message' => 'Payment updated successfully']);
    }

    // Manajemen Upload Dokumen
    public function uploadDocument(Request $request, $id)
    {
        $request->validate([
            'document' => 'required|file|mimes:pdf,doc,docx|max:10240'
        ]);

        $application = Application::findOrFail($id);
        
        if ($request->hasFile('document')) {
            $file = $request->file('document');
            $filename = time() . '_' . $file->getClientOriginalName();
            $path = $file->storeAs('generated_documents', $filename, 'public');
            
            GeneratedDocument::create([
                'application_id' => $application->id,
                'document_path' => $path,
                'document_name' => $file->getClientOriginalName()
            ]);

            $application->update(['status' => 'processing']);
        }

        return response()->json(['success' => true, 'message' => 'Document uploaded successfully']);
    }

    public function completeApplication($id)
    {
        $application = Application::findOrFail($id);
        $application->update(['status' => 'completed']);

        // Auto archive
        Archive::create([
            'application_id' => $application->id,
            'archive_date' => now(),
            'notes' => 'Automatically archived upon completion'
        ]);

        return response()->json(['success' => true, 'message' => 'Application completed']);
    }

    // Manajemen Panduan
    public function guidelines()
    {
        $guidelines = Guideline::orderBy('created_at', 'desc')->get();
        return response()->json($guidelines);
    }

    public function storeGuideline(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'type' => 'required|in:pnbp,non_pnbp',
            'required_documents' => 'required|array',
            'fee' => 'required|numeric|min:0'
        ]);

        Guideline::create($request->all());

        return response()->json(['success' => true, 'message' => 'Guideline created successfully']);
    }

    public function updateGuideline(Request $request, $id)
    {
        $guideline = Guideline::findOrFail($id);
        $guideline->update($request->all());

        return response()->json(['success' => true, 'message' => 'Guideline updated successfully']);
    }

    // Manajemen Arsip
    public function archives(Request $request)
    {
        $query = Archive::with(['application.user', 'application.guideline']);

        if ($request->month) {
            $query->whereMonth('archive_date', $request->month);
        }

        if ($request->year) {
            $query->whereYear('archive_date', $request->year);
        }

        $archives = $query->orderBy('archive_date', 'desc')->paginate(20);

        return response()->json($archives);
    }

    // Manajemen Pengguna
    public function users()
    {
        $users = User::where('role', 'user')
            ->select('id', 'name', 'email', 'phone', 'created_at')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return response()->json($users);
    }
}
