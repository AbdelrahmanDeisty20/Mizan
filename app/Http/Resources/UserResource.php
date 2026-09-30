<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
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
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
            'role' => $this->role,
            'avatar' => $this->avatar_url,
            'syndicate_card_image' => $this->syndicate_card_image_url,
            'office_details' => new OfficeResource($this->whenLoaded('office')),
            'created_at' => $this->created_at?->toDateTimeString(),
        ];

    }
}
