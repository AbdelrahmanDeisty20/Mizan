<?php

namespace App\Http\Controllers\API\AUTH;

use App\Http\Controllers\Controller;
use App\Http\Requests\API\ForgetPasswordRequest;
use App\Http\Requests\API\ResendOtpRequest;
use App\Http\Requests\API\ResetPasswordRequest;
use App\Http\Requests\API\VerifyForgetPasswordRequest;
use App\Services\ForgetPasswordService;

class ForgetPasswordController extends Controller
{
    protected ForgetPasswordService $forgetPasswordService;

    public function __construct(ForgetPasswordService $forgetPasswordService)
    {
        $this->forgetPasswordService = $forgetPasswordService;
    }

    public function forgetPassword(ForgetPasswordRequest $request)
    {
        return $this->forgetPasswordService->sendOtp($request->validated());
    }

    public function verifyOtp(VerifyForgetPasswordRequest $request)
    {
        return $this->forgetPasswordService->verifyOtp($request->validated());
    }

    public function resetPassword(ResetPasswordRequest $request)
    {
        return $this->forgetPasswordService->resetPassword($request->validated());
    }

    public function resendOtp(ResendOtpRequest $request)
    {
        return $this->forgetPasswordService->resendOtp($request->validated());
    }
}
