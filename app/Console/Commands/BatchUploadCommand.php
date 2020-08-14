<?php

namespace App\Console\Commands;

use App\Drivers\UfoDriver;
use FilesystemIterator;
use Illuminate\Console\Command;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use SplFileInfo;
use aalfiann\ParallelRequest;

class BatchUploadCommand extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $signature = "batch:upload {directory}";

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = "Sent a massive upload to API from directory.";


    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $directory = $this->argument('directory');
        $objects = $this->getFiles($directory);
        $captures = $this->organizeFiles($objects);
        $upload = $this->uploadCaptures($captures);

        return $upload;
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
            $date = UfoDriver::getFileDate($file->getFilename());

            if (is_null($date)) {
                continue;
            }

            $station = $this->getStationFromFile($file->getFilename());
            $queue[ $station ][ $date->format('Ymd_His') ][] = $file;
        }

        return $queue;
    }

    /**
     * Retrieve the station name from file.
     *
     * @param string $filename
     * @return string|null
     */
    private function getStationFromFile(string $filename): ?string
    {
        if (!UfoDriver::validate($filename)) {
            return null;
        }

        $exploded = explode('_', $filename);

        $station = "{$exploded[2]}{$exploded[3]}";

        if (preg_match('/[[:alpha:]]$/i', $station)) {
            return substr($station, 0, -5);
        }

        return substr($station, 0, -4);
    }

    /**
     * Upload the captures to API.
     *
     * @param array $captures
     */
    private function uploadCaptures(array $captures)
    {
        $queue = [];

        foreach ($captures as $station => $date) {
            foreach ($date as $capture) {
                $queue[] = [
                    'url' => 'https://webhook.site/b3d283ea-4f4a-4d85-b98b-15ffda98cb2d',
                    'post' => [
                        'station_id' => $station,
                        'files' => json_encode($capture),
                    ]
                ];
            }
        }

        $req = new ParallelRequest;
        $req->request = $queue;
        $req->encoded = true;
        $req->options = [
            CURLOPT_NOBODY => false,
            CURLOPT_HEADER => false,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false
        ];

        echo var_dump($req->send()->getResponse());
    }
}
