<?php

namespace App\Drivers;

use App\Models\Capture;
use DateTimeImmutable;
use InvalidArgumentException;
use SplFileInfo;

final class RmsDriver extends SourceDriverAbstract
{
    // BR0005_20200811_211456_509757_detected.tar.bz2
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
     * @param SplFileInfo $file
     * @return bool
     */
    private function isAnalyzed(SplFileInfo $file): bool
    {
        return false;
    }

    /**
     * Read the analyze file and fill the file with the details.
     *
     * @param SplFileInfo $file
     * @return array
     */
    private function readCaptureData(SplFileInfo $file)
    {
        return [];
    }

    /**
     * @param SplFileInfo $file
     * @param Capture $capture
     * @return Capture|null
     */
    public function readAnalyzeData(SplFileInfo $file, Capture $capture): ?Capture
    {
        if (!$this->isAnalyzed($file)) {
            return null;
        }

        $captureData = $this->readCaptureData($file);

        $capture->fill($captureData);
        $capture->save();

        return $capture;
    }
}
