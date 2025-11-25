<?php

namespace App\Services\Api\Auth;

use App\Models\User;
use Illuminate\Auth\Events\Verified;

class EmailVerificationService
{
    public function verifyEmail(int $id, string $hash)
    {
        $user = User::find($id);

        if (!$user) {
            return ['status' => false, 'message' => 'User not found', 'code' => 404];
        }

        if (! hash_equals(sha1($user->email), $hash)) {
            return ['status' => false, 'message' => 'Invalid verification link', 'code' => 403];
        }

        if ($user->hasVerifiedEmail()) {
            return ['status' => true, 'message' => 'Email already verified', 'code' => 200];
        }

        $user->markEmailAsVerified();
        event(new Verified($user));

        return ['status' => true, 'message' => 'Email verified successfully', 'code' => 200];
    }
}
