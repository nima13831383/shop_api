<?php
//php artisan make:model ProductCategory --all --api
use App\Http\Controllers\Api\Auth\VerifyEmailController;
use App\Http\Controllers\Api\Auth\RegisterController;
use App\Http\Controllers\Api\Auth\LoginController;
use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use App\Http\Controllers\Api\Auth\ProfileController;
use App\Http\Controllers\Api\Auth\ResetPasswordController;



use App\Http\Controllers\Api\Shop\ProductController;
use App\Http\Controllers\Api\Shop\ProductCategoryController;
use App\Http\Controllers\Api\Shop\ProductImageController;
use App\Http\Controllers\Api\Shop\ProductReviewController;




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






Route::apiResource('products', ProductController::class)
    ->only(['index', 'show']);

Route::apiResource('products', ProductController::class)
    ->only(['store', 'update', 'destroy'])
    ->middleware('check.admin');

Route::apiResource('product-categories', ProductCategoryController::class)
    ->only(['index', 'show']);

Route::apiResource('product-categories', ProductCategoryController::class)
    ->only(['store', 'update', 'destroy'])
    ->middleware('check.admin');

Route::prefix('products/{product}')->group(function () {

    Route::post('/images', [ProductImageController::class, 'store'])->middleware('check.admin'); // آپلود
});

Route::delete('/product-images/{image}', [ProductImageController::class, 'destroy'])->middleware('check.admin');

Route::put('/product-images/{image}/main', [ProductImageController::class, 'setMain'])->middleware('check.admin');
Route::apiResource('product-reviews', ProductReviewController::class)
    ->only(['index', 'show']);

Route::apiResource('product-reviews', ProductReviewController::class)
    ->only(['store', 'update', 'destroy'])
    ->middleware('check.admin');

Route::prefix('product-reviews/{product_review}')->group(function () {
    Route::put('approve', [ProductReviewController::class, 'approve']);
    Route::put('reject', [ProductReviewController::class, 'reject']);
})->middleware('check.admin');
