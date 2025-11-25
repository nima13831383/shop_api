<?php

namespace App\Services\Api\Auth;


use App\Repositories\UserRepository;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;

class AuthService
{
    protected UserRepository $users;


    public function __construct(UserRepository $users)
    {
        $this->users = $users;
    }


    public function register(array $data)
    {
        $user = $this->users->findByEmail($data['email']);
        if ($user) {

            $key = 'resend-verification:' . $user->id;
            if (RateLimiter::tooManyAttempts($key, 4)) {
                $msg = 'Too many requests. Try again later.';
                $status = ['res' => false, 'code' => 429];
            }
            RateLimiter::hit($key, 60); // یک دقیقه اعتبار

            if (!is_null($user->email_verified_at)) {
                $msg = 'Email exists';
                $status = ['res' => false, 'code' => 403];
            } else {
                $msg = 'new verification email sent.';
                $status = ['res' => true, 'code' => 200];
            }
        } else {
            // ایجاد کاربر جدید
            $user = $this->users->create($data);
            $msg = 'User registered. Check your email for verification link.';
            $status = ['res' => true, 'code' => 200];
        }


        // ارسال ایمیل تایید
        $user->sendEmailVerificationNotification();


        return ['user' => $user, 'msg' => $msg, 'status' => $status];
    }


    public function login(array $data)
    {
        $user = $this->users->findByEmail($data['email']);


        // اگر کاربر وجود نداشت
        if (!$user || !Hash::check($data['password'], $user->password)) {
            $msg = 'Invalid credentials';
            $status = ['res' => false, 'code' => 401];
            return ['user' => null, 'msg' => $msg, 'status' => $status, 'token' => null];
        }

        // اگر ایمیل تایید نشده بود
        elseif (is_null($user->email_verified_at)) {
            $msg = 'Email not verified';
            $status = ['res' => false, 'code' => 403];
            return ['user' => null, 'msg' => $msg, 'status' => $status, 'token' => null];
        } else {
            $msg = 'login successful';
            $status = ['res' => true, 'code' => 200];
            // تولید توکن جدید
            $token = Str::random(60);
            $user->api_token = $token;
            $user->save();
            return ['user' => $user, 'msg' => $msg, 'status' => $status, 'token' => $token];
        }
    }
}
