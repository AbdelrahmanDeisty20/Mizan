<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ChatbotMessageResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'               => $this->id,
            'user_id'          => $this->user_id,
            'office_id'        => $this->office_id,
            'session_id'       => $this->session_id,
            'prompt'           => $this->prompt,
            'reply'            => $this->reply,
            'referenced_cases' => $this->referenced_cases ?? [],
            'created_at'       => $this->created_at?->toDateTimeString(),
        ];
    }
}
