<?php

namespace App\Http\Controllers;

use App\Models\Submission;
use App\Models\Payment;
use App\Models\GeneratedDocument;
use App\Models\Archive;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

class DataUploadController extends Controller
{
    public function index()
    {
        $submissions = Submission::with(['user', 'payment'])
            ->where('status', 'verified')
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
            'document_path' => $filePath,
            'file_name' => $fileName,
            'file_size' => $file->getSize(),
            'mime_type' => $file->getMimeType(),
            'uploaded_by' => Auth::id(),
        ]);

        return redirect()->back()->with('success', 'Dokumen berhasil diupload.');
    }

    public function downloadDocument($id)
    {
        $document = GeneratedDocument::findOrFail($id);

        if (!Storage::disk('public')->exists($document->document_path)) {
            return redirect()->back()->with('error', 'File tidak ditemukan.');
        }

        // Use the actual filename from storage path to ensure correct extension
        $downloadName = $document->file_name ?: basename($document->document_path);

        $filePath = storage_path('app/public/' . $document->document_path);

        return response()->download($filePath, $downloadName, [
            'Content-Type' => $document->mime_type ?: mime_content_type($filePath),
            'Content-Disposition' => 'attachment; filename="' . $downloadName . '"'
        ]);
    }

    public function deleteDocument($id)
    {
        $document = GeneratedDocument::findOrFail($id);

        // Delete file from storage
        if (Storage::disk('public')->exists($document->document_path)) {
            Storage::disk('public')->delete($document->document_path);
        }

        $document->delete();

        return redirect()->back()->with('success', 'Dokumen berhasil dihapus.');
    }

    public function completeUpload($submissionId)
    {
        $submission = Submission::findOrFail($submissionId);

        $submission->update([
            'status' => 'completed',
            'admin_notes' => 'Data telah diupload dan diproses'
        ]);

        // Create archive record
        Archive::create([
            'submission_id' => $submission->id,
            'user_id' => $submission->user_id,
            'notes' => 'Pengajuan selesai diproses dan diarsipkan'
        ]);

        // Log history
        $submission->logHistory('completed', 'admin', Auth::id(), 'Data Upload Selesai', 'Data telah diupload dan diproses');

        return redirect()->route('admin.data-uploads.index')->with('success', 'Upload data selesai dan pengajuan dipindahkan ke arsip.');
    }

    public function viewUploadedFile($submissionId, $fileId)
    {
        $submission = Submission::findOrFail($submissionId);
        $file = $submission->files()->where('id', $fileId)->firstOrFail();

        $filePath = storage_path('app/public/' . $file->file_path);

        if (!file_exists($filePath)) {
            abort(404, 'File tidak ditemukan.');
        }

        return response()->file($filePath, [
            'Content-Type' => $file->file_type,
            'Content-Disposition' => 'inline; filename="' . $file->file_name . '"'
        ]);
    }
}
