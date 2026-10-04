<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\API\StoreHearingTypeRequest;
use App\Http\Requests\API\UpdateHearingTypeRequest;
use App\Models\HearingType;
use App\Services\HearingTypeService;

class HearingTypeController extends Controller
{
    protected HearingTypeService $hearingTypeService;

    public function __construct(HearingTypeService $hearingTypeService)
    {
        $this->hearingTypeService = $hearingTypeService;
    }

    /**
     * Display a listing of hearing types.
     */
    public function index()
    {
        return $this->hearingTypeService->index();
    }

    /**
     * Store a newly created hearing type.
     */
    public function store(StoreHearingTypeRequest $request)
    {
        return $this->hearingTypeService->store($request->validated());
    }

    /**
     * Display the specified hearing type.
     */
    public function show(HearingType $hearingType)
    {
        return $this->hearingTypeService->show($hearingType);
    }

    /**
     * Update the specified hearing type.
     */
    public function update(UpdateHearingTypeRequest $request, HearingType $hearingType)
    {
        return $this->hearingTypeService->update($request->validated(), $hearingType);
    }

    /**
     * Remove the specified hearing type.
     */
    public function destroy(HearingType $hearingType)
    {
        return $this->hearingTypeService->destroy($hearingType);
    }
}
