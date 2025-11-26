<?php

use App\Http\Controllers\Api\Auth\VerifyEmailController;
use App\Http\Controllers\Api\Auth\RegisterController;
use App\Http\Controllers\Api\Auth\LoginController;
use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use App\Http\Controllers\Api\Auth\ProfileController;
use App\Http\Controllers\Api\Auth\ResetPasswordController;


Route::post('/register', [RegisterController::class, 'register']);
Route::post('/login',    [LoginController::class, 'login']);




Route::middleware('auth.api')->group(function () {
    Route::get('/profile', [ProfileController::class, 'profile']);
});


Route::get('/email/verify/{id}/{hash}', [VerifyEmailController::class, 'verify'])
    ->name('verification.verify')
    ->middleware('signed'); // فقط امضا را چک می‌کند


Route::post('/password/forgot', [ResetPasswordController::class, 'forgotPassword']);
Route::get('/password/reset-signed', [ResetPasswordController::class, 'signedResetPassword'])
    ->name('password.reset.signed')
    ->middleware('signed');
Route::post('/password/reset', [ResetPasswordController::class, 'resetPassword'])
    ->name('password.reset');
