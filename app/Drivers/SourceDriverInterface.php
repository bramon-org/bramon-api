<?php

namespace App\Drivers;

use App\Models\Capture;
use DateTimeImmutable;
use InvalidArgumentException;
use Symfony\Component\HttpFoundation\File\UploadedFile;

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
     * @param UploadedFile $file
     * @param Capture $capture
     * @return Capture|null
     */
    public function readAnalyzeData(UploadedFile $file, Capture $capture): ?Capture;
}
