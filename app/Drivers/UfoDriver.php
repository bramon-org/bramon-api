<?php

namespace App\Drivers;

use App\Models\Capture;
use DateTimeImmutable;
use InvalidArgumentException;
use SplFileInfo;

final class UfoDriver extends SourceDriverAbstract
{
    const FILENAME_EXPRESSION = '/^M([[:digit:]]{8})_([[:digit:]]{6})_([[:alpha:]]{3,5})_(.+)\.([[:alnum:]]{3})$/i';

    /**
     * @inheritDoc
     */
    public function getFileDate(string $filename): ?DateTimeImmutable
    {
        if (!self::validate($filename)) {
            throw new InvalidArgumentException('Invalid filename');
        }

        $fileExploded = explode('_', $filename);
        $fileDate = substr($fileExploded[0],1);
        $fileTime = $fileExploded[1];
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
        return preg_match("/A.XML$/i", $file->getBasename())
            && file_exists($file->getRealPath());
    }

    /**
     * Read the analyze file and fill the file with the details.
     *
     * @param SplFileInfo $file
     * @return array
     */
    private function readCaptureData(SplFileInfo $file)
    {
        try {
            $inputFile = $file->getRealPath();
            $xml = simplexml_load_file($inputFile);
            $itemList = $xml->ua2_objects->ua2_object;

            $data = [];

            foreach ($itemList->attributes() as $attributeKey => $attributeValue) {
                $data[ (string) $attributeKey ] = (string) $attributeValue;
            };

            return $data;
        } catch (\ErrorException $errorException) {
            return [];
        }
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
