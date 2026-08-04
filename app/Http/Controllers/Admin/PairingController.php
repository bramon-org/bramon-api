<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\PairingService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class PairingController extends Controller
{
    /**
     * List probable pairings
     *
     * @OA\Get(
     *     path="/v1/admin/pairings",
     *     operationId="/v1/admin/pairings",
     *     tags={"Administrators"},
     *     @OA\Parameter(
     *         name="captured_date",
     *         in="query",
     *         description="Date (YYYY-MM-DD) to filter captures",
     *         required=false,
     *         @OA\Schema(type="string", format="date")
     *     ),
     *     @OA\Parameter(
     *         name="captured_from",
     *         in="query",
     *         description="Start datetime (ISO)",
     *         required=false,
     *         @OA\Schema(type="string", format="date-time")
     *     ),
     *     @OA\Parameter(
     *         name="captured_to",
     *         in="query",
     *         description="End datetime (ISO)",
     *         required=false,
     *         @OA\Schema(type="string", format="date-time")
     *     ),
     *     @OA\Response(
     *         response="200",
     *         description="List pairings",
     *         @OA\JsonContent()
     *     ),
     * )
     */
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
