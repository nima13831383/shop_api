<?php

namespace App\Http\Controllers\Api\Auth;


use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Services\Api\Auth\AuthService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Password;
use Illuminate\Http\Request;

class ResetPasswordController extends Controller
{
    protected AuthService $authService;


    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }


    public function login(LoginRequest $request)
    {
        $response = $this->authService->login($request->validated());
        $user = $response['user'];
        $msg = $response['msg'];
        return response()->json([
            'status' => $response['status']['res'],
            'code' => $response['status']['code'],
            'message' => $msg,
            'user' => $user,
            'token' => $response['token']
        ]);
    }
    public function forgotPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email'
        ]);

        $key = 'password-reset:' . $request->ip();

        // اگر بیش از حد درخواست داده
        if (RateLimiter::tooManyAttempts($key, 4)) { // نهایت 4 تلاش
            return response()->json([
                'status' => false,
                'message' => 'Too many attempts. Try again later.'
            ], 429);
        }

        RateLimiter::hit($key, 60 * 15);
        // ۱۵ دقیقه بعد صفر می‌شود

        $user = User::where('email', $request->email)->first();

        // اگر کاربر وجود نداشت
        if (!$user) {
            return response()->json([
                'status' => true,
                'message' => 'If this email exists, a reset link has been sent.'
            ]);
        }

        // ساخت توکن برای کاربر موجود
        $token = Password::createToken($user);

        // ارسال ایمیل کاستوم
        $user->notify(new \App\Notifications\ResetPasswordApi($token));
        return response()->json([
            'status' => true,
            'message' => 'Reset link sent to your email.'
        ]);
    }
}
