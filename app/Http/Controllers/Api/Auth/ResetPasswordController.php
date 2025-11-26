<?php

namespace App\Http\Controllers\Api\Auth;


use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Services\Api\Auth\AuthService;
use App\Services\Api\Auth\ResetPasswordService;
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
    public function forgotPassword(Request $request, ResetPasswordService $service)
    {
        $request->validate(['email' => 'required|email']);

        $res = $service->requestResetLink($request->email, $request->ip());

        return response()->json([
            'status' => $res['status'],
            'message' => $res['message'],
        ], $res['code']);
    }


    public function signedResetPassword(Request $request)
    {
        return response()->json([
            'status' => true,
            'message' => 'Signed link is valid.',
            'email'   => $request->email,
            'token'   => $request->token
        ]);
    }


    public function resetPassword(Request $request, ResetPasswordService $service)
    {
        $request->validate([
            'email'    => 'required|email',
            'token'    => 'required',
            'password' => 'required|min:6|confirmed',
        ]);

        $result = $service->resetPassword($request->only('email', 'password', 'password_confirmation', 'token'));

        return response()->json([
            'status' => $result['status'],
            'message' => $result['message'],
        ], $result['code']);
    }
}
