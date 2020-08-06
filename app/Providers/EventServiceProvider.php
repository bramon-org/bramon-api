<?php

namespace App\Providers;

use App\Events\FileUploadEvent;
use App\Listeners\FileUploadListener;
use Laravel\Lumen\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        FileUploadEvent::class => [
            FileUploadListener::class,
        ],
    ];
}
