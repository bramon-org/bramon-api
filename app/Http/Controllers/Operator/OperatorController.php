<?php

namespace App\Http\Controllers\Operator;

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
        $operators = User::paginate(15);

        return response()->json($operators);
    }

    /**
     * Update an operator
     *
     * @param Request $request
     * @param string $id
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
            $operator->last_request_ip = $request->ip();
            $operator->save();

            return response()->json(['operator' => $operator], 204);
        } catch (\Exception $exception) {
            return response()->json(['error' => $exception->getMessage()], 400);
        }
    }
}