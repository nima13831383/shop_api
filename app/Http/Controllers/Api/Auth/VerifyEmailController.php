<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Services\Api\Auth\EmailVerificationService;

class VerifyEmailController extends Controller
{
    protected EmailVerificationService $service;

    public function __construct(EmailVerificationService $service)
    {
        $this->service = $service;
    }

    public function verify($id, $hash)
    {
        $result = $this->service->verifyEmail($id, $hash);

        return response()->json(['message' => $result['message']], $result['code']);
    }
}
