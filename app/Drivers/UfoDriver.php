<?php

namespace App\Drivers;

use App\Models\Capture;
use DateTimeImmutable;
use InvalidArgumentException;
use Symfony\Component\HttpFoundation\File\UploadedFile;

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
     * @param UploadedFile $file
     * @return bool
     */
    private function isAnalyzed(UploadedFile $file): bool
    {
        return preg_match("/A.XML$/i", $file->getClientOriginalName())
            && file_exists($file->getRealPath());
    }

    /**
     * Read the analyze file and fill the file with the details.
     *
     * @param UploadedFile $file
     * @return array
     */
    private function readCaptureData(UploadedFile $file)
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
     * @param UploadedFile $file
     * @param Capture $capture
     * @return Capture|null
     */
    public function readAnalyzeData(UploadedFile $file, Capture $capture): ?Capture
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
