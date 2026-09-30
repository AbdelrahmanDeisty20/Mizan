<?php

namespace App\Services;

use App\Http\Resources\CourtResource;
use App\Models\Court;

class CourtService
{
    public function index()
    {
        $courts = Court::orderBy('name')->get();

        return response()->json([
            'status'  => true,
            'message' => __('messages.success'),
            'data'    => CourtResource::collection($courts),
        ], 200);
    }
}
