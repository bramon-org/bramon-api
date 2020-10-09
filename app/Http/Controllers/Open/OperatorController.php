<?php

namespace App\Http\Controllers\Open;

use App\Http\Controllers\Controller;
use App\Models\User;
use EloquentBuilder;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Cache;

/**
 * Class OperatorController
 * @package App\Http\Controllers\Open
 */
class OperatorController extends Controller
{
    const DEFAULT_CACHE_TIME = 900;

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
     * List all operators
     *
     * @OA\Get(
     *     path="/v1/public/operators",
     *     operationId="/v1/public/operators",
     *     tags={"get-public-operators"},
     *     @OA\Response(
     *         response="200",
     *         description="List all operators",
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
        $operators = EloquentBuilder
                ::to(User::class, $request->get('filter'))
                ->where('active', true)
                ->where('visible', true)
                ->paginate($request->get('limit', static::DEFAULT_PAGINATION_SIZE));

        return response()->json($operators);
    }

    /**
     * View an operator
     *
     * @OA\Get(
     *     path="/v1/public/operators/{operator}",
     *     operationId="/v1/public/operators/000-000-0000",
     *     tags={"get-public-operators-single"},
     *     @OA\Parameter(
     *         name="operator",
     *         in="path",
     *         description="The operator identifier",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response="200",
     *         description="Returns the operator details.",
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
            $station = Cache::remember('operator_' . $id, self::DEFAULT_CACHE_TIME, function() use($id) {
                return User
                    ::where('id', $id)
                    ->where('visible', true)
                    ->where('active', true)
                    ->firstOrFail();
            });

            return response()->json(['station' => $station], 200);
        } catch (ModelNotFoundException $exception) {
            return response()->json(['error' => 'Operator not found'], 404);
        } catch (\Exception $exception) {
            return response()->json(['error' => $exception->getMessage()], 400);
        }
    }
}
