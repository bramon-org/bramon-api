<?php

namespace App\Console\Commands;

use App\Services\PairingService;
use Carbon\Carbon;
use Illuminate\Console\Command;

class ComputePairingsCommand extends Command
{
    protected $signature = 'compute:pairings {captured_date?} {--max_distance=500} {--time_window=5} {--output=}';

    protected $description = 'Compute probable pairings for a given date and store as JSON in storage/pairings/';

    public function handle()
    {
        $dateArg = $this->argument('captured_date');
        $maxDistance = (float)$this->option('max_distance');
        $timeWindow = (int)$this->option('time_window');
        $output = $this->option('output');

        $date = $dateArg ? Carbon::createFromFormat('Y-m-d', $dateArg) : Carbon::now();
        $capturedDate = $date->format('Y-m-d');

        $this->info("Computing pairings for date: {$capturedDate}");

        $service = new PairingService();

        $result = $service->findPairings([
            'captured_date' => $capturedDate,
            'max_distance_km' => $maxDistance,
            'time_window_seconds' => $timeWindow,
            'limit' => 100000, // get full results
            'page' => 1,
        ]);

        $dir = storage_path('pairings');
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        $filename = $output ?: ($dir . DIRECTORY_SEPARATOR . $capturedDate . '.json');

        file_put_contents($filename, json_encode($result, JSON_PRETTY_PRINT));

        $this->info("Pairings written to: {$filename}");

        return 0;
    }
}
