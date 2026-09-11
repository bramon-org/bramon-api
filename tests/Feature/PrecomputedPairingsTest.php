<?php

use Laravel\Lumen\Testing\TestCase;
use App\Models\Station;
use App\Models\Capture;
use App\Models\Pairing;

class PrecomputedPairingsTest extends TestCase
{
    public function createApplication()
    {
        return require __DIR__ . '/../../bootstrap/app.php';
    }

    public function testComputeCommandPersistsPairingsAndEndpointReturnsThem()
    {
        // cleanup
        Pairing::truncate();
        Capture::truncate();
        Station::truncate();

        // create two stations
        $s1 = Station::create([
            'name' => 'S1',
            'user_id' => null,
            'latitude' => -23.5,
            'longitude' => -46.6,
            'azimuth' => 100,
            'elevation' => 30,
            'fov' => 2.0,
            'active' => true,
            'visible' => true,
            'source' => 'UFO',
        ]);

        $s2 = Station::create([
            'name' => 'S2',
            'user_id' => null,
            'latitude' => -23.6,
            'longitude' => -46.7,
            'azimuth' => 102,
            'elevation' => 31,
            'fov' => 2.1,
            'active' => true,
            'visible' => true,
            'source' => 'UFO',
        ]);

        // create two captures within time window
        $now = date('Y-m-d H:i:s');

        $c1 = Capture::create([
            'station_id' => $s1->id,
            'class' => 'meteor',
            'captured_at' => $now,
        ]);

        $c2 = Capture::create([
            'station_id' => $s2->id,
            'class' => 'meteor',
            'captured_at' => $now,
        ]);

        // run compute command
        $command = new \App\Console\Commands\ComputePairingsCommand();
        $command->handle();

        $this->assertGreaterThanOrEqual(1, Pairing::count());

        // call endpoint
        $date = date('Y-m-d');
        $this->get('/v1/public/pairings/precomputed?pairing_date=' . $date);
        $this->assertEquals(200, $this->response->status());

        $content = json_decode($this->response->getContent(), true);
        $this->assertArrayHasKey('data', $content);
        $this->assertNotEmpty($content['data']);
    }
}
