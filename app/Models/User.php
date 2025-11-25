<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Contracts\Auth\CanResetPassword;
use Illuminate\Auth\Passwords\CanResetPassword as CanResetPasswordTrait;
class User extends Authenticatable implements MustVerifyEmail,CanResetPassword
{
    use HasFactory, Notifiable,CanResetPasswordTrait;

    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'address',
        'api_token',
        'email_verified_at',
    ];

    protected $hidden = [
        'password',
        'api_token',
    ];

    // برای API Notification سفارشی
    public function sendEmailVerificationNotification()
    {
        $this->notify(new \App\Notifications\VerifyEmailApi);
    }
}
