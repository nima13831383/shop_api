<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Models\User;
use Illuminate\Validation\Rules\Exists;
use Illuminate\Support\Facades\Password;


class AuthController extends Controller
{
    // Register
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|',
            'password' => 'required|string|min:6',
        ]);
        if ($user = User::where('email', $request->email)->first()) {
            $key = 'resend-verification:' . $user->id;
            if (RateLimiter::tooManyAttempts($key, 4)) {
                return response()->json([
                    'status' => false,
                    'message' => 'Too many requests. Try again later.'
                ], 429);
            }

            RateLimiter::hit($key, 60); // یک دقیقه اعتبار
            if (!is_null($user->email_verified_at)) {
                return response()->json([
                    'status'  => false,
                    'message' => 'Email exists',
                ], 403);
            } else {
                $registered = 0;
            }
        } else {

            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'api_token' => Str::random(60),
            ]);
        }

        // ارسال ایمیل verify
        $user->sendEmailVerificationNotification();

        if (isset($registered)) {
            if ($registered == 0) {
                $msg = 'new verification email sent.';
            } else {
                $msg = 'User registered. Check your email for verification link.';
            }
        } else {
            $msg = 'User registered. Check your email for verification link.';
        }
        return response()->json([
            'status' => true,
            'message' => $msg,
            'user' => $user
        ]);
    }

    // Login
    public function login(Request $request)
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required',
        ]);

        $user = User::where('email', $request->email)->first();

        // اگر کاربر وجود نداشت
        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'status'  => false,
                'message' => 'Invalid credentials',
            ], 401);
        }

        // اگر ایمیل تایید نشده بود
        if (is_null($user->email_verified_at)) {
            return response()->json([
                'status'  => false,
                'message' => 'Email not verified',
            ], 403);
        }

        // تولید توکن جدید
        $token = Str::random(60);
        $user->api_token = $token;
        $user->save();

        return response()->json([
            'status'  => true,
            'message' => 'Login successful',
            'token'   => $token,
            'user'    => $user,
        ]);
    }


    // Profile
    public function profile(Request $request)
    {
        return response()->json([
            'status' => true,
            'data' => $request->user()
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

    public function signedResetPassword(Request $request)
    {
        // اگر signed نبود middleware خودش 403 می‌دهد

        return response()->json([
            'status' => true,
            'message' => 'Signed link is valid.',
            'email'   => $request->email,
            'token'   => $request->token
        ]);
    }
    public function resetPassword(Request $request)
    {
        $request->validate([
            'email'    => 'required|email',
            'token'    => 'required',
            'password' => 'required|min:6|',
        ]);
        if ($request->password !== $request->password_confirmation) {
            return response()->json([
                'status'  => false,
                'message' => 'password and its confirmation doesnt match', // دلیل خطا
            ], 400);
        }


        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user->forceFill([
                    'password' => Hash::make($password)
                ])->save();
            }
        );

        if ($status === Password::PASSWORD_RESET) {
            return response()->json([
                'status'  => true,
                'message' => 'Password has been reset.'
            ]);
        }

        return response()->json([
            'status'  => false,
            'message' => __($status), // دلیل خطا
        ], 400);
    }
}
