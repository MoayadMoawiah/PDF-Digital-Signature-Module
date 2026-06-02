<?php

use App\Http\Controllers\Signing\SigningController;
use App\Http\Middleware\ValidateSigningToken;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

// Static terminal pages — must be declared BEFORE the token wildcard route
Route::get('/sign/expired',  fn() => Inertia::render('Signing/Expired'))->name('sign.expired');
Route::get('/sign/complete', fn() => Inertia::render('Signing/Complete'))->name('sign.complete');
Route::get('/sign/rejected', fn() => Inertia::render('Signing/Rejected'))->name('sign.rejected');

// Token-protected routes
Route::middleware([ValidateSigningToken::class, 'throttle:10,1'])->group(function () {
    Route::get('/sign/{token}',         [SigningController::class, 'show'])->name('sign.show');
    Route::get('/sign/{token}/pdf',     [SigningController::class, 'pdf'])->name('sign.pdf');
    Route::post('/sign/{token}/submit', [SigningController::class, 'submit'])->name('sign.submit');
    Route::post('/sign/{token}/reject', [SigningController::class, 'reject'])->name('sign.reject');
});
