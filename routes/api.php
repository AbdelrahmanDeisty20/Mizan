<?php

use App\Http\Controllers\API\AUTH\AuthController;
use App\Http\Controllers\API\AUTH\ClientAuthController;
use App\Http\Controllers\API\AUTH\ForgetPasswordController;
use App\Http\Controllers\API\ClientController;
use App\Http\Controllers\API\CourtController;
use App\Http\Controllers\API\DegreeController;
use App\Http\Controllers\API\GovernorateController;
use App\Http\Controllers\API\LegalCaseController;
use App\Http\Middleware\SetLang;
use Illuminate\Support\Facades\Route;

Route::middleware([SetLang::class])->group(function () {
    // Auth Routes
    Route::controller(AuthController::class)->group(function () {
        Route::post('register', 'register')->middleware('throttle:5,1');
        Route::post('login', 'login')->middleware('throttle:10,1');
        Route::post('verify-otp', 'verifyOtp')->middleware('throttle:10,1');
        Route::post('resend-otp', 'resendOtp')->middleware('throttle:3,1');
    });

    // Forget Password Routes
    Route::controller(ForgetPasswordController::class)->prefix('forget-password')->group(function () {
        Route::post('send-otp', 'forgetPassword')->middleware('throttle:3,1');
        Route::post('verify-otp', 'verifyOtp')->middleware('throttle:10,1');
        Route::post('resend-otp', 'resendOtp')->middleware('throttle:3,1');
        Route::post('reset', 'resetPassword')->middleware('throttle:5,1');
    });

    // Client Auth Routes
    Route::controller(ClientAuthController::class)->prefix('client-auth')->group(function () {
        Route::post('login', 'login')->middleware('throttle:10,1');
    });

    // Lookups Routes
    Route::get('degrees', [DegreeController::class, 'index']);
    Route::get('governorates', [GovernorateController::class, 'index']);
    Route::get('courts', [CourtController::class, 'index']);

    // Protected Routes
    Route::middleware('auth:sanctum')->group(function () {
        Route::get('profile', [AuthController::class, 'profile']);
        Route::put('profile', [AuthController::class, 'updateProfile']);
        Route::apiResource('clients', ClientController::class);
        Route::apiResource('legal-cases', LegalCaseController::class);
    });
});

