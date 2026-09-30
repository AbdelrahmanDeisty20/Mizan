<?php

namespace App\Http\Controllers\API\AUTH;

use App\Http\Controllers\Controller;
use App\Http\Requests\API\ClientLoginRequest;
use App\Services\ClientAuthService;

class ClientAuthController extends Controller
{
    protected ClientAuthService $clientAuthService;

    public function __construct(ClientAuthService $clientAuthService)
    {
        $this->clientAuthService = $clientAuthService;
    }

    public function login(ClientLoginRequest $request)
    {
        return $this->clientAuthService->login($request->validated());
    }
}
