<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Services\CourtService;

class CourtController extends Controller
{
    protected CourtService $courtService;

    public function __construct(CourtService $courtService)
    {
        $this->courtService = $courtService;
    }

    public function index()
    {
        return $this->courtService->index();
    }
}
