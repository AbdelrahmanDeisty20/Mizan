<?php

namespace App\Services;

use App\Http\Resources\ServiceRequestOfferResource;
use App\Http\Resources\ServiceRequestResource;
use App\Models\ServiceRequest;
use App\Models\ServiceRequestOffer;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;

class ServiceRequestService
{
    use ApiResponse;

    /**
     * Check if the authenticated user is a lawyer.
     */
    private function authorizeAsLawyer(): ?JsonResponse
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
     * List all open service requests on the exchange network with filters.
     */
    public function index(): JsonResponse
    {
        $deny = $this->authorizeAsLawyer();
        if ($deny) return $deny;

        $perPage = request()->get('per_page', 10);
        $query   = ServiceRequest::with([
            'governorate',
            'court',
            'requesterOffice',
            'user',
            'assignedOffice',
            'assignedUser',
        ])->withCount('offers');

        // Optional filter by governorate
        if (request()->filled('governorate_id')) {
            $query->where('governorate_id', request()->get('governorate_id'));
        }

        // Optional filter by court
        if (request()->filled('court_id')) {
            $query->where('court_id', request()->get('court_id'));
        }

        // Optional filter by status (default open)
        if (request()->filled('status')) {
            $query->where('status', request()->get('status'));
        } else {
            $query->where('status', 'open');
        }

        // Search in title, description, or case number
        if (request()->filled('search')) {
            $search = request()->get('search');
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('case_number', 'like', "%{$search}%");
            });
        }

        $serviceRequests = $query->latest()->paginate($perPage);

        return $this->paginated(ServiceRequestResource::class, $serviceRequests, __('messages.success'));
    }

    /**
     * List requests submitted by the authenticated lawyer.
     */
    public function myRequests(): JsonResponse
    {
        $deny = $this->authorizeAsLawyer();
        if ($deny) return $deny;

        $user    = auth()->user();
        $perPage = request()->get('per_page', 10);

        $serviceRequests = ServiceRequest::where(function ($q) use ($user) {
            $q->where('user_id', $user->id)
              ->orWhere('requester_office_id', $user->office_id);
        })
        ->with([
            'governorate',
            'court',
            'requesterOffice',
            'user',
            'assignedOffice',
            'assignedUser',
            'offers.user',
            'offers.office',
        ])
        ->withCount('offers')
        ->latest()
        ->paginate($perPage);

        return $this->paginated(ServiceRequestResource::class, $serviceRequests, __('messages.success'));
    }

    /**
     * List requests assigned to the authenticated lawyer to execute.
     */
    public function myAssigned(): JsonResponse
    {
        $deny = $this->authorizeAsLawyer();
        if ($deny) return $deny;

        $user    = auth()->user();
        $perPage = request()->get('per_page', 10);

        $serviceRequests = ServiceRequest::where('assigned_user_id', $user->id)
            ->orWhere('assigned_office_id', $user->office_id)
            ->with([
                'governorate',
                'court',
                'requesterOffice',
                'user',
                'assignedOffice',
                'assignedUser',
            ])
            ->withCount('offers')
            ->latest()
            ->paginate($perPage);

        return $this->paginated(ServiceRequestResource::class, $serviceRequests, __('messages.success'));
    }

    /**
     * Show a single service request.
     */
    public function show(ServiceRequest $serviceRequest): JsonResponse
    {
        $deny = $this->authorizeAsLawyer();
        if ($deny) return $deny;

        $serviceRequest->load([
            'governorate',
            'court',
            'requesterOffice',
            'user',
            'assignedOffice',
            'assignedUser',
            'offers.user',
            'offers.office',
        ])->loadCount('offers');

        return response()->json([
            'status'  => true,
            'message' => __('messages.success'),
            'data'    => new ServiceRequestResource($serviceRequest),
        ], 200);
    }

    /**
     * Create a new service request.
     */
    public function store(array $data): JsonResponse
    {
        $deny = $this->authorizeAsLawyer();
        if ($deny) return $deny;

        $user = auth()->user();

        $serviceRequest = ServiceRequest::create([
            'requester_office_id' => $user->office_id,
            'user_id'             => $user->id,
            'title'               => $data['title'],
            'description'         => $data['description'],
            'governorate_id'      => $data['governorate_id'],
            'court_id'            => $data['court_id'],
            'case_number'         => $data['case_number'] ?? null,
            'due_date'            => $data['due_date'],
            'offered_fee'         => $data['offered_fee'],
            'contact_phone'       => $data['contact_phone'] ?? $user->phone,
            'status'              => 'open',
        ]);

        return response()->json([
            'status'  => true,
            'message' => __('messages.service_request_created_successfully'),
            'data'    => new ServiceRequestResource($serviceRequest->load(['governorate', 'court', 'user', 'requesterOffice'])),
        ], 201);
    }

    /**
     * Update an existing service request.
     */
    public function update(array $data, ServiceRequest $serviceRequest): JsonResponse
    {
        $deny = $this->authorizeAsLawyer();
        if ($deny) return $deny;

        $user = auth()->user();

        if ($serviceRequest->user_id !== $user->id && $serviceRequest->requester_office_id !== $user->office_id) {
            return response()->json([
                'status'  => false,
                'message' => __('messages.unauthorized_role'),
            ], 403);
        }

        $serviceRequest->update($data);

        return response()->json([
            'status'  => true,
            'message' => __('messages.service_request_updated_successfully'),
            'data'    => new ServiceRequestResource($serviceRequest->fresh()->load(['governorate', 'court', 'user', 'requesterOffice'])),
        ], 200);
    }

    /**
     * Delete a service request.
     */
    public function destroy(ServiceRequest $serviceRequest): JsonResponse
    {
        $deny = $this->authorizeAsLawyer();
        if ($deny) return $deny;

        $user = auth()->user();

        if ($serviceRequest->user_id !== $user->id && $serviceRequest->requester_office_id !== $user->office_id) {
            return response()->json([
                'status'  => false,
                'message' => __('messages.unauthorized_role'),
            ], 403);
        }

        $serviceRequest->delete();

        return response()->json([
            'status'  => true,
            'message' => __('messages.service_request_deleted_successfully'),
        ], 200);
    }

    /**
     * Submit or update an offer on a service request.
     */
    public function submitOffer(array $data, ServiceRequest $serviceRequest): JsonResponse
    {
        $deny = $this->authorizeAsLawyer();
        if ($deny) return $deny;

        $user = auth()->user();

        if ($serviceRequest->user_id === $user->id) {
            return response()->json([
                'status'  => false,
                'message' => __('messages.cannot_offer_own_request'),
            ], 422);
        }

        if ($serviceRequest->status !== 'open') {
            return response()->json([
                'status'  => false,
                'message' => __('messages.service_request_not_open'),
            ], 422);
        }

        $offer = ServiceRequestOffer::updateOrCreate(
            [
                'service_request_id' => $serviceRequest->id,
                'user_id'            => $user->id,
            ],
            [
                'office_id'    => $user->office_id,
                'proposed_fee' => $data['proposed_fee'],
                'notes'        => $data['notes'] ?? null,
                'status'       => 'pending',
            ]
        );

        return response()->json([
            'status'  => true,
            'message' => __('messages.offer_submitted_successfully'),
            'data'    => new ServiceRequestOfferResource($offer->load(['user', 'office'])),
        ], 201);
    }

    /**
     * Accept an offer and assign the service request to the corresponding lawyer.
     */
    public function acceptOffer(ServiceRequest $serviceRequest, ServiceRequestOffer $offer): JsonResponse
    {
        $deny = $this->authorizeAsLawyer();
        if ($deny) return $deny;

        $user = auth()->user();

        if ($serviceRequest->user_id !== $user->id && $serviceRequest->requester_office_id !== $user->office_id) {
            return response()->json([
                'status'  => false,
                'message' => __('messages.unauthorized_role'),
            ], 403);
        }

        if ($offer->service_request_id !== $serviceRequest->id) {
            return response()->json([
                'status'  => false,
                'message' => __('messages.offer_does_not_belong_to_request'),
            ], 422);
        }

        // Accept this offer
        $offer->update(['status' => 'accepted']);

        // Reject other offers for this request
        ServiceRequestOffer::where('service_request_id', $serviceRequest->id)
            ->where('id', '!=', $offer->id)
            ->update(['status' => 'rejected']);

        // Assign service request to the offering lawyer
        $serviceRequest->update([
            'status'            => 'assigned',
            'assigned_office_id' => $offer->office_id,
            'assigned_user_id'   => $offer->user_id,
        ]);

        return response()->json([
            'status'  => true,
            'message' => __('messages.offer_accepted_successfully'),
            'data'    => new ServiceRequestResource($serviceRequest->fresh()->load([
                'governorate',
                'court',
                'user',
                'requesterOffice',
                'assignedUser',
                'assignedOffice',
                'offers.user',
            ])),
        ], 200);
    }
}
