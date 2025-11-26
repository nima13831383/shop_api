<?php

namespace App\Services\Api\Auth;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\RateLimiter;

class ResetPasswordService
{
    public function requestResetLink($email, $ip)
    {
        $key = 'password-reset:' . $ip;

        if (RateLimiter::tooManyAttempts($key, 4)) {
            return [
                'status' => false,
                'message' => 'Too many attempts. Try again later.',
                'code' => 429
            ];
        }

        RateLimiter::hit($key, 60 * 15);

        $user = User::where('email', $email)->first();

        if (!$user) {
            return [
                'status' => true,
                'message' => 'If this email exists, a reset link has been sent.',
                'code' => 200
            ];
        }

        $token = Password::createToken($user);
        $user->notify(new \App\Notifications\ResetPasswordApi($token));

        return [
            'status' => true,
            'message' => 'Reset link sent to your email.',
            'code' => 200
        ];
    }


    public function resetPassword($data)
    {
        if ($data['password'] !== $data['password_confirmation']) {
            return [
                'status' => false,
                'message' => 'password and its confirmation doesnt match',
                'code' => 400
            ];
        }

        $status = Password::reset(
            $data,
            function ($user, $password) {
                $user->forceFill([
                    'password' => Hash::make($password)
                ])->save();
            }
        );

        if ($status === Password::PASSWORD_RESET) {
            return [
                'status' => true,
                'message' => 'Password has been reset.',
                'code' => 200
            ];
        }

        return [
            'status' => false,
            'message' => __($status),
            'code' => 400
        ];
    }
}
