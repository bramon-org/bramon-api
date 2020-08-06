<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Station;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class StationController extends Controller
{
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
        $stations = Station::where('active', true)->paginate(15);

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
            $station = Station::where('id', $id)->firstOrFail();

            return response()->json(['station' => $station], 200);
        } catch (ModelNotFoundException $exception) {
            return response()->json(['error' => 'Station not found'], 404);
        } catch (\Exception $exception) {
            return response()->json(['error' => $exception->getMessage()], 400);
        }
    }

    /**
     * List the stations from an user
     *
     * @param Request $request
     * @param string $id
     * @return JsonResponse
     * @throws ValidationException
     */
    public function fromUser(Request $request, string $id): JsonResponse
    {
        $request['id'] = $id;

        $this->validate($request, ['id' => 'required|uuid|exists:users,id']);

        try {
            $operator = User::where('id', $id)->firstOrFail();
            $stations = $operator->stations()->paginate(15);

            return response()->json(['stations' => $stations], 200);
        } catch (ModelNotFoundException $exception) {
            return response()->json(['error' => 'Operator not found'], 404);
        } catch (\Exception $exception) {
            return response()->json(['error' => $exception->getMessage()], 400);
        }
    }
}
