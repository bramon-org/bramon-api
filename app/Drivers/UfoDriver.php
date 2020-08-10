<?php

namespace App\Drivers;

use DateTimeImmutable;

abstract class UfoDriver extends SourceDriverAbstract
{
    const FILENAME_EXPRESSION = '/^M([[:digit:]]{8})_([[:digit:]]{6})_([[:alpha:]]{3,5})_([[:alnum:]]+)\.([[:alpha:]]{3})$/i';

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
