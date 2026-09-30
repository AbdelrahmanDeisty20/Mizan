<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Services\DegreeService;

class DegreeController extends Controller
{
    protected DegreeService $degreeService;

    public function __construct(DegreeService $degreeService)
    {
        $this->degreeService = $degreeService;
    }

    public function index()
    {
        return $this->degreeService->index();
    }
}
