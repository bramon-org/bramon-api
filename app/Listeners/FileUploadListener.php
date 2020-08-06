<?php

namespace App\Listeners;

use App\Events\FileUploadEvent;
use App\Jobs\FileUploaderJob;
use Illuminate\Support\Facades\Log;

class FileUploadListener
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  FileUploadEvent  $event
     * @return void
     */
    public function handle(FileUploadEvent $event)
    {
        Log::info('=== FileUploadListener  ========');

        dispatch(new FileUploaderJob($event));
    }
}
