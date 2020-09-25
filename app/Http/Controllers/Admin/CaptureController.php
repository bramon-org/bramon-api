<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Shared\UploadApi;
use App\Models\Capture;
use App\Models\File;
use EloquentBuilder;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Validation\ValidationException;

/**
 * Class CaptureController
 * @package App\Http\Controllers\Admin
 */
class CaptureController extends Controller
{
    use UploadApi;

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
     * List all captures
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $captures = EloquentBuilder
                ::to(Capture::class, $request->get('filter'))
                ->paginate($request->get('limit', static::DEFAULT_PAGINATION_SIZE));

        return response()->json($captures);
    }

    /**
     * Upload files and create a capture register
     *
     * @param Request $request
     * @return JsonResponse
     * @throws ValidationException
     */
    public function create(Request $request): JsonResponse
    {
        $this->validate($request, [
            'user_id'    => 'required|uuid|exists:users,id',
            'station_id'    => 'required|uuid|exists:stations,id',
            'files'         => 'required|array',
        ]);

        $capturesRegistered = $this->createCaptures($request);

        $captures = Capture::whereIn('id', $capturesRegistered)->get();

        return response()->json(['captures' => $captures], 201);
    }

    /**
     * Exclude captures files
     *
     * @param Request $request
     * @return JsonResponse
     * @throws ValidationException
     */
    public function exclude(Request $request): JsonResponse
    {
        $this->validate($request, [
            'station_id'    => 'required|uuid|exists:stations,id',
            'files'         => 'required|array|between:1,20',
        ]);

        $files = $request->get('files');
        $filesFromCaptures = File::whereIn('filename', $files)->get();

        foreach ($filesFromCaptures as $file) {
            $file->delete();

            $capture = $file->capture;

            if ($capture) {
                $capture->delete();
            }
        }

        return response()->json([], 204);
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
            $station = Cache::remember(('admin_capture_' . $id), self::DEFAULT_CACHE_TIME, function() use ($id) {
                return Capture::where('id', $id)->firstOrFail();
            });

            return response()->json(['capture' => $station], 200);
        } catch (ModelNotFoundException $exception) {
            return response()->json(['error' => 'Capture not found'], 404);
        } catch (\Exception $exception) {
            return response()->json(['error' => $exception->getMessage()], 400);
        }
    }
}
