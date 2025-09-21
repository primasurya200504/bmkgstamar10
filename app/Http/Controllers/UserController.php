<?php

namespace App\Http\Controllers;

use App\Models\Submission;
use App\Models\Guideline;
use App\Models\SubmissionFile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class UserController extends Controller
{
    public function dashboard()
    {
        $user = auth()->user();
        $submissions = Submission::where('user_id', $user->id)
            ->with('files')
            ->latest()
            ->get();

        $guidelines = Guideline::active()->get();

        $stats = [
            'total' => $submissions->count(),
            'pending' => $submissions->where('status', 'Menunggu')->count(),
            'approved' => $submissions->whereIn('status', ['Diterima', 'Selesai'])->count(),
            'rejected' => $submissions->where('status', 'Ditolak')->count(),
        ];

        return view('user.dashboard', compact('submissions', 'guidelines', 'stats'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'data_type' => 'required|string|max:255',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'purpose' => 'required|string',
            'category' => 'required|in:PNBP,Non-PNBP',
            'files.*' => 'required|file|mimes:pdf,doc,docx|max:5120', // 5MB max
        ]);

        $user = auth()->user();

        // Generate submission number
        $submissionNumber = 'SUB-' . date('Y') . '-' . str_pad(
            Submission::whereYear('created_at', date('Y'))->count() + 1,
            4,
            '0',
            STR_PAD_LEFT
        );

        // Create submission
        $submission = Submission::create([
            'user_id' => $user->id,
            'submission_number' => $submissionNumber,
            'data_type' => $request->data_type,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'purpose' => $request->purpose,
            'category' => $request->category,
            'status' => 'Menunggu',
        ]);

        // Handle file uploads
        if ($request->hasFile('files')) {
            foreach ($request->file('files') as $file) {
                if ($file->isValid()) {
                    $fileName = time() . '_' . Str::random(10) . '.' . $file->getClientOriginalExtension();
                    $filePath = $file->storeAs('submissions', $fileName, 'public');

                    SubmissionFile::create([
                        'submission_id' => $submission->id,
                        'file_name' => $file->getClientOriginalName(),
                        'file_path' => $filePath,
                        'file_type' => $file->getClientMimeType(),
                        'file_size' => $file->getSize(),
                    ]);
                }
            }
        }

        return redirect()->route('user.dashboard')
            ->with('success', 'Pengajuan berhasil dikirim dengan nomor: ' . $submissionNumber);
    }

    public function edit(Submission $submission)
    {
        $this->authorize('update', $submission);
        $guidelines = Guideline::active()->get();

        return response()->json([
            'submission' => $submission,
            'guidelines' => $guidelines
        ]);
    }

    public function update(Request $request, Submission $submission)
    {
        $this->authorize('update', $submission);

        $request->validate([
            'data_type' => 'required|string|max:255',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'purpose' => 'required|string',
            'files.*' => 'file|mimes:pdf,doc,docx|max:5120',
        ]);

        $submission->update([
            'data_type' => $request->data_type,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'purpose' => $request->purpose,
            'status' => 'Menunggu', // Reset status
        ]);

        // Handle new files if uploaded
        if ($request->hasFile('files')) {
            foreach ($request->file('files') as $file) {
                if ($file->isValid()) {
                    $fileName = time() . '_' . Str::random(10) . '.' . $file->getClientOriginalExtension();
                    $filePath = $file->storeAs('submissions', $fileName, 'public');

                    SubmissionFile::create([
                        'submission_id' => $submission->id,
                        'file_name' => $file->getClientOriginalName(),
                        'file_path' => $filePath,
                        'file_type' => $file->getClientMimeType(),
                        'file_size' => $file->getSize(),
                    ]);
                }
            }
        }

        return redirect()->route('user.dashboard')
            ->with('success', 'Pengajuan berhasil diperbarui!');
    }
}
