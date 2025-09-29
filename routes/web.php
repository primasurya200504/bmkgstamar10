<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::middleware('auth')->group(function () {
    // Admin routes
    Route::middleware('admin')->prefix('admin')->name('admin.')->group(function () {
        Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');

        // AJAX Routes untuk Admin Dashboard
        Route::get('/submissions', [AdminController::class, 'getSubmissions'])->name('submissions');
        Route::get('/payments', [AdminController::class, 'getPayments'])->name('payments');
        Route::get('/documents', [AdminController::class, 'getDocuments'])->name('documents');
        Route::get('/guidelines', [AdminController::class, 'getGuidelines'])->name('guidelines');
        Route::get('/archives', [AdminController::class, 'getArchives'])->name('archives');
        Route::get('/users', [AdminController::class, 'getUsers'])->name('users');

        // Detail Routes
        Route::get('/submissions/{id}/detail', [AdminController::class, 'getSubmissionDetail'])->name('submission.detail');
        Route::get('/payments/{id}/detail', [AdminController::class, 'getPaymentDetail'])->name('payment.detail');

        // Action Routes
        Route::post('/submissions/{id}/verify', [AdminController::class, 'verifySubmission'])->name('submission.verify');
        Route::post('/payments/{id}/verify', [AdminController::class, 'verifyPayment'])->name('payment.verify');
        Route::post('/submissions/{id}/upload', [AdminController::class, 'uploadDocument'])->name('document.upload');
    });

    // User routes
    Route::middleware('user')->prefix('user')->name('user.')->group(function () {
        Route::get('/dashboard', [UserController::class, 'dashboard'])->name('dashboard');
        Route::get('/submissions', [UserController::class, 'getSubmissions'])->name('submissions');
        Route::get('/submissions/{id}', [UserController::class, 'getSubmission'])->name('submission.detail');
        Route::post('/submissions', [UserController::class, 'storeSubmission'])->name('submission.store');
        Route::post('/submissions/{id}/payment', [UserController::class, 'uploadPayment'])->name('payment.upload');
        Route::post('/profile', [UserController::class, 'updateProfile'])->name('profile.update');
    });

    // Main dashboard redirect
    Route::get('/dashboard', function () {
        if (Auth::user()->role === 'admin') {
            return redirect()->route('admin.dashboard');
        }
        return redirect()->route('user.dashboard');
    })->name('dashboard');
});

require __DIR__ . '/auth.php';
