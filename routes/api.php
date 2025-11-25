<?php

use App\Http\Controllers\Api\VerifyEmailController;
use App\Http\Controllers\Api\AuthController;
use Illuminate\Support\Facades\Route;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login',    [AuthController::class, 'login']);

use Illuminate\Foundation\Auth\EmailVerificationRequest;



Route::middleware('auth.api')->group(function () {
    Route::get('/profile', [AuthController::class, 'profile']);
});


Route::get('/email/verify/{id}/{hash}', [VerifyEmailController::class, 'verify'])
    ->name('verification.verify')
    ->middleware('signed'); // فقط امضا را چک می‌کند


Route::post('/password/forgot', [AuthController::class, 'forgotPassword']);
Route::get('/password/reset-signed', [AuthController::class, 'signedResetPassword'])
    ->name('password.reset.signed')
    ->middleware('signed');
Route::post('/password/reset', [AuthController::class, 'resetPassword'])
    ->name('password.reset');
