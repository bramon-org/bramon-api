<?php

namespace App\Console\Commands;

use App\Drivers\UfoDriver;
use App\Http\Controllers\Shared\UploadApi;
use App\Models\Capture;
use App\Models\File;
use App\Models\Station;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use FilesystemIterator;
use Illuminate\Http\UploadedFile;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use SplFileInfo;

class ImportCapturesCommand extends Command
{
    const FILENAME_EXPRESSION = '/^M([[:digit:]]{8})_([[:digit:]]{6})_([[:alpha:]]{3,5})_(.+)\.([[:alnum:]]{3})$/i';

    use UploadApi;

    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = "import:captures {directory}";

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = "Import captures from a local directory.";

    /**
     * @var UfoDriver
     */
    private UfoDriver $driver;

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        $this->driver = new UfoDriver();

        $directory = $this->argument('directory');
        $objects = $this->getFiles($directory);
        $captures = $this->organizeFiles($objects);
        $imports = $this->createCaptures($captures);

        $this->info($imports ? 'Done.' : 'Fail.');
    }

    /**
     * Read files from directory recursively.
     *
     * @param string $directory
     * @return RecursiveIteratorIterator
     */
    private function getFiles(string $directory): RecursiveIteratorIterator
    {
        return new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($directory, FilesystemIterator::SKIP_DOTS),
            RecursiveIteratorIterator::SELF_FIRST
        );
    }

    /**
     * Organize captures into array blocks by date_hour.
     *
     * @param RecursiveIteratorIterator $objects
     * @return array
     */
    private function organizeFiles(RecursiveIteratorIterator $objects): array
    {
        $queue = [];

        /* @var $file SplFileInfo */
        foreach ($objects as $file) {
            if ($file->isDir()) {
                continue;
            }

            try {
                $date       = $this->driver->getFileDate($file->getFilename())->format('Ymd_His');
                $station    = $this->getStationFromFile($file->getFilename());

                $queue[ $station ][ $date ][] = $file;
            } catch (\InvalidArgumentException $invalidArgumentException) {
                continue;
            }
        }

        return $queue;
    }

    /**
     * Upload the captures to API.
     *
     * @param array $captures
     * @return void
     */
    private function createCaptures(array $captures)
    {
        foreach ($captures as $station => $date) {
            try {
                $stationObj = Station::where(['name' => $station, 'source' => Station::SOURCE_UFO])->firstOrFail();
            } catch (ModelNotFoundException $exception) {
                continue;
            }

            foreach ($date as $captureDate => $captureFiles) {
                $captureHash = md5($stationObj->id . $captureDate);
                $captureDate = \DateTimeImmutable::createFromFormat('Ymd_His', $captureDate);

                $capture = Capture::firstOrNew(['station_id' => $stationObj->id, 'capture_hash' => $captureHash]);
                $capture->captured_at = $captureDate;
                $capture->created_at = $captureDate;
                $capture->save();

                foreach ($captureFiles as $captureFile) {
                    $originalName = $captureFile->getBasename();
                    $originalExtension = $captureFile->getExtension();
                    $fileType = $captureFile->getType();

                    $this->driver->readAnalyzeData(new UploadedFile($captureFile, $originalName), $capture);

                    $pathPrefix = $this->capturePrefixPath($capture, $stationObj);

                    $hash = md5($capture->id . $originalName);

                    $captureFile = File::firstOrNew(['file_hash' => $hash, 'capture_id' => $capture->id]);
                    $captureFile->fill([
                        'filename' => $originalName,
                        'url' => "{$pathPrefix}/{$originalName}",
                        'type' => $fileType,
                        'extension' => $originalExtension,
                        'captured_at' => $captureDate,
                    ]);
                    $captureFile->save();

                    /*
                    $capture_path = sprintf(
                        "%s/%s/%s",
                        storage_path(),
                        'captures',
                        $this->captureStoragePath($capture, $stationObj)
                    );

                    copy($capture_path, $originalName);
                    */
                }
            }
        }

        return true;
    }

    /**
     * Retrieve the station name from file.
     *
     * @param string $filename
     * @return string|null
     */
    private function getStationFromFile(string $filename): ?string
    {
        if (!$this->driver->validate($filename)) {
            return null;
        }

        $exploded = explode('_', $filename);

        $station = "{$exploded[2]}{$exploded[3]}";

        if (preg_match('/([[:alpha:]]+)\.([[:alpha:]]{3})$/i', $station)) {
            return substr($station, 0, -5);
        }

        return substr($station, 0, -4);
    }
}
