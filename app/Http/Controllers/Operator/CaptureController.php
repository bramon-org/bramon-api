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
            'files'         => 'required',
        ]);

        $uploadedFiles = $request->file('files');
        $captureData = [];
        $captureDate = new DateTimeImmutable();

        if (!is_array($uploadedFiles)) {
            $uploadedFiles = [ $uploadedFiles ];
        }

        $capture = new Capture();
        $capture->station_id = $request->get('station_id');
        $capture->user_id = $request->user()->id;
        $capture->date = $captureDate;
        $capture->fill($captureData);
        $capture->save();

        foreach ($uploadedFiles as $file) {
            $captureFile = $this->sanitizeFile($file, $capture);

            $capture->date = $captureFile->date;

            if ($this->isAnalyzed($file)) {
                $captureData = $this->readCaptureData($captureFile);

                $capture->analyzed = sizeof($captureData) !== 0;
            }
        }

        $capture->save();
        $capture->refresh();

//        event(new FileUploadEvent($capture));

        return response()->json(['capture' => $capture], 201);
    }

    /**
     * Check if file is an analyze file.
     *
     * @param UploadedFile $file
     * @return bool
     */
    private function isAnalyzed(UploadedFile $file): bool
    {
        return preg_match("/A.XML$/i", $file->getClientOriginalName());
    }

    /**
     * @param UploadedFile $file
     * @return stdClass
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

    /**
     * @param stdClass $file
     * @return array
     */
    private function readCaptureData(File $file)
    {
        $inputFile = storage_path() . '/sync/' . $file->filename;
        $xml = simplexml_load_file($inputFile);
        $itemList = $xml->ua2_objects->ua2_object;

        $data = [];

        foreach ($itemList->attributes() as $attributeKey => $attributeValue) {
            $data[ (string) $attributeKey ] = (string) $attributeValue;
        };

        return $data;
    }
}
