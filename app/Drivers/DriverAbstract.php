<?php

namespace App\Drivers;

use App\Models\Capture;
use DateTimeImmutable;
use InvalidArgumentException;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * Class DriverAbstract
 * @package App\Drivers
 */
abstract class DriverAbstract
{
    /**
     * Get date of the capture from the filename.
     *
     * @param string $filename
     * @return DateTimeImmutable
     * @throws InvalidArgumentException
     */
    abstract public function getFileDate(string $filename): ?DateTimeImmutable;

    /**
     * Read the analyze data and save to capture.
     *
     * @param UploadedFile $file
     * @param Capture $capture
     * @return Capture|null
     */
    abstract public function readAnalyzeData(UploadedFile $file, Capture $capture): ?Capture;

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
