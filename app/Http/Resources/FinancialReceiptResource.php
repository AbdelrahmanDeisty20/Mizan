<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FinancialReceiptResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'             => $this->id,
            'receipt_number' => $this->receipt_number,
            'amount'         => (float) $this->amount,
            'payment_method' => $this->payment_method,
            'date'           => $this->date?->format('Y-m-d'),
            'notes'          => $this->notes,
            'client'         => new ClientResource($this->whenLoaded('client')),
            'legal_case'     => new LegalCaseResource($this->whenLoaded('legalCase')),
            'user'           => new UserResource($this->whenLoaded('user')),
            'created_at'     => $this->created_at?->toDateTimeString(),
        ];
    }
}
