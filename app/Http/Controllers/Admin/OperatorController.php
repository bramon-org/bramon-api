<?php

namespace App\Http\Controllers\Admin;

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
 * @package App\Http\Controllers\Admin
 */
class OperatorController extends Controller
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
     * List all operators
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $operators = EloquentBuilder
                ::to(User::class, $request->get('filter'))
                ->paginate($request->get('limit', static::DEFAULT_PAGINATION_SIZE));

        return response()->json($operators);
    }

    /**
     * Add an operator
     *
     * @param Request $request
     * @return JsonResponse
     * @throws ValidationException
     */
    public function create(Request $request): JsonResponse
    {
        $this->validate($request, [
            'name'          => 'required|string|max:255',
            'email'         => 'required|string|email|max:255',
            'city'          => 'required|string|max:255',
            'state'         => 'required|string|max:255',
            'mobile_phone'  => 'required|string|max:100',
            'role'          => 'required|in:' . implode(',', User::AVAILABLE_ROLES),
        ]);

        $operator = new User;
        $operator->fill($request->all());
        $operator->email = $request->get('email');
        $operator->password = $operator->generatePassword();
        $operator->api_token = $operator->generateApiToken();
        $operator->save();

        return response()->json(['operator' => $operator], 201);
    }

    /**
     * Update an operator
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
            'id'            => 'required|uuid',
            'name'          => 'required|string|max:255',
            'city'         => 'required|string|max:255',
            'state'         => 'required|string|max:255',
            'mobile_phone'  => 'required|max:50',
            'role'          => 'required|in:' . implode(',', User::AVAILABLE_ROLES),
        ]);

        $operator = User::where('id', $id)->firstOrFail();
        $operator->fill($request->all());
        $operator->save();

        return response()->json(['operator' => $operator], 204);
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
