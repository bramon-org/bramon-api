<?php

namespace App\Drivers;

use App\Models\Capture;
use DateTimeImmutable;
use InvalidArgumentException;
use Symfony\Component\HttpFoundation\File\UploadedFile;

final class RmsDriver extends DriverAbstract
{
    const FILENAME_EXPRESSION = '/^([[:alpha:]]{2})([[:digit:]]{4})_([[:digit:]]{8})_([[:digit:]]{6})_([[:digit:]]{6})_(detected\.tar\.bz2)$/i';

    /**
     * @inheritDoc
     */
    public function getFileDate(string $filename): ?DateTimeImmutable
    {
        if (!self::validate($filename)) {
            throw new InvalidArgumentException('Invalid filename');
        }

        $fileExploded = explode('_', $filename);
        $fileDate = $fileExploded[1];
        $fileTime = $fileExploded[2];
        $fileDateTime = $fileDate . '_' . $fileTime;

        return DateTimeImmutable::createFromFormat('Ymd_His', $fileDateTime) ?? null;
    }

    /**
     * Check if file is an analyze file.
     *
     * @param UploadedFile $file
     * @return bool
     */
    private function isAnalyzed(UploadedFile $file): bool
    {
        return false;
    }

    /**
     * Read the analyze file and fill the file with the details.
     *
     * @param UploadedFile $file
     * @return array
     */
    private function readCaptureData(UploadedFile $file)
    {
        return [];
    }

    /**
     * @param UploadedFile $file
     * @param Capture $capture
     * @return Capture
     */
    public function readAnalyzeData(UploadedFile $file, Capture $capture): Capture
    {
        if (!$this->isAnalyzed($file)) {
            return $capture;
        }

        $captureData = $this->readCaptureData($file);

        $capture->fill($captureData);
        $capture->save();

        return $capture;
    }
}
