<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware('auth')->group(function () {
    // Admin routes
    Route::middleware('admin')->prefix('admin')->name('admin.')->group(function () {
        Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
        
        // Manajemen Permintaan
        Route::get('/requests', [AdminController::class, 'requests'])->name('requests');
        Route::post('/requests/{id}/verify', [AdminController::class, 'verifyRequest'])->name('requests.verify');
        
        // Manajemen Pembayaran
        Route::get('/payments', [AdminController::class, 'payments'])->name('payments');
        Route::post('/payments/{id}/verify', [AdminController::class, 'verifyPayment'])->name('payments.verify');
        
        // Upload Dokumen
        Route::get('/documents', [AdminController::class, 'documents'])->name('documents');
        Route::post('/documents/{id}/upload', [AdminController::class, 'uploadDocument'])->name('documents.upload');
        
        // Manajemen Aplikasi
        Route::post('/applications/{id}/complete', [AdminController::class, 'completeApplication'])->name('applications.complete');
        
        // Manajemen Panduan (CRUD lengkap)
        Route::get('/guidelines', [AdminController::class, 'guidelines'])->name('guidelines');
        Route::post('/guidelines', [AdminController::class, 'storeGuideline'])->name('guidelines.store');
        Route::get('/guidelines/{id}', [AdminController::class, 'showGuideline'])->name('guidelines.show');
        Route::put('/guidelines/{id}', [AdminController::class, 'updateGuideline'])->name('guidelines.update');
        Route::delete('/guidelines/{id}', [AdminController::class, 'destroyGuideline'])->name('guidelines.destroy');
        
        // Manajemen Arsip
        Route::get('/archives', [AdminController::class, 'archives'])->name('archives');
        
        // Manajemen Pengguna
        Route::get('/users', [AdminController::class, 'users'])->name('users');
        Route::post('/users', [AdminController::class, 'createUser'])->name('users.store');
        Route::put('/users/{id}', [AdminController::class, 'updateUser'])->name('users.update');
        Route::delete('/users/{id}', [AdminController::class, 'deleteUser'])->name('users.destroy');
    });

    // User routes
    Route::middleware('user')->prefix('user')->name('user.')->group(function () {
        Route::get('/dashboard', [UserController::class, 'dashboard'])->name('dashboard');
        
        // Guidelines dan Pengajuan
        Route::get('/guidelines', [UserController::class, 'guidelines'])->name('guidelines');
        Route::post('/applications', [UserController::class, 'submitApplication'])->name('applications.store');
        
        // Pembayaran
        Route::post('/applications/{id}/payment', [UserController::class, 'uploadPaymentProof'])->name('applications.payment');
        
        // Download Dokumen
        Route::get('/documents/{id}/download', [UserController::class, 'downloadDocument'])->name('documents.download');
        
        // Riwayat
        Route::get('/history', [UserController::class, 'history'])->name('history');
        
        // Profil
        Route::get('/profile', [UserController::class, 'profile'])->name('profile');
        Route::put('/profile', [UserController::class, 'updateProfile'])->name('profile.update');
        Route::put('/change-password', [UserController::class, 'changePassword'])->name('password.change');
    });

    // Main dashboard redirect based on role
    Route::get('/dashboard', function () {
        if (Auth::user()->isAdmin()) {
            return redirect()->route('admin.dashboard');
        }
        return redirect()->route('user.dashboard');
    })->name('dashboard');

    // Profile routes (jika menggunakan ProfileController dari Breeze/Jetstream)
    Route::get('/profile-settings', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile-settings', [ProfileController::class, 'update'])->name('profile.update.breeze');
    Route::delete('/profile-settings', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__ . '/auth.php';
