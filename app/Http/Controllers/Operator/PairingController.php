<?php

namespace App\Http\Controllers\Operator;

use App\Http\Controllers\Controller;
use App\Services\PairingService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class PairingController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $this->validate($request, [
            'captured_date' => 'sometimes|date_format:Y-m-d',
            'captured_from' => 'sometimes|date',
            'captured_to' => 'sometimes|date',
            'max_distance_km' => 'sometimes|numeric',
            'time_window_seconds' => 'sometimes|integer',
        ]);

        $service = new PairingService();

        $result = $service->findPairings($request->all());

        return response()->json($result, 200);
    }
}
