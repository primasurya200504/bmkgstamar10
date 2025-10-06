<?php

namespace App\Http\Controllers;

use App\Models\Submission;
use App\Models\Payment;
use App\Models\GeneratedDocument;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

class DataUploadController extends Controller
{
    public function index()
    {
        $submissions = Submission::with(['user', 'payment'])
            ->where('status', 'Diterima')
            ->whereHas('payment', function($query) {
                $query->where('status', 'verified');
            })
            ->paginate(10);

        return view('admin.data-uploads.index', compact('submissions'));
    }

    public function show($id)
    {
        $submission = Submission::with(['user', 'payment', 'generatedDocuments', 'files'])->findOrFail($id);

        return view('admin.data-uploads.show', compact('submission'));
    }

    public function uploadDocument(Request $request, $submissionId)
    {
        $request->validate([
            'document_name' => 'required|string|max:255',
            'document_file' => 'required|file|mimes:pdf,doc,docx,xls,xlsx,jpg,jpeg,png|max:10240',
            'document_type' => 'required|string|max:255',
        ]);

        $submission = Submission::findOrFail($submissionId);

        $file = $request->file('document_file');
        $fileName = time() . '_' . $file->getClientOriginalName();
        $filePath = $file->storeAs('generated_documents', $fileName, 'public');

        GeneratedDocument::create([
            'submission_id' => $submission->id,
            'document_name' => $request->document_name,
            'document_type' => $request->document_type,
            'file_path' => $filePath,
            'file_name' => $fileName,
            'file_size' => $file->getSize(),
            'uploaded_by' => Auth::id(),
        ]);

        return redirect()->back()->with('success', 'Dokumen berhasil diupload.');
    }

    public function downloadDocument($id)
    {
        $document = GeneratedDocument::findOrFail($id);

        $filePath = storage_path('app/public/' . $document->file_path);

        if (!file_exists($filePath)) {
            return redirect()->back()->with('error', 'File tidak ditemukan.');
        }

        return response()->download($filePath, $document->file_name);
    }

    public function deleteDocument($id)
    {
        $document = GeneratedDocument::findOrFail($id);

        // Delete file from storage
        if (Storage::disk('public')->exists($document->file_path)) {
            Storage::disk('public')->delete($document->file_path);
        }

        $document->delete();

        return redirect()->back()->with('success', 'Dokumen berhasil dihapus.');
    }

    public function completeUpload($submissionId)
    {
        $submission = Submission::findOrFail($submissionId);

        $submission->update([
            'status' => 'Selesai',
            'admin_notes' => 'Data telah diupload dan diproses'
        ]);

        // Log history
        $submission->logHistory('Data upload selesai', 'admin');

        return redirect()->route('admin.data-uploads.index')->with('success', 'Upload data selesai dan pengajuan dipindahkan ke arsip.');
    }
}
