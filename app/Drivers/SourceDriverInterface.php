<?php

namespace App\Drivers;

use App\Models\Capture;
use DateTimeImmutable;
use InvalidArgumentException;
use SplFileInfo;

interface SourceDriverInterface
{
    /**
     * Get date of the capture from the filename.
     *
     * @param string $filename
     * @return DateTimeImmutable
     * @throws InvalidArgumentException
     */
    public function getFileDate(string $filename): ?DateTimeImmutable;

    /**
     * Read the analyze data and save to capture.
     *
     * @param SplFileInfo $file
     * @param Capture $capture
     * @return Capture|null
     */
    public function readAnalyzeData(SplFileInfo $file, Capture $capture): ?Capture;
}
