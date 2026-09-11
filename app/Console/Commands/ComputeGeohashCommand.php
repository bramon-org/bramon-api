<?php

namespace App\Console\Commands;

use App\Helpers\Geohash;
use App\Models\Capture;
use App\Models\Station;
use Illuminate\Console\Command;

class ComputeGeohashCommand extends Command
{
    protected $signature = 'compute:geohash {--precision=5}';

    protected $description = 'Compute geohash for stations and captures and persist into DB (used to optimize spatial queries)';

    public function handle()
    {
        $precision = (int)$this->option('precision');

        $this->info('Computing geohash for stations...');

        Station::chunk(100, function ($stations) use ($precision) {
            foreach ($stations as $s) {
                if ($s->latitude !== null && $s->longitude !== null) {
                    $s->geohash = Geohash::encode((float)$s->latitude, (float)$s->longitude, $precision);
                    $s->saveQuietly();
                }
            }
        });

        $this->info('Computing geohash for captures...');

        Capture::chunk(100, function ($captures) use ($precision) {
            foreach ($captures as $c) {
                if (isset($c->lat1) && isset($c->lng1) && $c->lat1 !== null && $c->lng1 !== null) {
                    $c->geohash = Geohash::encode((float)$c->lat1, (float)$c->lng1, $precision);
                    $c->saveQuietly();
                }
            }
        });

        $this->info('Geohash computation finished.');

        return 0;
    }
}
