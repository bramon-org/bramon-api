<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class OperatorController extends Controller
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
     * List all operators
     *
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        $operators = User::paginate();

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
        $operator->last_request_ip = $request->ip();
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
        $operator->last_request_ip = $request->ip();
        $operator->save();

        return response()->json(['operator' => $operator], 204);
    }

    /**
     * Show an operator
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

        $operator = User::where('id', $id)->firstOrFail();

        return response()->json(['operator' => $operator], 200);
    }
}
