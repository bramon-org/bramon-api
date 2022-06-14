<?php

namespace App\Drivers;

use App\Models\Capture;
use DateTimeImmutable;
use InvalidArgumentException;
use SimpleXMLElement;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use ErrorException;

final class UfoDriver extends DriverAbstract
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
     * Read the analyze file.
     *
     * @param UploadedFile $file
     * @return SimpleXMLElement
     */
    private function readAnalyzeFile(UploadedFile $file): SimpleXMLElement
    {
        $inputFile = $file->getRealPath();

        return simplexml_load_file($inputFile);
    }

    /**
     * Read the analyze file and fill the file with the details.
     *
     * @param SimpleXMLElement $file
     * @return array
     */
    private function readCaptureData(SimpleXMLElement $file)
    {
        $data = [];
        $itemList = $file->ua2_objects->ua2_object;

        foreach ($itemList->attributes() as $attributeKey => $attributeValue) {
            $data[ (string) $attributeKey ] = (string) $attributeValue;
        };

        return $data;
    }

    /**
     * Read the station data from XML analyze.
     *
     * @param SimpleXMLElement $file
     * @return array
     */
    private function readStationData(SimpleXMLElement $file)
    {
        $itemAttributes = $file->attributes();

        return [
            'latitude' => (string) $itemAttributes['lat'],
            'longitude' => (string) $itemAttributes['lng'],
            'azimuth' => (string) $itemAttributes['az'],
            'elevation' => (string) $itemAttributes['ev'],
            'alt' => (string) $itemAttributes['alt'],
            'rotation' => (string) $itemAttributes['rot'],
            'cx' => (string) $itemAttributes['cx'],
            'cy' => (string) $itemAttributes['cy'],
            'fov' => (string) $itemAttributes['vx'],
            'dec1' => (string) $itemAttributes['dc1'],
            'dec2' => (string) $itemAttributes['dc2'],
            'fps' => (string) $itemAttributes['fps'],
            'frames' => (string) $itemAttributes['frames'],
            'camera_model' => (string) $itemAttributes['cam'],
            'camera_lens' => (string) $itemAttributes['lens'],
            'camera_capture' => (string) $itemAttributes['cap'],
        ];
    }

    /**
     * Read analyze data from file.
     *
     * @param UploadedFile $file
     * @param Capture $capture
     * @return Capture
     */
    public function readAnalyzeData(UploadedFile $file, Capture $capture): Capture
    {
        if (!$this->isAnalyzed($file)) {
            return $capture;
        }

        try {
            $xml = $this->readAnalyzeFile($file);
            $captureData = $this->readCaptureData($xml);
            $stationData = $this->readStationData($xml);

            $capture->fill($captureData);
            $capture->save();

            $station = $capture->station;
            $station->fill($stationData);
            $station->save();
        } catch (ErrorException $errorException) {
            info($errorException->getMessage());
        }

        return $capture;
    }
}
