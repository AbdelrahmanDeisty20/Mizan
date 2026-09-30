<?php

namespace App\Http\Controllers\API\AUTH;

use App\Http\Controllers\Controller;
use App\Http\Requests\API\LoginRequest;
use App\Http\Requests\API\RegisterRequest;
use App\Http\Requests\API\ResendOtpRequest;
use App\Http\Requests\API\UpdateProfileRequest;
use App\Http\Requests\API\VerifyOtpRequest;
use App\Services\AuthService;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    protected AuthService $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    public function register(RegisterRequest $request)
    {
        return $this->authService->register($request->validated());
    }

    public function login(LoginRequest $request)
    {
        return $this->authService->login($request->validated());
    }

    public function verifyOtp(VerifyOtpRequest $request)
    {
        return $this->authService->verifyOtp($request->validated());
    }

    public function resendOtp(ResendOtpRequest $request)
    {
        return $this->authService->resendOtp($request->validated());
    }

    public function profile()
    {
        return $this->authService->profile();
    }

    public function updateProfile(UpdateProfileRequest $request)
    {
        return $this->authService->updateProfile($request->validated());
    }
}

