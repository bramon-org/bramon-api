<?php

namespace App\Drivers;

abstract class SourceDriverAbstract implements SourceDriverInterface
{
    /**
     * Validate the filename.
     *
     * @param $filename
     * @return bool
     */
    public function validate($filename): bool
    {
        return preg_match(static::FILENAME_EXPRESSION, $filename);
    }
}
