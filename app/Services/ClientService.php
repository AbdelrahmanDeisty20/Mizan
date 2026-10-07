<?php

namespace App\Services;

use App\Http\Resources\ClientResource;
use App\Models\Client;
use App\Models\FinancialReceipt;
use App\Models\LegalCase;
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

        if (! \Illuminate\Support\Facades\Hash::check($data['password'], $user->password)) {
            return response()->json([
                'status'  => false,
                'message' => __('messages.lawyer_password_invalid'),
            ], 422);
        }

        // Generate unique access code for client tracking
        do {
            $accessCode = 'CLI-' . str_pad((string) mt_rand(1, 999999), 6, '0', STR_PAD_LEFT);
        } while (Client::where('access_code', $accessCode)->exists());

        $imagePath = null;
        if (isset($data['image']) && $data['image'] instanceof \Illuminate\Http\UploadedFile) {
            $imagePath = $data['image']->store('clients', 'public');
        }

        $client = Client::create([
            'office_id'      => $user->office_id,
            'name'           => $data['name'],
            'phone'          => $data['phone'],
            'national_id'    => $data['national_id'] ?? null,
            'whatsapp'       => $data['whatsapp'] ?? null,
            'governorate_id' => $data['governorate_id'] ?? null,
            'address'        => $data['address'] ?? null,
            'image'          => $imagePath,
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

        if (! \Illuminate\Support\Facades\Hash::check($data['password'], auth()->user()->password)) {
            return response()->json([
                'status'  => false,
                'message' => __('messages.lawyer_password_invalid'),
            ], 422);
        }

        if (isset($data['image']) && $data['image'] instanceof \Illuminate\Http\UploadedFile) {
            if ($client->image && \Illuminate\Support\Facades\Storage::disk('public')->exists($client->image)) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($client->image);
            }
            $data['image'] = $data['image']->store('clients', 'public');
        }

        unset($data['password'], $data['password_confirmation']);

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

    /**
     * Get fees summary for the authenticated client.
     */
    public function feesSummary()
    {
        $client = auth()->user();

        if (! $client || ! ($client instanceof Client)) {
            return response()->json([
                'status'  => false,
                'message' => __('messages.unauthorized_role'),
            ], 403);
        }

        $totalAgreed  = (float) LegalCase::where('client_id', $client->id)->sum('total_fees');
        $receiptsPaid = (float) FinancialReceipt::where('client_id', $client->id)->sum('amount');
        $casesPaid    = (float) LegalCase::where('client_id', $client->id)->sum('paid_fees');

        $totalPaid      = max($receiptsPaid, $casesPaid);
        $totalRemaining = max(0, $totalAgreed - $totalPaid);

        return response()->json([
            'status'  => true,
            'message' => __('messages.success'),
            'data'    => [
                'total_agreed_fees'    => $totalAgreed,
                'total_paid_fees'      => $totalPaid,
                'total_remaining_fees' => $totalRemaining,
            ],
        ], 200);
    }
}

