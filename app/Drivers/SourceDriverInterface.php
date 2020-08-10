<?php

namespace App\Drivers;

use DateTimeImmutable;

interface SourceDriverInterface
{
    /**
     * Get date of the capture from the filename.
     *
     * @param string $filename
     * @return DateTimeImmutable
     */
    public static function getFileDate(string $filename): ?DateTimeImmutable;
}
