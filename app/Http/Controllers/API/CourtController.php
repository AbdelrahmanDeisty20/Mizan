<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\API\StoreCourtRequest;
use App\Http\Requests\API\UpdateCourtRequest;
use App\Models\Court;
use App\Services\CourtService;

class CourtController extends Controller
{
    protected CourtService $courtService;

    public function __construct(CourtService $courtService)
    {
        $this->courtService = $courtService;
    }

    /**
     * Display a listing of courts.
     */
    public function index()
    {
        return $this->courtService->index();
    }

    /**
     * Store a newly created court.
     */
    public function store(StoreCourtRequest $request)
    {
        return $this->courtService->store($request->validated());
    }

    /**
     * Display the specified court.
     */
    public function show(Court $court)
    {
        return $this->courtService->show($court);
    }

    /**
     * Update the specified court.
     */
    public function update(UpdateCourtRequest $request, Court $court)
    {
        return $this->courtService->update($request->validated(), $court);
    }

    /**
     * Remove the specified court.
     */
    public function destroy(Court $court)
    {
        return $this->courtService->destroy($court);
    }
}
