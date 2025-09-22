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
        Route::get('/requests', [AdminController::class, 'requests'])->name('requests');
        Route::post('/requests/{id}/verify', [AdminController::class, 'verifyRequest'])->name('requests.verify');
        Route::get('/payments', [AdminController::class, 'payments'])->name('payments');
        Route::post('/payments/{id}/verify', [AdminController::class, 'verifyPayment'])->name('payments.verify');
        Route::post('/documents/{id}/upload', [AdminController::class, 'uploadDocument'])->name('documents.upload');
        Route::post('/applications/{id}/complete', [AdminController::class, 'completeApplication'])->name('applications.complete');
        Route::get('/guidelines', [AdminController::class, 'guidelines'])->name('guidelines');
        Route::post('/guidelines', [AdminController::class, 'storeGuideline'])->name('guidelines.store');
        Route::get('/guidelines/{id}', [AdminController::class, 'showGuideline'])->name('guidelines.show');
        Route::put('/guidelines/{id}', [AdminController::class, 'updateGuideline'])->name('guidelines.update');
        Route::delete('/guidelines/{id}', [AdminController::class, 'destroyGuideline'])->name('guidelines.destroy');
        Route::get('/archives', [AdminController::class, 'archives'])->name('archives');
        Route::get('/users', [AdminController::class, 'users'])->name('users');
    });

    // User routes
    Route::middleware('user')->prefix('user')->name('user.')->group(function () {
        Route::get('/dashboard', [UserController::class, 'dashboard'])->name('dashboard');
        Route::get('/guidelines', [UserController::class, 'guidelines'])->name('guidelines');
        Route::post('/applications', [UserController::class, 'submitApplication'])->name('applications.store');
        Route::post('/applications/{id}/payment', [UserController::class, 'uploadPaymentProof'])->name('applications.payment');
        Route::get('/documents/{id}/download', [UserController::class, 'downloadDocument'])->name('documents.download');
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
