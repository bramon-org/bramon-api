<?php

namespace App\Jobs;

use App\Events\FileUploadEvent;
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
     * @return void
     */
    public function handle()
    {
        Log::info('=== FileUploaderJob  ========');

        $event = $this->event;
        $inputFile = storage_path() . '/sync/' . $event->file->filename;

        if (!file_exists($inputFile)) {
            return;
        }

        Storage::disk(config('filesystems.cloud'))
            ->put(
                $event->file->filename,
                fopen($inputFile, 'r')
            );

        unlink($inputFile);
    }
}
