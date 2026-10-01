<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class HearingResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'                 => $this->id,
            'legal_case_id'      => $this->legal_case_id,
            'legal_case'         => new LegalCaseResource($this->whenLoaded('legalCase')),
            'hearing_date'       => $this->hearing_date?->toDateTimeString(),
            'hearing_type'       => $this->hearing_type,
            'court_room'         => $this->court_room,
            'roll_number'        => $this->roll_number,
            'decision'           => $this->decision,
            'requirements'       => $this->requirements,
            'status'             => $this->status,
            'created_at'         => $this->created_at?->toDateTimeString(),
        ];
    }
}
