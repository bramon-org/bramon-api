<?php

namespace App\Http\Controllers\Operator;

use App\Http\Controllers\Controller;
use App\Models\Pairing;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class PairingController extends Controller
{
    public function precomputed(Request $request): JsonResponse
    {
        $this->validate($request, [
            'pairing_date' => 'sometimes|date_format:Y-m-d',
            'limit' => 'sometimes|integer',
            'page' => 'sometimes|integer',
        ]);

        $date = $request->get('pairing_date', Carbon::now()->format('Y-m-d'));
        $limit = $request->get('limit', 50);
        $page = $request->get('page', 1);

        $query = Pairing::with(['captureA.station', 'captureB.station'])->where('pairing_date', $date)->orderBy('distance_km');

        $total = $query->count();
        $data = $query->skip(($page - 1) * $limit)->take($limit)->get();

        return response()->json([
            'total' => $total,
            'per_page' => (int)$limit,
            'page' => (int)$page,
            'data' => $data,
        ], 200);
    }
}
