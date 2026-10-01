<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\API\StoreHearingRequest;
use App\Http\Requests\API\UpdateHearingRequest;
use App\Models\Hearing;
use App\Services\HearingService;

class HearingController extends Controller
{
    protected HearingService $hearingService;

    public function __construct(HearingService $hearingService)
    {
        $this->hearingService = $hearingService;
    }

    /**
     * Display a listing of hearings for the authenticated office.
     */
    public function index()
    {
        return $this->hearingService->index();
    }

    /**
     * Store a newly created hearing.
     */
    public function store(StoreHearingRequest $request)
    {
        return $this->hearingService->store($request->validated());
    }

    /**
     * Display the specified hearing.
     */
    public function show(Hearing $hearing)
    {
        return $this->hearingService->show($hearing);
    }

    /**
     * Update the specified hearing.
     */
    public function update(UpdateHearingRequest $request, Hearing $hearing)
    {
        return $this->hearingService->update($request->validated(), $hearing);
    }

    /**
     * Remove the specified hearing.
     */
    public function destroy(Hearing $hearing)
    {
        return $this->hearingService->destroy($hearing);
    }
}
