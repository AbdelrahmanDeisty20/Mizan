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
            'legal_case'         => new LegalCaseResource($this->whenLoaded('legalCase')),
            'hearing_date'       => $this->hearing_date?->format('Y-m-d'),
            'hearing_time'       => $this->hearing_time ? substr($this->hearing_time, 0, 5) : null,
            'hearing_type'       => new HearingTypeResource($this->whenLoaded('hearingType')),
            'court_room'         => $this->court_room,
            'roll_number'        => $this->roll_number,
            'decision'           => $this->decision,
            'requirements'       => $this->requirements,
            'status'             => $this->status,
            'created_at'         => $this->created_at?->toDateTimeString(),
        ];
    }
}
