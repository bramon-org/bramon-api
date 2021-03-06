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
     * List all stations
     *
     * @OA\Get(
     *     path="/v1/admin/stations",
     *     operationId="/v1/admin/stations",
     *     tags={"Administrators"},
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
                ->orderBy('name')
                ->paginate($request->get('limit', static::DEFAULT_PAGINATION_SIZE));

        return response()->json($stations);
    }

    /**
     * Add a station
     *
     * @OA\Post(
     *     path="/v1/admin/stations",
     *     operationId="/v1/admin/stations",
     *     tags={"Administrators"},
     *     @OA\Parameter(
     *         name="user_id",
     *         in="path",
     *         description="The operator identifier",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="name",
     *         in="path",
     *         description="The station name",
     *         required=true,
     *         @OA\Schema(type="string", maxLength=255)
     *     ),
     *     @OA\Parameter(
     *         name="latitude",
     *         in="path",
     *         description="The station latitude",
     *         required=true,
     *         @OA\Schema(type="string", maxLength=255)
     *     ),
     *     @OA\Parameter(
     *         name="longitude",
     *         in="path",
     *         description="The station longitude",
     *         required=true,
     *         @OA\Schema(type="string", maxLength=255)
     *     ),
     *     @OA\Parameter(
     *         name="azimuth",
     *         in="path",
     *         description="The station azimuth",
     *         required=true,
     *         @OA\Schema(type="string", maxLength=255)
     *     ),
     *     @OA\Parameter(
     *         name="elevation",
     *         in="path",
     *         description="The station elevation",
     *         required=true,
     *         @OA\Schema(type="string", maxLength=255)
     *     ),
     *     @OA\Parameter(
     *         name="fov",
     *         in="path",
     *         description="The station FOV",
     *         required=true,
     *         @OA\Schema(type="string", maxLength=255)
     *     ),
     *     @OA\Parameter(
     *         name="camera_model",
     *         in="path",
     *         description="The station camera model",
     *         required=true,
     *         @OA\Schema(type="string", maxLength=255)
     *     ),
     *     @OA\Parameter(
     *         name="camera_lens",
     *         in="path",
     *         description="The station camera lens",
     *         required=true,
     *         @OA\Schema(type="string", maxLength=255)
     *     ),
     *     @OA\Parameter(
     *         name="camera_capture",
     *         in="path",
     *         description="The station camera capture",
     *         required=true,
     *         @OA\Schema(type="string", maxLength=255)
     *     ),
     *     @OA\Parameter(
     *         name="source",
     *         in="path",
     *         description="The station capture source",
     *         required=true,
     *         @OA\Schema(type="string", enum={"UFO","RMS"})
     *     ),
     *     @OA\Response(
     *         response="201",
     *         description="Returns the capture data.",
     *         @OA\JsonContent()
     *     ),
     *     @OA\Response(
     *         response="404",
     *         description="Error: Not Found. When capture not exists or not public.",
     *     ),
     *     @OA\Response(
     *         response="422",
     *         description="Error: Bad Param. When file not found or can not be deleted.",
     *     ),
     * )
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
     * @OA\Post(
     *     path="/v1/admin/stations/{station}",
     *     operationId="/v1/admin/stations/000-00000-00000",
     *     tags={"Administrators"},
     *     @OA\Parameter(
     *         name="station",
     *         in="path",
     *         description="The station identifier",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="name",
     *         in="path",
     *         description="The station name",
     *         required=true,
     *         @OA\Schema(type="string", maxLength=255)
     *     ),
     *     @OA\Parameter(
     *         name="latitude",
     *         in="path",
     *         description="The station latitude",
     *         required=true,
     *         @OA\Schema(type="string", maxLength=255)
     *     ),
     *     @OA\Parameter(
     *         name="longitude",
     *         in="path",
     *         description="The station longitude",
     *         required=true,
     *         @OA\Schema(type="string", maxLength=255)
     *     ),
     *     @OA\Parameter(
     *         name="azimuth",
     *         in="path",
     *         description="The station azimuth",
     *         required=true,
     *         @OA\Schema(type="string", maxLength=255)
     *     ),
     *     @OA\Parameter(
     *         name="elevation",
     *         in="path",
     *         description="The station elevation",
     *         required=true,
     *         @OA\Schema(type="string", maxLength=255)
     *     ),
     *     @OA\Parameter(
     *         name="fov",
     *         in="path",
     *         description="The station FOV",
     *         required=true,
     *         @OA\Schema(type="string", maxLength=255)
     *     ),
     *     @OA\Parameter(
     *         name="camera_model",
     *         in="path",
     *         description="The station camera model",
     *         required=true,
     *         @OA\Schema(type="string", maxLength=255)
     *     ),
     *     @OA\Parameter(
     *         name="camera_lens",
     *         in="path",
     *         description="The station camera lens",
     *         required=true,
     *         @OA\Schema(type="string", maxLength=255)
     *     ),
     *     @OA\Parameter(
     *         name="camera_capture",
     *         in="path",
     *         description="The station camera capture",
     *         required=true,
     *         @OA\Schema(type="string", maxLength=255)
     *     ),
     *     @OA\Parameter(
     *         name="source",
     *         in="path",
     *         description="The station capture source",
     *         required=true,
     *         @OA\Schema(type="string", enum={"UFO","RMS"})
     *     ),
     *     @OA\Response(
     *         response="201",
     *         description="Returns the capture data.",
     *         @OA\JsonContent()
     *     ),
     *     @OA\Response(
     *         response="404",
     *         description="Error: Not Found. When capture not exists or not public.",
     *     ),
     *     @OA\Response(
     *         response="422",
     *         description="Error: Bad Param. When file not found or can not be deleted.",
     *     ),
     * )
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
     * @OA\Get(
     *     path="/v1/admin/stations/{station}",
     *     operationId="/v1/admin/stations/000-000-0000",
     *     tags={"Administrators"},
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
