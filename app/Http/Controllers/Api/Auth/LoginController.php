<?php

namespace App\Http\Controllers\Api\Auth;


use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Services\Api\Auth\AuthService;
use Illuminate\Support\Facades\Auth;


class LoginController extends Controller
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
}
