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
     * Get the station source driver
     *
     * @param Station $station
     * @return SourceDriverInterface
     */
    private function driver(Station $station): SourceDriverInterface
    {
        $driverClass = '\\App\\Drivers\\' . Str::camel($station->source) . 'Driver';

        return new $driverClass;
    }

    /**
     * @param Request $request
     * @return void
     */
    private function validateUploadFiles(Request $request): void
    {
        $station = Station::find($request->get('station_id'));

        switch ($station->source) {
            case Station::SOURCE_RMS:
                $this->validate($request, [
                    'files.*' => 'mimes:bz2',
                ]);
                break;

            case Station::SOURCE_UFO:
                $this->validate($request, [
                    'files.*' => 'mimes:avi,txt,xml,bmp,jpg,mp4',
                ]);
                break;

            default:
                abort(422, 'Station source not configured.');
        }
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
        $userId             = $request->get('user_id');
        $stationId          = $request->get('station_id');
        $station            = Station::find($stationId);
        $uploadedFiles      = $this->organizeUploads($filesFromRequest, $station);
        $capturesRegistered = [];

        foreach ($uploadedFiles as $captureDate => $captureFiles) {
            $captureHash = md5($stationId . $captureDate);

            $capture = Capture::firstOrNew([
                'station_id'    => $stationId,
                'capture_hash'  => $captureHash,
            ]);

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
            try {
                $fileDate = $driver->getFileDate($file->getClientOriginalName());

                $captures[ $fileDate->format('Ymd_His') ][] = $file;
            } catch (\InvalidArgumentException $invalidArgumentException) {
                continue;
            }
        }

        return $captures;
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
        $driver = $this->driver($station);

        foreach ($files as $file) {
            $this->sanitizeFile($station, $capture, $file);
            $driver->readAnalyzeData($file, $capture);

            $capture_path = sprintf(
                "%s/%s/%s",
                storage_path(),
                'captures',
                $this->captureStoragePath($capture, $station)
            );

            $file->move($capture_path, $file->getClientOriginalName());
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
        $originalName = $file->getClientOriginalName();
        $originalExtension = $file->getClientOriginalExtension();
        $originalDateTime = $this->driver($station)->getFileDate($file->getClientOriginalName());
        $fileType = $file->getMimeType();

        $capture->captured_at = $originalDateTime;
        $capture->save();

        $pathPrefix = $this->captureStoragePath($capture, $station);

        $hash = md5($capture->id . $originalName);

        $captureFile = File::firstOrNew([
            'file_hash'  => $hash,
            'capture_id' => $capture->id,
        ]);

        $captureFile->fill([
            'filename' => $originalName,
            'url' => "{$pathPrefix}/{$originalName}",
            'type' => $fileType,
            'extension' => $originalExtension,
            'captured_at' => $originalDateTime,
        ]);
        $captureFile->save();

        return $captureFile;
    }

    /**
     * @param Capture $capture
     * @param Station $station
     * @return string
     */
    private function captureStoragePath(Capture $capture, Station $station): string
    {
        /* @var $date DateTimeImmutable */
        $date = $capture->captured_at;

        return sprintf(
            '%s/%s/%s/%s',
            $station->name,
            $date->format('Y'),
            $date->format('Ym'),
            $date->format('Ymd'),
        );
    }
}
