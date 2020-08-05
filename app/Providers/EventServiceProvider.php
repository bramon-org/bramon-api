<?php

namespace App\Providers;

use App\Events\ExampleEvent;
use App\Events\FileUploadEvent;
use App\Listeners\ExampleListener;
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
        ExampleEvent::class => [
            ExampleListener::class,
        ],
        FileUploadEvent::class => [
            FileUploadListener::class,
        ],
    ];
}
