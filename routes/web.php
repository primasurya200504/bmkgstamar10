<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\GuidelineController; // Asumsi controller baru untuk panduan
use App\Http\Controllers\TeacherController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\DataUploadController;

Route::view('/', 'welcome');

Route::view('/beranda', 'beranda');
Route::view('/profil-kami', 'profil-kami');
Route::view('/faq', 'faq');
Route::view('/formulir-permohonan', 'formulir-permohonan');

Route::middleware('auth')->group(function () {
    // Admin routes (integrasi dengan user, tapi protected admin middleware)
    Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
        Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard'); // 1 route utama

        // admin routes untuk mengelola pengguna menambahkan atau menghapus pengguna
        Route::resource('guru', TeacherController::class)->names('guru');

        // Fitur sidebar
        Route::get('/submissions', [AdminController::class, 'submissions'])->name('submissions');
        Route::get('/submissions/{submission}', [AdminController::class, 'showSubmission'])->name('submissions.show');
        Route::post('/submissions/{submission}/verify', [AdminController::class, 'verifySubmission'])->name('submissions.verify');
        Route::post('/submissions/{submission}/approve', [AdminController::class, 'approveSubmission'])->name('submissions.approve');
        Route::post('/submissions/{submission}/reject', [AdminController::class, 'rejectSubmission'])->name('submissions.reject');
        Route::post('/submissions/{submissionId}/upload-file', [AdminController::class, 'uploadFileData'])->name('upload.file');
        Route::get('/submissions/{submissionId}/files/{fileId}/download', [AdminController::class, 'downloadUploadedFile'])->name('submissions.file.download');
        Route::get('/payments/{paymentId}/e-billing/download', [AdminController::class, 'downloadEBilling'])->name('payments.ebilling.download');
        Route::get('/ebilling', [AdminController::class, 'ebilling'])->name('ebilling');
        Route::get('/ebilling/upload', [AdminController::class, 'uploadEBillingPage'])->name('ebilling.upload');
        Route::post('/submissions/{submission}/upload-ebilling', [AdminController::class, 'uploadEBilling'])->name('upload.ebilling');
        Route::post('/payments/{id}/verify', [AdminController::class, 'verifyPayment'])->name('verify.payment');
        Route::post('/payments/{id}/reject', [AdminController::class, 'rejectPayment'])->name('reject.payment');
        Route::get('/payments/{id}/download-proof', [AdminController::class, 'downloadPaymentProof'])->name('download.payment.proof');
        Route::get('/archives', [AdminController::class, 'archives'])->name('archives');
        Route::get('/archives/export-pdf', [AdminController::class, 'exportArchivesPdf'])->name('archives.export-pdf');
        Route::get('/archives/{archive}', [AdminController::class, 'showArchive'])->name('archives.show');
        Route::get('/users', [AdminController::class, 'users'])->name('users');
        Route::get('/users/create', [AdminController::class, 'create'])->name('users.create');
        Route::post('/users', [AdminController::class, 'store'])->name('users.store');
        Route::get('/users/{user}/edit', [AdminController::class, 'edit'])->name('users.edit');
        Route::put('/users/{user}', [AdminController::class, 'update'])->name('users.update');
        Route::delete('/users/{user}', [AdminController::class, 'destroy'])->name('users.destroy');

        // Guidelines management
        Route::resource('guidelines', GuidelineController::class)->names([
            'index' => 'guidelines',
            'create' => 'guidelines.create',
            'store' => 'guidelines.store',
            'show' => 'guidelines.show',
            'edit' => 'guidelines.edit',
            'update' => 'guidelines.update',
            'destroy' => 'guidelines.destroy'
        ]);

        // Data Upload Management
        Route::get('/data-uploads', [DataUploadController::class, 'index'])->name('data-uploads.index');
        Route::get('/data-uploads/{id}', [DataUploadController::class, 'show'])->name('data-uploads.show');
        Route::post('/data-uploads/{submissionId}/upload-document', [DataUploadController::class, 'uploadDocument'])->name('data-uploads.upload');
        Route::get('/data-uploads/{id}/download', [DataUploadController::class, 'downloadDocument'])->name('data-uploads.download');
        Route::delete('/data-uploads/{id}/delete', [DataUploadController::class, 'deleteDocument'])->name('data-uploads.delete');
        Route::post('/data-uploads/{submissionId}/complete', [DataUploadController::class, 'completeUpload'])->name('data-uploads.complete');
        Route::get('/data-uploads/{submissionId}/files/{fileId}/view', [DataUploadController::class, 'viewUploadedFile'])->name('data-uploads.file.view');
    });

    // USER
    Route::middleware('user')->prefix('user')->name('user.')->group(function () {
        Route::get('/dashboard', [UserController::class, 'dashboard'])->name('dashboard');
        Route::get('/submissions', [UserController::class, 'getSubmissions'])->name('submissions');
        Route::get('/submissions/{id}', [UserController::class, 'getSubmission'])->name('submission.detail');
        Route::post('/submissions', [UserController::class, 'storeSubmission'])->name('submission.store');
        Route::post('/submissions/{id}/upload-files', [UserController::class, 'uploadFilesToSubmission'])->name('submission.upload.files');
        Route::post('/submissions/{id}/resubmit', [UserController::class, 'resubmitSubmission'])->name('submission.resubmit');
        Route::get('/submissions/{submissionId}/files/{fileId}/download', [UserController::class, 'downloadUploadedFile'])->name('submission.file.download');
        Route::get('/submissions/{submissionId}/documents/{documentId}/download', [UserController::class, 'downloadGeneratedDocument'])->name('submission.document.download');
        Route::post('/submissions/{id}/payment', [UserController::class, 'uploadPayment'])->name('payment.upload');
        Route::get('/profile/edit', [UserController::class, 'profile'])->name('profile.edit');
        Route::post('/profile', [UserController::class, 'updateProfile'])->name('profile.update');
    });

    // Route user untuk pembayaran
    Route::get('/payments', [PaymentController::class, 'index'])->name('payments.index');
    Route::post('/payments/{id}/upload-proof', [PaymentController::class, 'uploadProof'])->name('payments.uploadProof');

    // MAIN DASHBOARD REDIRECT
    Route::get('/dashboard', function () {
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login');
        }

        if ($user->role === 'admin') {
            return redirect()->route('admin.dashboard');
        }

        return redirect()->route('user.dashboard');
    })->name('dashboard');
});

require __DIR__ . '/auth.php';
