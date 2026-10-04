<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ServiceRequestResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'               => $this->id,
            'title'            => $this->title,
            'description'      => $this->description,
            'case_number'      => $this->case_number,
            'due_date'         => $this->due_date?->format('Y-m-d'),
            'offered_fee'      => (float) $this->offered_fee,
            'status'           => $this->status,
            'contact_phone'    => $this->contact_phone,
            'governorate'      => new GovernorateResource($this->whenLoaded('governorate')),
            'court'            => new CourtResource($this->whenLoaded('court')),
            'requester_office' => new OfficeResource($this->whenLoaded('requesterOffice')),
            'user'             => new UserResource($this->whenLoaded('user')),
            'assigned_office'  => new OfficeResource($this->whenLoaded('assignedOffice')),
            'assigned_user'    => new UserResource($this->whenLoaded('assignedUser')),
            'offers_count'     => $this->whenCounted('offers', $this->offers_count ?? $this->offers()->count()),
            'offers'           => ServiceRequestOfferResource::collection($this->whenLoaded('offers')),
            'created_at'       => $this->created_at?->toDateTimeString(),
        ];
    }
}
