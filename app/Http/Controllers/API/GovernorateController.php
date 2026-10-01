<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\API\StoreGovernorateRequest;
use App\Http\Requests\API\UpdateGovernorateRequest;
use App\Models\Governorate;
use App\Services\GovernorateService;

class GovernorateController extends Controller
{
    protected GovernorateService $governorateService;

    public function __construct(GovernorateService $governorateService)
    {
        $this->governorateService = $governorateService;
    }

    /**
     * Display a listing of governorates.
     */
    public function index()
    {
        return $this->governorateService->index();
    }

    /**
     * Store a newly created governorate.
     */
    public function store(StoreGovernorateRequest $request)
    {
        return $this->governorateService->store($request->validated());
    }

    /**
     * Display the specified governorate.
     */
    public function show(Governorate $governorate)
    {
        return $this->governorateService->show($governorate);
    }

    /**
     * Update the specified governorate.
     */
    public function update(UpdateGovernorateRequest $request, Governorate $governorate)
    {
        return $this->governorateService->update($request->validated(), $governorate);
    }

    /**
     * Remove the specified governorate.
     */
    public function destroy(Governorate $governorate)
    {
        return $this->governorateService->destroy($governorate);
    }
}
