<?php

namespace App\Services;

use App\Http\Resources\HearingResource;
use App\Models\Hearing;
use App\Traits\ApiResponse;

class HearingService
{
    use ApiResponse;

    /**
     * Check if the authenticated user is a lawyer.
     */
    private function authorizeAsLawyer(): ?\Illuminate\Http\JsonResponse
    {
        $user = auth()->user();

        if (! $user || $user->role !== 'lawyer') {
            return response()->json([
                'status'  => false,
                'message' => __('messages.unauthorized_role'),
            ], 403);
        }

        return null;
    }

    /**
     * List all hearings belonging to the authenticated office.
     */
    public function index()
    {
        $deny = $this->authorizeAsLawyer();
        if ($deny) return $deny;

        $perPage = request()->get('per_page', 10);

        $hearings = Hearing::where('office_id', auth()->user()->office_id)
            ->with(['legalCase', 'legalCase.client', 'hearingType'])
            ->latest('hearing_date')
            ->paginate($perPage);

        return $this->paginated(HearingResource::class, $hearings, __('messages.success'));
    }

    /**
     * Show a single hearing.
     */
    public function show(Hearing $hearing)
    {
        $deny = $this->authorizeAsLawyer();
        if ($deny) return $deny;

        if ($hearing->office_id !== auth()->user()->office_id) {
            return response()->json([
                'status'  => false,
                'message' => __('messages.unauthorized_role'),
            ], 403);
        }

        return response()->json([
            'status'  => true,
            'message' => __('messages.success'),
            'data'    => new HearingResource($hearing->load('legalCase', 'hearingType')),
        ], 200);
    }

    /**
     * Store a newly created hearing in storage.
     */
    public function store(array $data)
    {
        $deny = $this->authorizeAsLawyer();
        if ($deny) return $deny;

        $user = auth()->user();

        $hearing = Hearing::create([
            'office_id'          => $user->office_id,
            'legal_case_id'      => $data['legal_case_id'],
            'assigned_lawyer_id' => $data['assigned_lawyer_id'] ?? null,
            'hearing_date'       => $data['hearing_date'],
            'hearing_time'       => $data['hearing_time'] ?? null,
            'hearing_type_id'    => $data['hearing_type_id'] ?? null,
            'court_room'         => $data['court_room'] ?? null,
            'roll_number'        => $data['roll_number'] ?? null,
            'decision'           => $data['decision'] ?? null,
            'requirements'       => $data['requirements'] ?? null,
            'status'             => $data['status'] ?? 'مقبلة',
        ]);

        return response()->json([
            'status'  => true,
            'message' => __('messages.hearing_created_successfully'),
            'data'    => new HearingResource($hearing->load('legalCase', 'hearingType')),
        ], 201);
    }

    /**
     * Update the specified hearing.
     */
    public function update(array $data, Hearing $hearing)
    {
        $deny = $this->authorizeAsLawyer();
        if ($deny) return $deny;

        if ($hearing->office_id !== auth()->user()->office_id) {
            return response()->json([
                'status'  => false,
                'message' => __('messages.unauthorized_role'),
            ], 403);
        }

        $hearing->update($data);

        return response()->json([
            'status'  => true,
            'message' => __('messages.hearing_updated_successfully'),
            'data'    => new HearingResource($hearing->fresh()->load('legalCase', 'hearingType')),
        ], 200);
    }

    /**
     * Remove the specified hearing from storage.
     */
    public function destroy(Hearing $hearing)
    {
        $deny = $this->authorizeAsLawyer();
        if ($deny) return $deny;

        if ($hearing->office_id !== auth()->user()->office_id) {
            return response()->json([
                'status'  => false,
                'message' => __('messages.unauthorized_role'),
            ], 403);
        }

        $hearing->delete();

        return response()->json([
            'status'  => true,
            'message' => __('messages.hearing_deleted_successfully'),
        ], 200);
    }
}
