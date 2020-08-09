<?php

namespace App\Http\Controllers\Operator;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Shared\UploadApi;
use App\Models\Capture;
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
            ->where('user_id', $request->user()->id)
            ->paginate();

        return response()->json($captures);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     * @throws ValidationException
     */
    public function create(Request $request): JsonResponse
    {
        $this->validate($request, [
            'station_id'    => 'required|uuid',
            'files'         => 'required|array|between:1,20',
        ]);

        $capturesRegistered = $this->createCaptures($request);

        $captures = Capture
            ::where('user_id', $request->user()->id)
            ->whereIn('id', $capturesRegistered)
            ->paginate();

        return response()->json(['capture' => $captures], 201);
    }
}
