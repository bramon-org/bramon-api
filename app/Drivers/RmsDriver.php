<?php

namespace App\Drivers;

use DateTimeImmutable;

abstract class RmsDriver extends SourceDriverAbstract
{
    const FILENAME_EXPRESSION = '/^FF_([[:alpha:]]{2})([[:digit:]]{4})_([[:digit:]]{8})_(.+)$/i';

    /**
     * @inheritDoc
     */
    public static function getFileDate(string $filename): ?DateTimeImmutable
    {
        if (!self::validate($filename)) {
            return null;
        }

        $fileExploded = explode('_', $filename);
        $fileDate = substr($fileExploded[0],1);
        $fileTime = $fileExploded[1];
        $fileDateTime = $fileDate . '_' . $fileTime;

        return DateTimeImmutable::createFromFormat('Ymd_His', $fileDateTime) ?? null;
    }
}
