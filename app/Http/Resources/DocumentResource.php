<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DocumentResource extends JsonResource
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
            'office_id'    => $this->office_id,
            'client_id'    => $this->client_id,
            'legal_case_id'=> $this->legal_case_id,
            'title'        => $this->title,
            'file_url'     => $this->file_url,
            'file_type'    => $this->file_type ?? 'PDF',
            'file_size'    => $this->file_size ?? '1.0 MB',
            'deposit_date' => $this->created_at?->format('d-m-Y'),
            'created_at'   => $this->created_at?->toDateTimeString(),
        ];
    }
}
