<?php

namespace App\Http\Controllers\Open;

use App\Http\Controllers\Controller;
use App\Models\Capture;
use EloquentBuilder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

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
}
