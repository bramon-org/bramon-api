<?php

namespace App\Http\Controllers\Open;

use App\Http\Controllers\Controller;
use App\Models\Capture;
use EloquentBuilder;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class CaptureController extends Controller
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
     * List all captures
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $captures = EloquentBuilder
            ::to(Capture::class, $request->get('filter'))
            ->where('class', '!=', '')
            ->paginate();

        return response()->json($captures);
    }

    /**
     * View a capture
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
            $station = Capture::where('id', $id)->whereNotNull('class')->firstOrFail();

            return response()->json(['capture' => $station], 200);
        } catch (ModelNotFoundException $exception) {
            return response()->json(['error' => 'Capture not found'], 404);
        } catch (\Exception $exception) {
            return response()->json(['error' => $exception->getMessage()], 400);
        }
    }
}