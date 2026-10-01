<?php

namespace App\Services;

use App\Http\Resources\GovernorateResource;
use App\Models\Governorate;

class GovernorateService
{
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
     * List all governorates.
     */
    public function index()
    {
        $governorates = Governorate::all();

        return response()->json([
            'status'  => true,
            'message' => __('messages.success'),
            'data'    => GovernorateResource::collection($governorates),
        ], 200);
    }

    /**
     * Show a single governorate.
     */
    public function show(Governorate $governorate)
    {
        return response()->json([
            'status'  => true,
            'message' => __('messages.success'),
            'data'    => new GovernorateResource($governorate),
        ], 200);
    }

    /**
     * Store a newly created governorate in storage.
     */
    public function store(array $data)
    {
        $deny = $this->authorizeAsAdmin();
        if ($deny) return $deny;

        $governorate = Governorate::create([
            'name_ar' => $data['name_ar'],
            'name_en' => $data['name_en'] ?? null,
        ]);

        return response()->json([
            'status'  => true,
            'message' => __('messages.governorate_created_successfully'),
            'data'    => new GovernorateResource($governorate),
        ], 201);
    }

    /**
     * Update the specified governorate.
     */
    public function update(array $data, Governorate $governorate)
    {
        $deny = $this->authorizeAsAdmin();
        if ($deny) return $deny;

        $governorate->update($data);

        return response()->json([
            'status'  => true,
            'message' => __('messages.governorate_updated_successfully'),
            'data'    => new GovernorateResource($governorate->fresh()),
        ], 200);
    }

    /**
     * Remove the specified governorate from storage.
     */
    public function destroy(Governorate $governorate)
    {
        $deny = $this->authorizeAsAdmin();
        if ($deny) return $deny;

        $governorate->delete();

        return response()->json([
            'status'  => true,
            'message' => __('messages.governorate_deleted_successfully'),
        ], 200);
    }
}
