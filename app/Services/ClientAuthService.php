<?php

namespace App\Services;

use App\Http\Resources\ClientResource;
use App\Models\Client;
use Hash;

class ClientAuthService
{
    /**
     * Authenticate a client using access_code and password.
     */
    public function login(array $data)
    {
        $client = Client::where('access_code', $data['access_code'])->first();

        if (! $client || ! Hash::check($data['password'], $client->password)) {
            return response()->json([
                'status'  => false,
                'message' => __('messages.invalid_credentials'),
            ], 401);
        }

        $token = $client->createToken('client_token')->plainTextToken;

        return response()->json([
            'status'  => true,
            'message' => __('messages.user_logged_in_successfully'),
            'token'   => $token,
            'data'    => new ClientResource($client->load('governorate')),
        ], 200);
    }
}
