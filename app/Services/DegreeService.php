<?php

namespace App\Services;

use App\Http\Resources\DegreeResource;
use App\Models\Degree;

class DegreeService
{
    public function index()
    {
        $degrees = Degree::all();

        return response()->json([
            'status'  => true,
            'message' => __('messages.success'),
            'data'    => DegreeResource::collection($degrees),
        ], 200);
    }
}
