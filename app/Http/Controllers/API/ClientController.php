<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\API\StoreClientRequest;
use App\Http\Requests\API\UpdateClientRequest;
use App\Models\Client;
use App\Services\ClientService;
use App\Traits\ApiResponse;

class ClientController extends Controller
{
    use ApiResponse;

    protected ClientService $clientService;

    public function __construct(ClientService $clientService)
    {
        $this->clientService = $clientService;
    }

    /**
     * Display a listing of clients for the authenticated office.
     */
    public function index()
    {
        return $this->clientService->index();
    }

    /**
     * Store a newly created client.
     */
    public function store(StoreClientRequest $request)
    {
        return $this->clientService->store($request->validated());
    }

    /**
     * Display the specified client.
     */
    public function show(Client $client)
    {
        return $this->clientService->show($client);
    }

    /**
     * Update the specified client.
     */
    public function update(UpdateClientRequest $request, Client $client)
    {
        return $this->clientService->update($request->validated(), $client);
    }

    /**
     * Remove the specified client.
     */
    public function destroy(Client $client)
    {
        return $this->clientService->destroy($client);
    }

    /**
     * Display fees summary for the authenticated client.
     */
    public function feesSummary()
    {
        return $this->clientService->feesSummary();
    }

    /**
     * Display financial receipts for the authenticated client.
     */
    public function receipts()
    {
        return $this->clientService->receipts();
    }
}


