<?php

namespace App\Http\Controllers\Shared;

use App\Drivers\SourceDriverInterface;
use App\Events\FileUploadEvent;
use App\Models\Capture;
use App\Models\File;
use App\Models\Station;
use DateTimeImmutable;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\File\UploadedFile;

trait UploadApi
{

    /**
     * Get all files from request and save into database classified by capture date.
     *
     * @param Request $request
     * @return array
     */
    private function createCaptures(Request $request): array
    {
        $filesFromRequest   = $request->file('files');
        $station            = Station::find($request->get('station_id'));
        $uploadedFiles      = $this->organizeUploads($filesFromRequest, $station);
        $capturesRegistered = [];

        foreach ($uploadedFiles as $captureDate => $captureFiles) {
            $capture = Capture::firstOrCreate(
                [
                    'station_id' => $request->get('station_id'),
                    'user_id' => $request->get('user_id'),
                    'captured_at' => DateTimeImmutable::createFromFormat('Ymd_His', $captureDate),
                ]
            );

            $this->storeUploadedFiles($station, $capture, $captureFiles);

            $capturesRegistered[] = $capture->id;

            event(new FileUploadEvent($capture->id));
        }

        return $capturesRegistered;
    }

    /**
     * Get all files uploaded, organize and group by date of capture.
     *
     * @param array $files
     * @param Station $station
     * @return array
     */
    private function organizeUploads(array $files, Station $station): array
    {
        $captures = [];
        $driver = $this->driver($station);

        foreach ($files as $file) {
            $fileDate = $driver::getFileDate($file->getClientOriginalName());

            $captures[ $fileDate->format('Ymd_His') ][] = $file;
        }

        return $captures;
    }

    /**
     * Get the station source driver
     *
     * @param Station $station
     * @return string
     */
    private function driver(Station $station): string
    {
        return '\\App\\Drivers\\' . Str::camel($station->source) . 'Driver';
    }

    /**
     * Organize and store uploaded files of capture.
     *
     * @param Station $station
     * @param Capture $capture
     * @param array $files
     * @return bool
     */
    private function storeUploadedFiles(Station $station, Capture $capture, array $files = []): bool
    {
        foreach ($files as $file) {
            $this->sanitizeFile($station, $capture, $file);
            $this->readAnalyzeData($file, $capture);

            $file->move(storage_path() . '/sync', $file->getClientOriginalName());
        }

        return true;
    }

    /**
     * Sanitize file uploaded and get the most important information.
     *
     * @param Station $station
     * @param Capture $capture
     * @param UploadedFile $file
     * @return File
     */
    private function sanitizeFile(Station $station, Capture $capture, UploadedFile $file): File
    {
        $driver = $this->driver($station);

        $originalName = $file->getClientOriginalName();
        $originalExtension = $file->getClientOriginalExtension();
        $originalDateTime = $driver::getFileDate($file->getClientOriginalName());
        $fileType = $file->getMimeType();

        return File::firstOrCreate([
            'capture_id' => $capture->id,
            'user_id' => $capture->user_id,
            'station_id' => $station->id,
            'filename' => $originalName,
            'url' => $originalName,
            'type' => $fileType,
            'extension' => $originalExtension,
            'captured_at' => $originalDateTime,
        ]);
    }

    /**
     * Check if file is an analyze file.
     *
     * @param UploadedFile $file
     * @return bool
     */
    private function isAnalyzed(UploadedFile $file): bool
    {
        return preg_match("/A.XML$/i", $file->getClientOriginalName())
            && file_exists($file->getRealPath());
    }

    /**
     * Read the analyze file and fill the file with the details.
     *
     * @param UploadedFile $file
     * @return array
     */
    private function readCaptureData(UploadedFile $file)
    {
        try {
            $inputFile = $file->getRealPath();
            $xml = simplexml_load_file($inputFile);
            $itemList = $xml->ua2_objects->ua2_object;

            $data = [];

            foreach ($itemList->attributes() as $attributeKey => $attributeValue) {
                $data[ (string) $attributeKey ] = (string) $attributeValue;
            };

            return $data;
        } catch (\ErrorException $errorException) {
            return [];
        }
    }

    /**
     * @param UploadedFile $file
     * @param Capture $capture
     * @return Capture|null
     */
    private function readAnalyzeData(UploadedFile $file, Capture $capture): ?Capture
    {
        if (!$this->isAnalyzed($file)) {
            return null;
        }

        $captureData = $this->readCaptureData($file);

        $capture->fill($captureData);
        $capture->save();

        return $capture;
    }
}
