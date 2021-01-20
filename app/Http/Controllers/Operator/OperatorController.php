<?php

namespace App\Http\Controllers\Operator;

use App\Http\Controllers\Controller;
use App\Models\User;
use EloquentBuilder;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Validation\ValidationException;

/**
 * Class OperatorController
 * @package App\Http\Controllers\Operator
 */
class OperatorController extends Controller
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
     * List all operators
     *
     * @OA\Get(
     *     path="/v1/operator/operators",
     *     operationId="/v1/operator/operators",
     *     tags={"Operators"},
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
                ->orderBy('name')
                ->paginate($request->get('limit', static::DEFAULT_PAGINATION_SIZE));

        return response()->json($operators);
    }

    /**
     * Update an operator
     *
     * @OA\Put(
     *     path="/v1/operator/operators/{operator}",
     *     operationId="/v1/operator/operators/000-0000-00000",
     *     tags={"Operators"},
     *     @OA\Parameter(
     *         name="operator",
     *         in="path",
     *         description="The operator identifier",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="name",
     *         in="path",
     *         description="The operator name",
     *         required=true,
     *         @OA\Schema(type="string", maxLength=255)
     *     ),
     *     @OA\Parameter(
     *         name="email",
     *         in="path",
     *         description="The operator email",
     *         required=true,
     *         @OA\Schema(type="string", maxLength=255)
     *     ),
     *     @OA\Parameter(
     *         name="city",
     *         in="path",
     *         description="The operator city",
     *         required=true,
     *         @OA\Schema(type="string", maxLength=255)
     *     ),
     *     @OA\Parameter(
     *         name="state",
     *         in="path",
     *         description="The operator state",
     *         required=true,
     *         @OA\Schema(type="string", maxLength=255)
     *     ),
     *     @OA\Parameter(
     *         name="mobile_phone",
     *         in="path",
     *         description="The operator mobile phone",
     *         required=true,
     *         @OA\Schema(type="string", maxLength=100)
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
    public function update(Request $request): JsonResponse
    {
        $operator = $request->user();

        $this->validate($request, [
            'name'          => 'required|string|max:255',
            'mobile_phone'  => 'required|max:50',
        ]);

        try {
            $operator->name = $request->get('name') ?? $operator->name;
            $operator->mobile_phone = $request->get('mobile_phone') ?? $operator->mobile_phone;
            $operator->save();

            return response()->json(['operator' => $operator], 204);
        } catch (\Exception $exception) {
            return response()->json(['error' => $exception->getMessage()], 400);
        }
    }

    /**
     * View an operator
     *
     * @OA\Get(
     *     path="/v1/operator/operators/{operator}",
     *     operationId="/v1/operator/operators/000-000-0000",
     *     tags={"Operators"},
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
