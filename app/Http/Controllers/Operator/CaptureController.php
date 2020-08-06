<?php

namespace App\Http\Controllers\Operator;

use App\Events\FileUploadEvent;
use App\Http\Controllers\Controller;
use App\Models\Capture;
use App\Models\File;
use DateTimeImmutable;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use stdClass;
use Symfony\Component\HttpFoundation\File\UploadedFile;

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
     * List all operators
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $captures = Capture::where('user_id', $request->user()->id)->paginate(15);

        return response()->json($captures);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     * @throws ValidationException
     */
    public function create(Request $request): JsonResponse
    {
        $this->validate($request, [
            'station_id'    => 'required|uuid|exists:stations,id',
            'files'         => 'required|array',
        ]);

        $uploadedFiles = $this->organizeUploads($request->file('files'));
        $capturesRegistered = [];

        foreach ($uploadedFiles as $captureDate => $captureFiles) {
            $capture = new Capture();
            $capture->station_id = $request->get('station_id');
            $capture->user_id = $request->user()->id;
            $capture->save();

            foreach ($captureFiles as $file) {
                $captureFile = $this->sanitizeFile($file, $capture);
                $capture->date = $captureFile->date;
                $capture->save();
            }

            $capturesRegistered[] = $capture->id;

            event(new FileUploadEvent($capture->id));
        }

        $captures = Capture::where('user_id', $request->user()->id)
            ->whereIn('id', $capturesRegistered)
            ->paginate(15);

        return response()->json(['capture' => $captures], 201);
    }

    /**
     * @param array $files
     * @return array
     */
    private function organizeUploads(array $files = []): array
    {
        $captures = [];

        foreach ($files as $file) {
            $fileDate = $this->getFileDate($file->getClientOriginalName());

            $captures[ $fileDate->format('Ymd_His') ][] = $file;
        }

        return $captures;
    }

    /**
     * @param UploadedFile $file
     * @param Capture $capture
     * @return File
     */
    private function sanitizeFile(UploadedFile $file, Capture $capture): File
    {
        $originalName = $file->getClientOriginalName();
        $originalExtension = $file->getClientOriginalExtension();
        $originalDateTime = $this->getFileDate($originalName);
        $fileType = $file->getMimeType();

        $file->move(storage_path() . '/sync', $originalName);

        $captureFile = new File();
        $captureFile->filename = $originalName;
        $captureFile->url = $originalName;
        $captureFile->type = $fileType;
        $captureFile->extension = $originalExtension;
        $captureFile->date = $originalDateTime;
        $captureFile->capture_id = $capture->id;
        $captureFile->save();

        return $captureFile;
    }

    /**
     * @param string $filename
     * @return DateTimeImmutable
     */
    private function getFileDate(string $filename): DateTimeImmutable
    {
        $fileExploded = explode('_', $filename);
        $fileDate = substr($fileExploded[0],1);
        $fileTime = $fileExploded[1];
        $fileDateTime = $fileDate . '_' . $fileTime;

        return DateTimeImmutable::createFromFormat('Ymd_His', $fileDateTime);
    }
}
