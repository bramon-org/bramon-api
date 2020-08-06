<?php

namespace App\Jobs;

use App\Events\FileUploadEvent;
use App\Models\Capture;
use App\Models\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class FileUploaderJob extends Job
{
    private FileUploadEvent $event;

    /**
     * Create a new job instance.
     *
     * @param FileUploadEvent $event
     */
    public function __construct(FileUploadEvent $event)
    {
        $this->event = $event;
    }

    /**
     * Execute the job.
     *
     * @return bool
     */
    public function handle()
    {
        Log::info('=== FileUploaderJob start ========');

        $event = $this->event;
        $capture = Capture::find($event->capture);
        $files = $capture->files;

        foreach ($files as $file) {
            $inputFile = storage_path() . '/sync/' . $file->filename;

            if (!file_exists($inputFile)) {
                Log::error('WARNING: File ' .  $inputFile . ' not exists.');

                continue;
            }

            if ($this->isAnalyzed($file)) {
                $captureData = $this->readCaptureData($file);

                $capture->fill($captureData);
                $capture->analyzed = sizeof($captureData) !== 0;
                $capture->save();
            }

            Storage::disk(config('filesystems.cloud'))
                ->put(
                    $file->filename,
                    fopen($inputFile, 'r')
                );

            unlink($inputFile);
        }

        Log::info('=== FileUploaderJob end ========');

        return true;
    }

    /**
     * Check if file is an analyze file.
     *
     * @param File $file
     * @return bool
     */
    private function isAnalyzed(File $file): bool
    {
        return preg_match("/A.XML$/i", $file->filename);
    }

    /**
     * Read the analyze file and fill the file with the details.
     *
     * @param File $file
     * @return array
     */
    private function readCaptureData(File $file)
    {
        if (!file_exists($file->filename) || !is_readable($file->filename)) {
            return [];
        }

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
