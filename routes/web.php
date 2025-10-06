<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\GuidelineController; // Asumsi controller baru untuk panduan
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\PaymentController;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::middleware('auth')->group(function () {
    // Admin routes (integrasi dengan user, tapi protected admin middleware)
    Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
        Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard'); // 1 route utama

        // Fitur sidebar
        Route::get('/submissions', [AdminController::class, 'submissions'])->name('submissions');
        Route::get('/submissions/{submission}', [AdminController::class, 'showSubmission'])->name('submissions.show');
        Route::post('/submissions/{submission}/approve', [AdminController::class, 'approveSubmission'])->name('submissions.approve');
        Route::post('/submissions/{submission}/reject', [AdminController::class, 'rejectSubmission'])->name('submissions.reject');
        Route::post('/submissions/{submissionId}/upload-file', [AdminController::class, 'uploadFileData'])->name('upload.file');
        Route::get('/ebilling', [AdminController::class, 'ebilling'])->name('ebilling');
        Route::post('/payments/{id}/verify', [AdminController::class, 'verifyPayment'])->name('verify.payment');
        Route::get('/archives', [AdminController::class, 'archives'])->name('archives');
        Route::get('/users', [AdminController::class, 'users'])->name('users');

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
    });

    // USER
    Route::middleware('user')->prefix('user')->name('user.')->group(function () {
        Route::get('/dashboard', [UserController::class, 'dashboard'])->name('dashboard');
        Route::get('/submissions', [UserController::class, 'getSubmissions'])->name('submissions');
        Route::get('/submissions/{id}', [UserController::class, 'getSubmission'])->name('submission.detail');
        Route::post('/submissions', [UserController::class, 'storeSubmission'])->name('submission.store');
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
