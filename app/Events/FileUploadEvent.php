<?php

namespace App\Events;

use stdClass;

class FileUploadEvent extends Event
{
    public stdClass $file;

    /**
     * Create a new event instance.
     *
     * @param stdClass $file
     */
    public function __construct(stdClass $file)
    {
        $this->file = $file;
    }
}
