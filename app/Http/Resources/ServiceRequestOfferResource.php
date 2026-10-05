<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ServiceRequestOfferResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'           => $this->id,
            'proposed_fee' => (float) $this->proposed_fee,
            'notes'        => $this->notes,
            'status'       => $this->status,
            'user'            => new UserResource($this->whenLoaded('user')),
            'office'          => new OfficeResource($this->whenLoaded('office')),
            'service_request' => new ServiceRequestResource($this->whenLoaded('serviceRequest')),
            'created_at'      => $this->created_at?->toDateTimeString(),
        ];
    }
}
