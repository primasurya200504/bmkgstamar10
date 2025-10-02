<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Admin API Routes
Route::middleware(['auth:sanctum', 'admin'])->prefix('admin')->name('api.admin.')->group(function () {
    // Submissions
    Route::get('/submissions', [AdminController::class, 'getSubmissions'])->name('submissions');
    Route::get('/submissions/{id}/detail', [AdminController::class, 'getSubmissionDetail'])->name('submission.detail');
    Route::post('/submissions/reupload-file', [AdminController::class, 'reuploadFile'])->name('submission.reupload-file');

    // Payments
    Route::get('/payments', [AdminController::class, 'getPayments'])->name('payments');
    Route::get('/payments/{id}/detail', [AdminController::class, 'getPaymentDetail'])->name('payment.detail');
    Route::post('/payments/update-status', [AdminController::class, 'updatePaymentStatus'])->name('payment.update-status');
    Route::post('/payments/upload-e-billing', [AdminController::class, 'uploadEBilling'])->name('payment.upload-e-billing');

    // Documents
    Route::get('/documents', [AdminController::class, 'getDocuments'])->name('documents');

    // Guidelines
    Route::get('/guidelines', [AdminController::class, 'getGuidelines'])->name('guidelines');

    // Archives
    Route::get('/archives', [AdminController::class, 'getArchives'])->name('archives');

    // Users
    Route::get('/users', [AdminController::class, 'getUsers'])->name('users');

    // Uploads
    Route::get('/uploads', [AdminController::class, 'getUploads'])->name('uploads');
    Route::post('/uploads', [AdminController::class, 'storeUpload'])->name('uploads.store');
    Route::get('/uploads/{id}', [AdminController::class, 'getUploadDetail'])->name('upload.detail');
    Route::delete('/uploads/{id}', [AdminController::class, 'deleteUpload'])->name('upload.delete');
});
