<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Station;
use EloquentBuilder;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Validation\ValidationException;

/**
 * Class StationController
 * @package App\Http\Controllers\Admin
 */
class StationController extends Controller
{
    const DEFAULT_CACHE_TIME = 60;

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
     * List all stations from the current user
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $stations = EloquentBuilder
                ::to(Station::class, $request->get('filter'))
                ->paginate($request->get('limit', static::DEFAULT_PAGINATION_SIZE));

        return response()->json($stations);
    }

    /**
     * Add a station
     *
     * @param Request $request
     * @return JsonResponse
     * @throws ValidationException
     */
    public function create(Request $request): JsonResponse
    {
        $this->validate($request, [
            'user_id'           => 'required|uuid|exists:users,id',
            'name'              => 'required|string|max:255',
            'latitude'          => 'required|numeric',
            'longitude'         => 'required|numeric',
            'azimuth'           => 'required|numeric',
            'elevation'         => 'required|numeric',
            'fov'               => 'required|numeric',
            'camera_model'      => 'required|string|max:255',
            'camera_lens'       => 'required|string|max:255',
            'camera_capture'    => 'required|string|max:255',
            'source'            => 'nullable|string|in:' . implode(',', Station::AVAILABLE_SOURCES),
        ]);

        try {
            $station = new Station();
            $station->fill($request->all());
            $station->user_id = $request->get('user_id');
            $station->active = true;
            $station->save();

            return response()->json(['station' => $station], 201);
        } catch (\Exception $exception) {
            return response()->json(['error' => $exception->getMessage()], 400);
        }
    }

    /**
     * Update a station
     *
     * @param Request $request
     * @param string $id
     * @return JsonResponse
     * @throws ValidationException
     */
    public function update(Request $request, string $id): JsonResponse
    {
        $request['id'] = $id;

        $this->validate($request, [
            'id'                => 'required|uuid',
            'name'              => 'required|string|max:255',
            'latitude'          => 'required|numeric',
            'longitude'         => 'required|numeric',
            'azimuth'           => 'required|numeric',
            'elevation'         => 'required|numeric',
            'fov'               => 'required|numeric',
            'camera_model'      => 'required|string|max:255',
            'camera_lens'       => 'required|string|max:255',
            'camera_capture'    => 'required|string|max:255',
            'active'            => 'required|boolean',
            'source'            => 'nullable|string|in:' . implode(',', Station::AVAILABLE_SOURCES),
        ]);

        try {
            $station = Station::where('id', $id)->firstOrFail();
            $station->fill($request->all());
            $station->save();

            return response()->json(['station' => $station], 204);
        } catch (ModelNotFoundException $exception) {
            return response()->json(['error' => 'Station not found'], 404);
        } catch (\Exception $exception) {
            return response()->json(['error' => $exception->getMessage()], 400);
        }
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
