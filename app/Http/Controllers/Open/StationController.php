<?php

namespace App\Http\Controllers\Open;

use App\Http\Controllers\Controller;
use App\Models\Station;
use EloquentBuilder;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Cache;

/**
 * Class StationController
 * @package App\Http\Controllers\Open
 */
class StationController extends Controller
{
    const DEFAULT_CACHE_TIME = 300;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * List all stations
     *
     * @OA\Get(
     *     path="/v1/public/stations",
     *     operationId="/v1/public/stations",
     *     tags={"get-public-stations"},
     *     @OA\Response(
     *         response="200",
     *         description="List all stations",
     *         @OA\JsonContent()
     *     ),
     *     @OA\Response(
     *         response="400",
     *         description="Error: Bad request. When required parameters were not supplied.",
     *     ),
     * )
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $stations = EloquentBuilder
                ::to(Station::class, $request->get('filter'))
                ->where('visible', true)
                ->where('active', true)
                ->orderBy('name')
                ->paginate($request->get('limit', static::DEFAULT_PAGINATION_SIZE));

        return response()->json($stations);
    }

    /**
     * View a station
     *
     * @OA\Get(
     *     path="/v1/public/stations/{station}",
     *     operationId="/v1/public/stations/000-000-0000",
     *     tags={"get-public-stations-single"},
     *     @OA\Parameter(
     *         name="station",
     *         in="path",
     *         description="The station identifier",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response="200",
     *         description="Returns the station details.",
     *         @OA\JsonContent()
     *     ),
     *     @OA\Response(
     *         response="404",
     *         description="Error: Not Found. When station not exists or not public.",
     *     ),
     * )
     *
     * @param Request $request
     * @param string $id
     * @return JsonResponse
     * @throws ValidationException
     */
    public function show(Request $request, string $id): JsonResponse
    {
        $request['id'] = $id;

        $this->validate($request, ['id' => 'required|uuid']);

        try {
            $station = Cache::remember('station_' . $id, self::DEFAULT_CACHE_TIME, function() use($id){
                return Station
                    ::where('id', $id)
                    ->where('visible', true)
                    ->where('active', true)
                    ->firstOrFail();
            });

            return response()->json(['station' => $station], 200);
        } catch (ModelNotFoundException $exception) {
            return response()->json(['error' => 'Station not found'], 404);
        } catch (\Exception $exception) {
            return response()->json(['error' => $exception->getMessage()], 400);
        }
    }
}
