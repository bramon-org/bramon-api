<?php

namespace App\Jobs;

use App\Events\FileUploadEvent;
use App\Models\Capture;
use App\Models\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

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
}
