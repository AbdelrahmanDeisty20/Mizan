<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ClientResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'          => $this->id,
            'name'        => $this->name,
            'national_id' => $this->national_id,
            'phone'       => $this->phone,
            'whatsapp'    => $this->whatsapp,
            'governorate' => new GovernorateResource($this->whenLoaded('governorate')),
            'address'     => $this->address,
            'access_code' => $this->access_code,
            'notes'       => $this->notes,
            'created_at'  => $this->created_at?->toDateTimeString(),
        ];
    }
}
