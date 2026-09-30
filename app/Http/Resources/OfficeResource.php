<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OfficeResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->office_name,
            'syndicate_card_id' => $this->syndicate_card_id,
            'degree' => new DegreeResource($this->whenLoaded('degree')),
            'governorate' => new GovernorateResource($this->whenLoaded('governorate')),
            'address' => $this->office_address,
            'phone' => $this->office_phone,
            'trial_active' => $this->isTrialActive(),
            'created_at' => $this->created_at?->toDateTimeString(),
        ];
    }
}
