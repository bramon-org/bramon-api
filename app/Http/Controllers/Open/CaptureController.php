<?php

namespace App\Http\Controllers\Open;

use App\Http\Controllers\Controller;
use App\Models\Capture;
use EloquentBuilder;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Cache;

/**
 * Class CaptureController
 * @package App\Http\Controllers\Open
 */
class CaptureController extends Controller
{
    const DEFAULT_CACHE_TIME = 120;

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
     * @OA\Get(
     *     path="/v1/public/captures",
     *     operationId="/v1/public/captures",
     *     tags={"get-public-captures"},
     *     @OA\Response(
     *         response="200",
     *         description="List all captures",
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
        $captures = EloquentBuilder
                ::to(Capture::class, $request->get('filter'))
                ->paginate($request->get('limit', static::DEFAULT_PAGINATION_SIZE));

        return response()->json($captures);
    }

    /**
     * View a capture
     *
     * @OA\Get(
     *     path="/v1/public/captures/{capture}",
     *     operationId="/v1/public/captures/000-000-0000",
     *     tags={"get-public-captures-single"},
     *     @OA\Parameter(
     *         name="capture",
     *         in="path",
     *         description="The capture identifier",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response="200",
     *         description="Returns the capture details.",
     *         @OA\JsonContent()
     *     ),
     *     @OA\Response(
     *         response="404",
     *         description="Error: Not Found. When capture not exists or not public.",
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
            $station = Cache::remember(('open_capture_' . $id), self::DEFAULT_CACHE_TIME, function() use ($id) {
                return Capture::where('id', $id)->firstOrFail();
            });

            return response()->json(['capture' => $station], 200);
        } catch (ModelNotFoundException $exception) {
            return response()->json(['error' => 'Capture not found'], 404);
        } catch (\Exception $exception) {
            return response()->json(['error' => $exception->getMessage()], 400);
        }
    }
}
