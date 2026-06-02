<?php

use App\Http\Controllers\Admin\AuditLogController;
use App\Http\Controllers\Admin\DocumentController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

// Root redirect
Route::get('/', fn() => redirect()->route('admin.documents.index'));

// Admin panel — authenticated
Route::middleware(['auth', 'verified'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('dashboard', fn() => Inertia::render('Dashboard'))->name('dashboard');

    Route::resource('documents', DocumentController::class)
        ->only(['index', 'create', 'store', 'show', 'destroy']);

    Route::get('documents/{document}/download', [DocumentController::class, 'download'])
        ->name('documents.download');

    Route::get('documents/{document}/audit', [AuditLogController::class, 'show'])
        ->name('documents.audit');
});

// Profile routes (from Breeze)
Route::middleware('auth')->group(function () {
    Route::get('/profile',    [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile',  [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__ . '/auth.php';
require __DIR__ . '/signing.php';
