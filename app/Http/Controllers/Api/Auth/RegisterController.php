<?php

namespace App\Http\Controllers\Api\Auth;


use App\Http\Controllers\Controller;
use App\Http\Requests\RegisterRequest;
use App\Services\Api\Auth\AuthService;
use Illuminate\Support\Facades\Auth;


class RegisterController extends Controller
{
    protected AuthService $authService;


    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }


    public function register(RegisterRequest $request)
    {
        $response = $this->authService->register($request->validated());

        $user = $response['user'];
        $msg = $response['msg'];

        return response()->json([
            'status' => $response['status']['res'],
            'code' => $response['status']['code'],
            'message' => $msg,
            'user' => $user
        ]);
    }
}
