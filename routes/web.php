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
    // ADMIN
    Route::middleware('admin')->prefix('admin')->name('admin.')->group(function () {
        // Satu route utama untuk dashboard dengan semua section
        Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');

        // Routes untuk actions spesifik (POST/GET tambahan)
        Route::post('/submissions/{id}/verify', [AdminController::class, 'verifySubmission'])->name('submission.verify');
        Route::post('/submissions/{id}/reject', [AdminController::class, 'rejectSubmission'])->name('submission.reject');
        Route::post('/payments/{id}/upload-ebilling', [AdminController::class, 'uploadEbilling'])->name('payment.uploadEbilling');
        Route::post('/payments/{id}/verify', [AdminController::class, 'verifyPayment'])->name('payment.verify');
        Route::post('/uploads/{id}', [AdminController::class, 'uploadFileToUser'])->name('upload.file');
        Route::get('/archives/download', [AdminController::class, 'downloadArchive'])->name('archive.download');

        // Resource routes untuk manajemen panduan dan pengguna
        Route::resource('guidelines', GuidelineController::class);
        Route::post('/users/store', [AdminController::class, 'storeUser'])->name('users.store'); // Custom untuk add user
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
