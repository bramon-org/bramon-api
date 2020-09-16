<?php

namespace App\Jobs;

use App\Events\FileUploadEvent;
use Illuminate\Support\Facades\Log;

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

        Log::info('=== FileUploaderJob end ========');

        return true;
    }
}
