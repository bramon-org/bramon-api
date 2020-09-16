<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Shared\UploadApi;
use App\Models\Capture;
use App\Models\File;
use EloquentBuilder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class CaptureController extends Controller
{
    use UploadApi;

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
     * List all captures
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $captures = EloquentBuilder
            ::to(Capture::class, $request->get('filter'))
            ->paginate($request->get('limit', 15));

        return response()->json($captures);
    }

    /**
     * Upload files and create a capture register
     *
     * @param Request $request
     * @return JsonResponse
     * @throws ValidationException
     */
    public function create(Request $request): JsonResponse
    {
        $this->validate($request, [
            'user_id'    => 'required|uuid|exists:users,id',
            'station_id'    => 'required|uuid|exists:stations,id',
            'files'         => 'required|array|between:1,20',
        ]);

        $capturesRegistered = $this->createCaptures($request);

        $captures = Capture
            ::whereIn('id', $capturesRegistered)
            ->paginate();

        return response()->json(['capture' => $captures], 201);
    }

    /**
     * Exclude captures files
     *
     * @param Request $request
     * @return JsonResponse
     * @throws ValidationException
     */
    public function exclude(Request $request): JsonResponse
    {
        $this->validate($request, [
            'station_id'    => 'required|uuid|exists:stations,id',
            'files'         => 'required|array|between:1,20',
        ]);

        File
            ::where('station_id', $request->get('station_id'))
            ->whereIn('filename', $request->get('files'))
            ->delete();

        return response()->json([], 204);
    }
}
