<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ConsultationResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'                  => $this->id,
            'consultation_number' => $this->consultation_number,
            'consultation_method' => $this->consultation_method,
            'preferred_date'      => $this->preferred_date?->format('Y-m-d'),
            'preferred_time'      => $this->preferred_time,
            'subject'             => $this->subject,
            'status'              => $this->status,
            'reply'               => $this->reply,
            'fee'                 => $this->fee ? (float) $this->fee : null,
            'is_paid'             => (bool) $this->is_paid,
            'client'              => new ClientResource($this->whenLoaded('client')),
            'office'              => new OfficeResource($this->whenLoaded('office')),
            'user'                => new UserResource($this->whenLoaded('user')),
            'created_at'          => $this->created_at?->toDateTimeString(),
        ];
    }
}
