<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\API\StoreServiceOfferRequest;
use App\Http\Requests\API\StoreServiceRequestRequest;
use App\Http\Requests\API\UpdateServiceRequestRequest;
use App\Models\ServiceRequest;
use App\Models\ServiceRequestOffer;
use App\Services\ServiceRequestService;

class ServiceRequestController extends Controller
{
    protected ServiceRequestService $serviceRequestService;

    public function __construct(ServiceRequestService $serviceRequestService)
    {
        $this->serviceRequestService = $serviceRequestService;
    }

    /**
     * Display a listing of available service requests in the network.
     */
    public function index()
    {
        return $this->serviceRequestService->index();
    }

    /**
     * Display service requests created by the authenticated lawyer.
     */
    public function myRequests()
    {
        return $this->serviceRequestService->myRequests();
    }

    /**
     * Display service requests assigned to the authenticated lawyer.
     */
    public function myAssigned()
    {
        return $this->serviceRequestService->myAssigned();
    }

    /**
     * Store a newly created service request.
     */
    public function store(StoreServiceRequestRequest $request)
    {
        return $this->serviceRequestService->store($request->validated());
    }

    /**
     * Display the specified service request.
     */
    public function show(ServiceRequest $serviceRequest)
    {
        return $this->serviceRequestService->show($serviceRequest);
    }

    /**
     * Update the specified service request.
     */
    public function update(UpdateServiceRequestRequest $request, ServiceRequest $serviceRequest)
    {
        return $this->serviceRequestService->update($request->validated(), $serviceRequest);
    }

    /**
     * Remove the specified service request.
     */
    public function destroy(ServiceRequest $serviceRequest)
    {
        return $this->serviceRequestService->destroy($serviceRequest);
    }

    /**
     * Submit an offer on a service request.
     */
    public function submitOffer(StoreServiceOfferRequest $request, ServiceRequest $serviceRequest)
    {
        return $this->serviceRequestService->submitOffer($request->validated(), $serviceRequest);
    }

    /**
     * Accept an offer for a service request.
     */
    public function acceptOffer(ServiceRequest $serviceRequest, ServiceRequestOffer $offer)
    {
        return $this->serviceRequestService->acceptOffer($serviceRequest, $offer);
    }
}
