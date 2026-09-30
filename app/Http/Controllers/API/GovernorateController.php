<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Services\GovernorateService;

class GovernorateController extends Controller
{
    protected GovernorateService $governorateService;

    public function __construct(GovernorateService $governorateService)
    {
        $this->governorateService = $governorateService;
    }

    public function index()
    {
        return $this->governorateService->index();
    }
}
