<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\CourtResource;
use App\Http\Resources\ClientResource;

class LegalCaseResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'              => $this->id,
            'case_number'     => $this->case_number,
            'year'            => $this->year,
            'court'           => new CourtResource($this->whenLoaded('court')),
            'degree'          => $this->degree,
            'case_type'       => $this->case_type,
            'status'          => $this->status,
            'client_role'     => $this->client_role,
            'opponent_name'   => $this->opponent_name,
            'opponent_lawyer' => $this->opponent_lawyer,
            'total_fees'      => $this->total_fees,
            'paid_fees'       => $this->paid_fees,
            'remaining_fees'  => $this->remaining_fees,
            'notes'           => $this->notes,
            'client'          => new ClientResource($this->whenLoaded('client')),
            'created_at'      => $this->created_at?->toDateTimeString(),
        ];
    }
}
