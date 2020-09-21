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
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $query = md5($request->getQueryString() || '');

        $stations = Cache::remember('stations_' . $query, self::DEFAULT_CACHE_TIME, function() use($request) {
            return EloquentBuilder
                ::to(Station::class, $request->get('filter'))
                ->where('visible', true)
                ->where('active', true)
                ->paginate($request->get('limit', static::DEFAULT_PAGINATION_SIZE));
        });

        return response()->json($stations);
    }

    /**
     * View a station
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
