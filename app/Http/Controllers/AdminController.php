<?php

namespace App\Http\Controllers;

use App\Models\Submission;
use App\Models\Guideline;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AdminController extends Controller
{
    public function dashboard()
    {
        $submissions = Submission::with(['user', 'files'])->latest()->get();
        $guidelines = Guideline::latest()->get();

        $stats = [
            'total_submissions' => Submission::count(),
            'pending_submissions' => Submission::where('status', 'Menunggu')->count(),
            'total_users' => User::where('role', 'user')->count(),
            'total_guidelines' => Guideline::where('is_active', true)->count(),
        ];

        return view('admin.dashboard', compact('submissions', 'guidelines', 'stats'));
    }

    public function updateStatus(Request $request, Submission $submission)
    {
        $request->validate([
            'status' => 'required|in:Menunggu,Diproses,Diterima,Ditolak,Selesai',
            'admin_notes' => 'nullable|string',
            'rejection_note' => 'nullable|string|required_if:status,Ditolak',
            'cover_letter' => 'nullable|file|mimes:pdf|max:5120',
        ]);

        $data = [
            'status' => $request->status,
            'admin_notes' => $request->admin_notes,
        ];

        if ($request->status === 'Ditolak') {
            $data['rejection_note'] = $request->rejection_note;
        }

        // Handle cover letter upload for approved submissions
        if ($request->hasFile('cover_letter') && $request->status === 'Selesai') {
            $file = $request->file('cover_letter');
            $fileName = 'cover_letter_' . $submission->submission_number . '.' . $file->getClientOriginalExtension();
            $filePath = $file->storeAs('cover_letters', $fileName, 'public');
            $data['cover_letter_path'] = $filePath;
        }

        $submission->update($data);

        return redirect()->route('admin.dashboard')
            ->with('success', 'Status pengajuan berhasil diperbarui!');
    }

    public function storeGuideline(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'requirements' => 'nullable|array',
            'example_data' => 'nullable|array',
        ]);

        Guideline::create([
            'title' => $request->title,
            'content' => $request->content,
            'requirements' => $request->requirements,
            'example_data' => $request->example_data,
            'is_active' => true,
        ]);

        return redirect()->route('admin.dashboard')
            ->with('success', 'Panduan berhasil ditambahkan!');
    }

    public function updateGuideline(Request $request, Guideline $guideline)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'requirements' => 'nullable|array',
            'example_data' => 'nullable|array',
        ]);

        $guideline->update([
            'title' => $request->title,
            'content' => $request->content,
            'requirements' => $request->requirements,
            'example_data' => $request->example_data,
        ]);

        return redirect()->route('admin.dashboard')
            ->with('success', 'Panduan berhasil diperbarui!');
    }

    public function destroyGuideline(Guideline $guideline)
    {
        $guideline->delete();

        return redirect()->route('admin.dashboard')
            ->with('success', 'Panduan berhasil dihapus!');
    }

    public function downloadFile($fileId)
    {
        $file = \App\Models\SubmissionFile::findOrFail($fileId);

        if (!Storage::disk('public')->exists($file->file_path)) {
            abort(404, 'File tidak ditemukan');
        }

        return Storage::disk('public')->download($file->file_path, $file->file_name);
    }
}
