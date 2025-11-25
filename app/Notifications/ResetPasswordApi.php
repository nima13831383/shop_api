<?php

namespace App\Notifications;

use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Carbon;

class ResetPasswordApi extends Notification
{
    public function __construct(public $token) {}

    public function via($notifiable)
    {
        return ['mail'];
    }

    // لینک ریست رمز عبور Signed
    protected function resetUrl($notifiable)
    {
        return URL::temporarySignedRoute(
            'password.reset.signed',  // اسم روت → پایین تعریف می‌کنیم
            Carbon::now()->addMinutes(30),
            [
                'email' => $notifiable->email,
                'token' => $this->token
            ]
        );
    }

    public function toMail($notifiable)
    {
        $url = $this->resetUrl($notifiable);

        //     return (new MailMessage)
        //         ->subject('Reset Password')
        //         ->line('Click the button below to reset your password.')
        //         ->action('Reset Password', $url)
        //         ->line('This link is valid for 30 minutes.');
        // }

        return (new MailMessage)
            ->subject('Reset Password')
            ->view('reset-token', [
                'token' => $this->token,
            ]);
    }
}
