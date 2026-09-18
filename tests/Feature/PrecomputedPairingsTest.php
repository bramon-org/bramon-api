<?php

use App\Helpers\Geohash;
use App\Models\Station;
use App\Models\Capture;
use App\Models\Pairing;
use Tests\Functional\TestCase;

class PrecomputedPairingsTest extends TestCase
{
    public function testComputeCommandPersistsPairingsAndEndpointReturnsThem()
    {
        $this->authenticate();

        // cleanup
        Pairing::truncate();
        Capture::truncate();
        Station::truncate();

        // create two stations
        $s1 = Station::create([
            'name' => 'S1',
            'user_id' => $this->user->id,
            'city' => 'Sao Paulo',
            'state' => 'SP',
            'country' => 'Brazil',
            'latitude' => -23.5505,
            'longitude' => -46.6333,
            'azimuth' => 100,
            'elevation' => 30,
            'fov' => 2.0,
            'camera_model' => 'Test camera',
            'camera_lens' => 'Test lens',
            'camera_capture' => 'Test capture',
            'active' => true,
            'visible' => true,
            'source' => 'UFO',
        ]);

        $s2 = Station::create([
            'name' => 'S2',
            'user_id' => $this->user->id,
            'city' => 'Sao Paulo',
            'state' => 'SP',
            'country' => 'Brazil',
            'latitude' => -23.5506,
            'longitude' => -46.6334,
            'azimuth' => 102,
            'elevation' => 31,
            'fov' => 2.1,
            'camera_model' => 'Test camera',
            'camera_lens' => 'Test lens',
            'camera_capture' => 'Test capture',
            'active' => true,
            'visible' => true,
            'source' => 'UFO',
        ]);

        $geohash = Geohash::encode((float) $s1->latitude, (float) $s1->longitude);
        $s1->geohash = $geohash;
        $s2->geohash = $geohash;
        $s1->save();
        $s2->save();

        // create two captures within time window
        $date = date('Y-m-d');
        $now = date('Y-m-d H:i:s');

        $c1 = Capture::create([
            'station_id' => $s1->id,
            'capture_hash' => 'pairing-capture-1',
            'class' => 'meteor',
            'captured_at' => $now,
        ]);

        $c2 = Capture::create([
            'station_id' => $s2->id,
            'capture_hash' => 'pairing-capture-2',
            'class' => 'meteor',
            'captured_at' => $now,
        ]);

        // run compute command through the application kernel
        app('Illuminate\\Contracts\\Console\\Kernel')->call('compute:pairings', [
            'captured_date' => $date,
        ]);

        $this->assertGreaterThanOrEqual(1, Pairing::count());

        // call endpoint
        $this->get('/v1/public/pairings/precomputed?pairing_date=' . $date);
        $this->assertEquals(200, $this->response->status());

        $content = json_decode($this->response->getContent(), true);
        $this->assertArrayHasKey('data', $content);
        $this->assertNotEmpty($content['data']);
    }
}
