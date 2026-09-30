<?php

namespace App\Services;

use App\Http\Resources\ClientResource;
use App\Models\Client;
use App\Traits\ApiResponse;

class ClientService
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
     * List all clients belonging to the authenticated office.
     */
    public function index()
    {
        $deny = $this->authorizeAsLawyer();
        if ($deny) return $deny;

        $perPage = request()->get('per_page', 10);

        $clients = Client::where('office_id', auth()->user()->office_id)
            ->with('governorate')
            ->latest()
            ->paginate($perPage);

        return $this->paginated(ClientResource::class, $clients, __('messages.success'));
    }

    /**
     * Show a single client.
     */
    public function show(Client $client)
    {
        $deny = $this->authorizeAsLawyer();
        if ($deny) return $deny;

        if ($client->office_id !== auth()->user()->office_id) {
            return response()->json([
                'status'  => false,
                'message' => __('messages.unauthorized_role'),
            ], 403);
        }

        return response()->json([
            'status'  => true,
            'message' => __('messages.success'),
            'data'    => new ClientResource($client->load('governorate')),
        ], 200);
    }

    /**
     * Store a newly created client in storage.
     */
    public function store(array $data)
    {
        $deny = $this->authorizeAsLawyer();
        if ($deny) return $deny;

        $user = auth()->user();

        // Generate unique access code for client tracking
        do {
            $accessCode = 'CLI-' . str_pad((string) mt_rand(1, 999999), 6, '0', STR_PAD_LEFT);
        } while (Client::where('access_code', $accessCode)->exists());

        $client = Client::create([
            'office_id'      => $user->office_id,
            'name'           => $data['name'],
            'phone'          => $data['phone'],
            'password'       => $data['password'],
            'national_id'    => $data['national_id'] ?? null,
            'whatsapp'       => $data['whatsapp'] ?? null,
            'governorate_id' => $data['governorate_id'] ?? null,
            'address'        => $data['address'] ?? null,
            'access_code'    => $accessCode,
            'notes'          => $data['notes'] ?? null,
        ]);

        return response()->json([
            'status'  => true,
            'message' => __('messages.client_created_successfully'),
            'data'    => new ClientResource($client->load('governorate')),
        ], 201);
    }

    /**
     * Update the specified client.
     */
    public function update(array $data, Client $client)
    {
        $deny = $this->authorizeAsLawyer();
        if ($deny) return $deny;

        if ($client->office_id !== auth()->user()->office_id) {
            return response()->json([
                'status'  => false,
                'message' => __('messages.unauthorized_role'),
            ], 403);
        }

        $client->update($data);

        return response()->json([
            'status'  => true,
            'message' => __('messages.client_updated_successfully'),
            'data'    => new ClientResource($client->fresh('governorate')),
        ], 200);
    }

    /**
     * Remove the specified client from storage.
     */
    public function destroy(Client $client)
    {
        $deny = $this->authorizeAsLawyer();
        if ($deny) return $deny;

        if ($client->office_id !== auth()->user()->office_id) {
            return response()->json([
                'status'  => false,
                'message' => __('messages.unauthorized_role'),
            ], 403);
        }

        $client->delete();

        return response()->json([
            'status'  => true,
            'message' => __('messages.client_deleted_successfully'),
        ], 200);
    }
}
