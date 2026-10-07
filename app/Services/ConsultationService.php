<?php

namespace App\Services;

use App\Http\Resources\ConsultationResource;
use App\Models\Consultation;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;

class ConsultationService
{
    use ApiResponse;

    /**
     * List all consultations for authenticated lawyer/office or client with filtering.
     */
    public function index(): JsonResponse
    {
        $user    = auth()->user();
        $perPage = request()->get('per_page', 10);
        $query   = Consultation::with(['client', 'office', 'user']);

        if ($user && $user->office_id) {
            $query->where('office_id', $user->office_id);
        }

        if (request()->filled('client_id')) {
            $query->where('client_id', request()->get('client_id'));
        }

        if (request()->filled('status')) {
            $query->where('status', request()->get('status'));
        }

        if (request()->filled('consultation_method')) {
            $query->where('consultation_method', request()->get('consultation_method'));
        }

        if (request()->filled('search')) {
            $search = request()->get('search');
            $query->where(function ($q) use ($search) {
                $q->where('subject', 'like', "%{$search}%")
                  ->orWhere('consultation_number', 'like', "%{$search}%");
            });
        }

        $consultations = $query->latest('id')->paginate($perPage);

        return $this->paginated(ConsultationResource::class, $consultations, __('messages.success'));
    }

    /**
     * Show single consultation.
     */
    public function show(Consultation $consultation): JsonResponse
    {
        $consultation->load(['client', 'office', 'user']);

        return response()->json([
            'status'  => true,
            'message' => __('messages.success'),
            'data'    => new ConsultationResource($consultation),
        ], 200);
    }

    /**
     * Store new consultation matching UI modal fields.
     */
    public function store(array $data): JsonResponse
    {
        $user = auth()->user();

        $isClientModel = $user instanceof \App\Models\Client;
        $clientId = $data['client_id'] ?? ($isClientModel ? $user->id : ($user?->client_id ?? ($user?->role === 'client' ? $user->id : null)));
        $userId   = $data['user_id'] ?? ($user?->role === 'lawyer' ? $user->id : null);
        $officeId = $data['office_id'] ?? ($isClientModel ? $user->office_id : ($user?->office_id ?? ($userId ? \App\Models\User::find($userId)?->office_id : null)));

        $consultation = Consultation::create([
            'client_id'           => $clientId,
            'office_id'           => $officeId,
            'user_id'             => $userId,
            'consultation_method' => $data['consultation_method'],
            'preferred_date'      => $data['preferred_date'],
            'preferred_time'      => $data['preferred_time'],
            'subject'             => $data['subject'],
            'status'              => 'pending',
            'reply'               => null,
            'fee'                 => null,
        ]);

        return response()->json([
            'status'  => true,
            'message' => __('messages.consultation_created_successfully'),
            'data'    => new ConsultationResource($consultation->load(['client', 'office', 'user'])),
        ], 201);
    }

    /**
     * Lawyer/Office reply to consultation.
     */
    public function reply(Consultation $consultation, array $data): JsonResponse
    {
        $user = auth()->user();

        $consultation->update([
            'user_id'    => $user?->id,
            'reply'      => $data['reply'],
            'status'     => $data['status'] ?? 'confirmed',
            'fee'        => $data['fee'] ?? $consultation->fee,
            'is_paid'    => isset($data['is_paid']) ? $data['is_paid'] : $consultation->is_paid,
        ]);

        return response()->json([
            'status'  => true,
            'message' => __('messages.consultation_replied_successfully'),
            'data'    => new ConsultationResource($consultation->fresh()->load(['client', 'office', 'user'])),
        ], 200);
    }

    /**
     * Update an existing consultation.
     */
    public function update(array $data, Consultation $consultation): JsonResponse
    {
        $updateData = array_filter($data, fn ($value) => ! is_null($value));
        $consultation->update($updateData);

        return response()->json([
            'status'  => true,
            'message' => __('messages.consultation_updated_successfully'),
            'data'    => new ConsultationResource($consultation->fresh()->load(['client', 'office', 'user'])),
        ], 200);
    }

    /**
     * Delete consultation.
     */
    public function destroy(Consultation $consultation): JsonResponse
    {
        $consultation->delete();

        return response()->json([
            'status'  => true,
            'message' => __('messages.consultation_deleted_successfully'),
        ], 200);
    }

    /**
     * List all consultations received by the lawyer's office from clients.
     */
    public function lawyerConsultations(): JsonResponse
    {
        $user = auth()->user();

        if (! $user || ! $user->office_id) {
            return response()->json([
                'status'  => false,
                'message' => __('messages.unauthorized_role'),
            ], 403);
        }

        $perPage = request()->get('per_page', 10);
        $query   = Consultation::where('office_id', $user->office_id)
            ->with(['client', 'office', 'user']);

        if (request()->filled('client_id')) {
            $query->where('client_id', request()->get('client_id'));
        }

        if (request()->filled('status')) {
            $query->where('status', request()->get('status'));
        }

        if (request()->filled('consultation_method')) {
            $query->where('consultation_method', request()->get('consultation_method'));
        }

        if (request()->filled('search')) {
            $search = request()->get('search');
            $query->where(function ($q) use ($search) {
                $q->where('subject', 'like', "%{$search}%")
                  ->orWhere('consultation_number', 'like', "%{$search}%");
            });
        }

        $consultations = $query->latest('id')->paginate($perPage);

        return $this->paginated(ConsultationResource::class, $consultations, __('messages.success'));
    }
}

