<?php

namespace App\Http\Controllers\Api\Auth;


use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Services\Api\Auth\AuthService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;


class ProfileController extends Controller
{
    protected AuthService $authService;


    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
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
