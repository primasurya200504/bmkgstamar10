<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AdminController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// Redirect dashboard based on role
Route::get('/dashboard', function () {
    if (auth()->check()) {
        return auth()->user()->isAdmin()
            ? redirect()->route('admin.dashboard')
            : redirect()->route('user.dashboard');
    }
    return redirect()->route('login');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// User Routes
Route::middleware(['auth', 'verified'])->prefix('user')->name('user.')->group(function () {
    Route::get('/dashboard', [UserController::class, 'dashboard'])->name('dashboard');
    Route::post('/submissions', [UserController::class, 'store'])->name('submissions.store');
    Route::get('/submissions/{submission}/edit', [UserController::class, 'edit'])->name('submissions.edit');
    Route::put('/submissions/{submission}', [UserController::class, 'update'])->name('submissions.update');
});

// Admin Routes
Route::middleware(['auth', 'verified'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
    Route::put('/submissions/{submission}/status', [AdminController::class, 'updateStatus'])->name('submissions.status');
    Route::post('/guidelines', [AdminController::class, 'storeGuideline'])->name('guidelines.store');
    Route::put('/guidelines/{guideline}', [AdminController::class, 'updateGuideline'])->name('guidelines.update');
    Route::delete('/guidelines/{guideline}', [AdminController::class, 'destroyGuideline'])->name('guidelines.destroy');
    Route::get('/files/{file}/download', [AdminController::class, 'downloadFile'])->name('files.download');
});

require __DIR__ . '/auth.php';
