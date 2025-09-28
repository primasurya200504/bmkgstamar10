<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

Route::get('/', function () {
    return redirect()->route('login');
});

// Authentication Routes
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register']);

Route::middleware('auth')->group(function () {
    // Admin routes
    Route::middleware('role:admin')->prefix('admin')->name('admin.')->group(function () {
        Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
        
        // AJAX Routes untuk Admin Dashboard
        Route::get('/ajax/submissions', [AdminController::class, 'getSubmissions'])->name('ajax.submissions');
        Route::get('/ajax/payments', [AdminController::class, 'getPayments'])->name('ajax.payments');
        Route::get('/ajax/documents', [AdminController::class, 'getDocuments'])->name('ajax.documents');
        Route::get('/ajax/guidelines', [AdminController::class, 'getGuidelines'])->name('ajax.guidelines');
        Route::get('/ajax/archives', [AdminController::class, 'getArchives'])->name('ajax.archives');
        Route::get('/ajax/users', [AdminController::class, 'getUsers'])->name('ajax.users');
        
        // Detail Routes
        Route::get('/submissions/{id}/detail', [AdminController::class, 'getSubmissionDetail'])->name('submission.detail');
        Route::get('/payments/{id}/detail', [AdminController::class, 'getPaymentDetail'])->name('payment.detail');
        
        // Action Routes
        Route::post('/submissions/{id}/verify', [AdminController::class, 'verifySubmission'])->name('submission.verify');
        Route::post('/payments/{id}/verify', [AdminController::class, 'verifyPayment'])->name('payment.verify');
        Route::post('/submissions/{id}/upload', [AdminController::class, 'uploadDocument'])->name('document.upload');
    });

    // User routes
    Route::middleware('role:user')->prefix('user')->name('user.')->group(function () {
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

require __DIR__.'/auth.php';
