<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Admin\PaymentController as AdminPaymentController;
use App\Http\Controllers\PaymentController;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::middleware('auth')->group(function () {
    // ADMIN
    Route::middleware('admin')->prefix('admin')->name('admin.')->group(function () {
        Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
        Route::get('/submissions', [AdminController::class, 'submissions'])->name('submissions');
        Route::get('/users', [AdminController::class, 'users'])->name('users');
        Route::post('/submissions/{id}/approve', [AdminController::class, 'approveSubmission'])->name('submission.approve');
        Route::post('/submissions/{id}/reject', [AdminController::class, 'rejectSubmission'])->name('submission.reject');

        // ✅ konsisten gunakan payments.index / create / store / verify
        Route::get('/payments', [AdminPaymentController::class, 'index'])->name('payments.index');
        Route::get('/payments/create', [AdminPaymentController::class, 'create'])->name('payments.create');
        Route::post('/payments', [AdminPaymentController::class, 'store'])->name('payments.store');
        Route::post('/payments/{id}/verify', [AdminPaymentController::class, 'verify'])->name('payments.verify');
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

    // ✅ route user untuk pembayaran
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
