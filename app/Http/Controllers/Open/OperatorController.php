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
