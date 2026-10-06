<?php

namespace App\Services;

use App\Http\Resources\LegalCaseResource;
use App\Models\LegalCase;
use App\Traits\ApiResponse;

class LegalCaseService
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
     * List all legal cases belonging to the authenticated office.
     */
    public function index()
    {
        $perPage = request()->get('per_page', 10);

        $cases = LegalCase::where('office_id', auth()->user()->office_id)
            ->with('client', 'court')
            ->latest()
            ->paginate($perPage);

        return $this->paginated(LegalCaseResource::class, $cases, __('messages.success'));
    }

    /**
     * Show a single legal case.
     */
    public function show(LegalCase $legalCase)
    {
        $deny = $this->authorizeAsLawyer();
        if ($deny) return $deny;

        if ($legalCase->office_id !== auth()->user()->office_id) {
            return response()->json([
                'status'  => false,
                'message' => __('messages.unauthorized_role'),
            ], 403);
        }

        return response()->json([
            'status'  => true,
            'message' => __('messages.success'),
            'data'    => new LegalCaseResource($legalCase->load('client', 'court')),
        ], 200);
    }

    /**
     * Store a newly created legal case in storage.
     */
    public function store(array $data)
    {
        $deny = $this->authorizeAsLawyer();
        if ($deny) return $deny;

        $user = auth()->user();

        $paidFees      = $data['paid_fees'] ?? 0;
        $totalFees     = $data['total_fees'] ?? 0;
        $remainingFees = max(0, $totalFees - $paidFees);

        $legalCase = LegalCase::create([
            'office_id'       => $user->office_id,
            'client_id'       => $data['client_id'] ?? null,
            'client_role'     => $data['client_role'],
            'court_id'        => $data['court_id'],
            'case_number'     => $data['case_number'],
            'year'            => $data['year'],
            'degree'          => $data['degree'],
            'case_type'       => $data['case_type'],
            'status'          => $data['status'] ?? 'active',
            'opponent_name'   => $data['opponent_name'] ?? null,
            'opponent_lawyer' => $data['opponent_lawyer'] ?? null,
            'total_fees'      => $totalFees,
            'paid_fees'       => $paidFees,
            'remaining_fees'  => $remainingFees,
            'notes'           => $data['notes'] ?? null,
        ]);

        return response()->json([
            'status'  => true,
            'message' => __('messages.legal_case_created_successfully'),
            'data'    => new LegalCaseResource($legalCase->load('client', 'court')),
        ], 201);
    }

    /**
     * Update the specified legal case.
     */
    public function update(array $data, LegalCase $legalCase)
    {
        $deny = $this->authorizeAsLawyer();
        if ($deny) return $deny;

        if ($legalCase->office_id !== auth()->user()->office_id) {
            return response()->json([
                'status'  => false,
                'message' => __('messages.unauthorized_role'),
            ], 403);
        }

        $totalFees = $data['total_fees'] ?? $legalCase->total_fees;
        $paidFees  = $data['paid_fees'] ?? $legalCase->paid_fees;
        $data['remaining_fees'] = max(0, $totalFees - $paidFees);

        $legalCase->update($data);

        return response()->json([
            'status'  => true,
            'message' => __('messages.legal_case_updated_successfully'),
            'data'    => new LegalCaseResource($legalCase->fresh()->load('client', 'court')),
        ], 200);
    }

    /**
     * Remove the specified legal case from storage.
     */
    public function destroy(LegalCase $legalCase)
    {
        $deny = $this->authorizeAsLawyer();
        if ($deny) return $deny;

        if ($legalCase->office_id !== auth()->user()->office_id) {
            return response()->json([
                'status'  => false,
                'message' => __('messages.unauthorized_role'),
            ], 403);
        }

        $legalCase->delete();

        return response()->json([
            'status'  => true,
            'message' => __('messages.legal_case_deleted_successfully'),
        ], 200);
    }
}
