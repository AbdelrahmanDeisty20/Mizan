<?php

namespace App\Services;

use App\Http\Resources\GovernorateResource;
use App\Models\Governorate;

class GovernorateService
{
    public function index()
    {
        $governorates = Governorate::all();

        return response()->json([
            'status'  => true,
            'message' => __('messages.success'),
            'data'    => GovernorateResource::collection($governorates),
        ], 200);
    }
}
