<?php

namespace App\Events;

use App\Models\Capture;

class FileUploadEvent extends Event
{
    public Capture $capture;

    /**
     * Create a new event instance.
     *
     * @param Capture $capture
     */
    public function __construct(Capture $capture)
    {
        $this->capture = $capture;
    }
}
