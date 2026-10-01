<?php

namespace App\Services;

use App\Http\Resources\CourtResource;
use App\Models\Court;
use App\Traits\ApiResponse;

class CourtService
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
     * List all courts.
     */
    public function index()
    {
        $courts = Court::orderBy('name')->get();

        return response()->json([
            'status'  => true,
            'message' => __('messages.success'),
            'data'    => CourtResource::collection($courts),
        ], 200);
    }

    /**
     * Show a single court.
     */
    public function show(Court $court)
    {
        return response()->json([
            'status'  => true,
            'message' => __('messages.success'),
            'data'    => new CourtResource($court),
        ], 200);
    }

    /**
     * Store a newly created court in storage.
     */
    public function store(array $data)
    {
        $deny = $this->authorizeAsAdmin();
        if ($deny) return $deny;

        $court = Court::create([
            'name' => $data['name'],
        ]);

        return response()->json([
            'status'  => true,
            'message' => __('messages.court_created_successfully'),
            'data'    => new CourtResource($court),
        ], 201);
    }

    /**
     * Update the specified court.
     */
    public function update(array $data, Court $court)
    {
        $deny = $this->authorizeAsAdmin();
        if ($deny) return $deny;

        $court->update($data);

        return response()->json([
            'status'  => true,
            'message' => __('messages.court_updated_successfully'),
            'data'    => new CourtResource($court->fresh()),
        ], 200);
    }

    /**
     * Remove the specified court from storage.
     */
    public function destroy(Court $court)
    {
        $deny = $this->authorizeAsAdmin();
        if ($deny) return $deny;

        $court->delete();

        return response()->json([
            'status'  => true,
            'message' => __('messages.court_deleted_successfully'),
        ], 200);
    }
}
