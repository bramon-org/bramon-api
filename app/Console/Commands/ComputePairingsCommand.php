<?php

namespace App\Console\Commands;

use App\Models\Pairing;
use App\Services\PairingService;
use Carbon\Carbon;
use Illuminate\Console\Command;

class ComputePairingsCommand extends Command
{
    protected $signature = 'compute:pairings {captured_date?} {--max_distance=500} {--time_window=5} {--output=}';

    protected $description = 'Compute probable pairings for a given date and store as JSON in storage/pairings/ and in DB table pairings';

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
            'limit' => 100000,
            'page' => 1,
        ]);

        // persist to DB table pairings
        Pairing::where('pairing_date', $capturedDate)->delete();

        foreach ($result['data'] as $row) {
            try {
                $a = $row['capture_a'];
                $b = $row['capture_b'];

                Pairing::create([
                    'capture_a_id' => $a->id,
                    'capture_b_id' => $b->id,
                    'time_difference_seconds' => $row['time_difference_seconds'],
                    'distance_km' => $row['distance_km'],
                    'azimuth_diff' => $row['azimuth_diff'],
                    'elevation_diff' => $row['elevation_diff'],
                    'fov_diff' => $row['fov_diff'],
                    'pairing_date' => $capturedDate,
                ]);
            } catch (\Exception $e) {
                // ignore individual insert errors but continue
                $this->error('Failed to persist a pairing: ' . $e->getMessage());
            }
        }

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
