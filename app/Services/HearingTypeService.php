<?php

namespace App\Services;

use App\Http\Resources\HearingTypeResource;
use App\Models\HearingType;
use App\Traits\ApiResponse;

class HearingTypeService
{
    use ApiResponse;

    /**
     * Check if the authenticated user is an admin.
     */
    private function authorizeAsAdmin(): ?\Illuminate\Http\JsonResponse
    {
        $user = auth()->user();

        if (! $user || $user->role !== 'admin') {
            return response()->json([
                'status'  => false,
                'message' => __('messages.unauthorized_role'),
            ], 403);
        }

        return null;
    }

    /**
     * List all hearing types.
     */
    public function index()
    {
        $hearingTypes = HearingType::orderBy('name')->get();

        return response()->json([
            'status'  => true,
            'message' => __('messages.success'),
            'data'    => HearingTypeResource::collection($hearingTypes),
        ], 200);
    }

    /**
     * Show a single hearing type.
     */
    public function show(HearingType $hearingType)
    {
        return response()->json([
            'status'  => true,
            'message' => __('messages.success'),
            'data'    => new HearingTypeResource($hearingType),
        ], 200);
    }

    /**
     * Store a newly created hearing type in storage.
     */
    public function store(array $data)
    {
        $deny = $this->authorizeAsAdmin();
        if ($deny) return $deny;

        $hearingType = HearingType::create([
            'name' => $data['name'],
        ]);

        return response()->json([
            'status'  => true,
            'message' => __('messages.hearing_type_created_successfully'),
            'data'    => new HearingTypeResource($hearingType),
        ], 201);
    }

    /**
     * Update the specified hearing type.
     */
    public function update(array $data, HearingType $hearingType)
    {
        $deny = $this->authorizeAsAdmin();
        if ($deny) return $deny;

        $hearingType->update($data);

        return response()->json([
            'status'  => true,
            'message' => __('messages.hearing_type_updated_successfully'),
            'data'    => new HearingTypeResource($hearingType->fresh()),
        ], 200);
    }

    /**
     * Remove the specified hearing type from storage.
     */
    public function destroy(HearingType $hearingType)
    {
        $deny = $this->authorizeAsAdmin();
        if ($deny) return $deny;

        $hearingType->delete();

        return response()->json([
            'status'  => true,
            'message' => __('messages.hearing_type_deleted_successfully'),
        ], 200);
    }
}
