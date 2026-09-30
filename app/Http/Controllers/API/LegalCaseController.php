<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\API\StoreLegalCaseRequest;
use App\Http\Requests\API\UpdateLegalCaseRequest;
use App\Models\LegalCase;
use App\Services\LegalCaseService;

class LegalCaseController extends Controller
{
    protected LegalCaseService $legalCaseService;

    public function __construct(LegalCaseService $legalCaseService)
    {
        $this->legalCaseService = $legalCaseService;
    }

    /**
     * Display a listing of legal cases for the authenticated office.
     */
    public function index()
    {
        return $this->legalCaseService->index();
    }

    /**
     * Store a newly created legal case.
     */
    public function store(StoreLegalCaseRequest $request)
    {
        return $this->legalCaseService->store($request->validated());
    }

    /**
     * Display the specified legal case.
     */
    public function show(LegalCase $legalCase)
    {
        return $this->legalCaseService->show($legalCase);
    }

    /**
     * Update the specified legal case.
     */
    public function update(UpdateLegalCaseRequest $request, LegalCase $legalCase)
    {
        return $this->legalCaseService->update($request->validated(), $legalCase);
    }

    /**
     * Remove the specified legal case.
     */
    public function destroy(LegalCase $legalCase)
    {
        return $this->legalCaseService->destroy($legalCase);
    }
}
