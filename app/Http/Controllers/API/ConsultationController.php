<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\API\ReplyConsultationRequest;
use App\Http\Requests\API\StoreConsultationRequest;
use App\Http\Requests\API\UpdateConsultationRequest;
use App\Models\Consultation;
use App\Services\ConsultationService;
use Illuminate\Http\JsonResponse;

class ConsultationController extends Controller
{
    public function __construct(protected ConsultationService $consultationService) {}

    /**
     * Display a listing of consultations.
     */
    public function index(): JsonResponse
    {
        return $this->consultationService->index();
    }

    /**
     * Store a newly created consultation.
     */
    public function store(StoreConsultationRequest $request): JsonResponse
    {
        return $this->consultationService->store($request->validated());
    }

    /**
     * Display the specified consultation.
     */
    public function show(Consultation $consultation): JsonResponse
    {
        return $this->consultationService->show($consultation);
    }

    /**
     * Update the specified consultation.
     */
    public function update(UpdateConsultationRequest $request, Consultation $consultation): JsonResponse
    {
        return $this->consultationService->update($request->validated(), $consultation);
    }

    /**
     * Reply to the specified consultation.
     */
    public function reply(ReplyConsultationRequest $request, Consultation $consultation): JsonResponse
    {
        return $this->consultationService->reply($consultation, $request->validated());
    }

    /**
     * Remove the specified consultation.
     */
    public function destroy(Consultation $consultation): JsonResponse
    {
        return $this->consultationService->destroy($consultation);
    }

    /**
     * Display listing of consultations received by lawyer's office.
     */
    public function lawyerConsultations(): JsonResponse
    {
        return $this->consultationService->lawyerConsultations();
    }

    /**
     * Lawyer accepts consultation.
     */
    public function accept(\Illuminate\Http\Request $request, Consultation $consultation): JsonResponse
    {
        return $this->consultationService->accept($consultation, $request->all());
    }

    /**
     * Lawyer rejects consultation.
     */
    public function reject(\Illuminate\Http\Request $request, Consultation $consultation): JsonResponse
    {
        return $this->consultationService->reject($consultation, $request->all());
    }
}


