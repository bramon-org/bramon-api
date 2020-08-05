<?php

namespace App\Events;

class FileUploadEvent extends Event
{
    public string $capture;

    /**
     * Create a new event instance.
     *
     * @param string $capture
     */
    public function __construct(string $capture)
    {
        $this->capture = $capture;
    }
}
