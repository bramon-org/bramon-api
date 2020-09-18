<?php

namespace App\Http\Controllers\Operator;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

/**
 * Class OperatorController
 * @package App\Http\Controllers\Operator
 */
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
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $operator = $request->user();

        return response()->json(['operator' => $operator]);
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
}
