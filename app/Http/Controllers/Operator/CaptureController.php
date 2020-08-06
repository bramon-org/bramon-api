<?php

namespace App\Http\Controllers\Operator;

use App\Events\FileUploadEvent;
use App\Http\Controllers\Controller;
use App\Models\Capture;
use App\Models\File;
use DateTimeImmutable;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
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

        $capturesRegistered = $this->createCaptures($request);

        $captures = Capture
            ::where('user_id', $request->user()->id)
            ->whereIn('id', $capturesRegistered)
            ->paginate(15);

        return response()->json(['capture' => $captures], 201);
    }

    /**
     * Get all files from request and save into database classified by capture date.
     *
     * @param Request $request
     * @return array
     */
    private function createCaptures(Request $request): array
    {
        $filesFromRequest   = $request->file('files');
        $uploadedFiles      = $this->organizeUploads($filesFromRequest);
        $capturesRegistered = [];

        foreach ($uploadedFiles as $captureDate => $captureFiles) {
            $capture = new Capture();
            $capture->station_id = $request->get('station_id');
            $capture->user_id = $request->user()->id;
            $capture->captured_at = new DateTimeImmutable();
            $capture->save();

            foreach ($captureFiles as $file) {
                $captureFile = $this->sanitizeFile($file, $capture);

                $capture->captured_at = $captureFile->captured_at;
                $capture->save();
            }

            $capturesRegistered[] = $capture->id;

            event(new FileUploadEvent($capture->id));
        }

        return $capturesRegistered;
    }

    /**
     * Get all files uploaded, organize and group by date of capture.
     *
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
     * Sanitize file uploaded and get the most important information.
     *
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
        $captureFile->captured_at = $originalDateTime;
        $captureFile->capture_id = $capture->id;
        $captureFile->save();

        return $captureFile;
    }

    /**
     * Get date of the capture from the filename.
     *
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
