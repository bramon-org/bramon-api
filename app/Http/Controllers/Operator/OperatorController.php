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
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $query = md5($request->getQueryString() || '');

        $operators = Cache::remember('operators_' . $query, self::DEFAULT_CACHE_TIME, function() use ($request) {
            return EloquentBuilder
                ::to(User::class, $request->get('filter'))
                ->where('active', true)
                ->where('visible', true)
                ->paginate($request->get('limit', static::DEFAULT_PAGINATION_SIZE));
        });

        return response()->json($operators);
    }

    /**
     * Update an operator
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
            'city'          => 'required|string|max:255',
            'state'         => 'required|string|max:255',
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
