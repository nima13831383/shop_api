<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Models\User;
use Illuminate\Validation\Rules\Exists;

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
}
