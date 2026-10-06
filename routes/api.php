<?php

use App\Http\Controllers\API\AUTH\AuthController;
use App\Http\Controllers\API\AUTH\ClientAuthController;
use App\Http\Controllers\API\AUTH\ForgetPasswordController;
use App\Http\Controllers\API\ClientController;
use App\Http\Controllers\API\CourtController;
use App\Http\Controllers\API\DegreeController;
use App\Http\Controllers\API\FinancialReceiptController;
use App\Http\Controllers\API\GovernorateController;
use App\Http\Controllers\API\HearingController;
use App\Http\Controllers\API\HearingTypeController;
use App\Http\Controllers\API\LegalCaseController;
use App\Http\Controllers\API\ChatbotController;
use App\Http\Controllers\API\ConsultationController;
use App\Http\Controllers\API\ServiceRequestController;
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
    Route::get('hearing-types', [HearingTypeController::class, 'index']);

    // Protected Routes (auth:sanctum)
    Route::middleware('auth:sanctum')->group(function () {
        Route::get('profile', [AuthController::class, 'profile']);
        Route::get('profile/{id}', [AuthController::class, 'getProfileById']);
        Route::post('profile/update', [AuthController::class, 'updateProfile']);
    });

    // Protected Routes requiring acceptance (auth:sanctum + accepted)
    Route::middleware(['auth:sanctum', 'accepted'])->group(function () {
        Route::post('clients/{client}', [ClientController::class, 'update']);
        Route::post('governorates/{governorate}', [GovernorateController::class, 'update']);
        Route::post('courts/{court}', [CourtController::class, 'update']);
        Route::post('hearing-types/{hearing_type}', [HearingTypeController::class, 'update']);
        Route::post('hearings/{hearing}', [HearingController::class, 'update']);
        Route::post('financial-receipts/{financial_receipt}', [FinancialReceiptController::class, 'update']);
        Route::get('service-requests/my-requests', [ServiceRequestController::class, 'myRequests']);
        Route::get('service-requests/my-assigned', [ServiceRequestController::class, 'myAssigned']);
        Route::get('service-requests/my-offers', [ServiceRequestController::class, 'myOffers']);
        Route::post('service-requests/{service_request}', [ServiceRequestController::class, 'update']);
        Route::post('service-requests/{service_request}/offers', [ServiceRequestController::class, 'submitOffer']);
        Route::post('service-requests/offers/{offer}/accept', [ServiceRequestController::class, 'acceptOffer']);
        Route::post('service-requests/offers/{offer}/reject', [ServiceRequestController::class, 'rejectOffer']);
        Route::apiResource('service-requests', ServiceRequestController::class);
        Route::apiResource('clients', ClientController::class);
        Route::apiResource('legal-cases', LegalCaseController::class);
        Route::apiResource('hearings', HearingController::class);
        Route::apiResource('financial-receipts', FinancialReceiptController::class);
        Route::apiResource('governorates', GovernorateController::class)->except(['index']);
        Route::apiResource('courts', CourtController::class)->except(['index']);
        Route::apiResource('hearing-types', HearingTypeController::class)->except(['index']);

        // Client Consultation Routes
        Route::post('consultations/{consultation}/reply', [ConsultationController::class, 'reply']);
        Route::post('consultations/{consultation}', [ConsultationController::class, 'update']);
        Route::apiResource('consultations', ConsultationController::class);

        // Groq AI Legal Assistant Routes
        Route::post('chatbot/chat', [ChatbotController::class, 'chat']);
        Route::get('chatbot/suggestions', [ChatbotController::class, 'suggestions']);
        Route::get('chatbot/history', [ChatbotController::class, 'history']);
        Route::delete('chatbot/history', [ChatbotController::class, 'clearHistory']);
    });
});

