<?php

namespace App\Http\Controllers\Shared;

use App\Events\FileUploadEvent;
use App\Models\Capture;
use App\Models\File;
use DateTimeImmutable;
use Illuminate\Http\Request;
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
        $uploadedFiles      = $this->organizeUploads($filesFromRequest);
        $capturesRegistered = [];

        foreach ($uploadedFiles as $captureDate => $captureFiles) {
            $capture = new Capture();
            $capture->station_id = $request->get('station_id');
            $capture->user_id = $request->user()->id;
            $capture->captured_at = $captureDate;
            $capture->save();

            $this->storeUploadedFiles($capture, $captureFiles);

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
     * Organize and store uploaded files of capture.
     *
     * @param Capture $capture
     * @param array $files
     * @return bool
     */
    private function storeUploadedFiles(Capture $capture, array $files = []): bool
    {
        foreach ($files as $file) {
            $this->sanitizeFile($file, $capture);
            $this->readAnalyzeData($file, $capture);

            $file->move(storage_path() . '/sync', $file->getClientOriginalName());
        }

        return true;
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
