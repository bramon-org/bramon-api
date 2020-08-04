<?php

namespace Tests\Functional\Admin;

use Laravel\Lumen\Testing\DatabaseMigrations;
use Laravel\Lumen\Testing\DatabaseTransactions;
use Tests\Functional\TestCase;

class StationControllerTest extends TestCase
{
    /**
     * @test
     * @return void
     */
    public function getStations()
    {
        $this->get('/v1/admin/stations', self::DEFAULT_ADMIN_HEADERS);

        $this->assertNotEmpty($this->response->getContent());
        $this->assertResponseStatus(200);
    }

    /**
     * @test
     * @return void
     */
    public function addStation()
    {
        $data = [
            'user_id' => $this->faker->uuid,
            'name' => $this->faker->name,
            'latitude' => $this->faker->latitude,
            'longitude' => $this->faker->longitude,
            'azimuth' => $this->faker->numberBetween(0, 360),
            'elevation' => $this->faker->numberBetween(0, 90),
            'fov' => $this->faker->numberBetween(0, 360),
            'camera_model' => $this->faker->company,
            'camera_lens' => $this->faker->company,
            'camera_capture' => $this->faker->company
        ];

        $this->post('/v1/operator/stations', $data, self::DEFAULT_ADMIN_HEADERS);

        $this->assertResponseStatus(201);
    }

    /**
     * @test
     * @return void
     */
    public function updateStation()
    {
        $data = [
            'user_id' => $this->faker->uuid,
            'name' => $this->faker->name,
            'latitude' => $this->faker->latitude,
            'longitude' => $this->faker->longitude,
            'azimuth' => $this->faker->numberBetween(0, 360),
            'elevation' => $this->faker->numberBetween(0, 90),
            'fov' => $this->faker->numberBetween(0, 360),
            'camera_model' => $this->faker->company,
            'camera_lens' => $this->faker->company,
            'camera_capture' => $this->faker->company
        ];

        $this->post('/v1/operator/stations', $data, self::DEFAULT_ADMIN_HEADERS);

        $station = json_decode($this->response->getContent(), true);

        $newData = [
            'name' => $this->faker->name,
            'latitude' => $this->faker->latitude,
            'longitude' => $this->faker->longitude,
            'azimuth' => $this->faker->numberBetween(0, 360),
            'elevation' => $this->faker->numberBetween(0, 90),
            'fov' => $this->faker->numberBetween(0, 360),
            'camera_model' => $this->faker->company,
            'camera_lens' => $this->faker->company,
            'camera_capture' => $this->faker->company,
            'active' => true,
        ];

        $this->put('/v1/operator/stations/' . $station['station']['id'], $newData, self::DEFAULT_ADMIN_HEADERS);

        $this->assertResponseStatus(204);
    }

    /**
     * @test
     * @return void
     */
    public function viewStation()
    {
        $data = [
            'user_id' => $this->faker->uuid,
            'name' => $this->faker->name,
            'latitude' => $this->faker->latitude,
            'longitude' => $this->faker->longitude,
            'azimuth' => $this->faker->numberBetween(0, 360),
            'elevation' => $this->faker->numberBetween(0, 90),
            'fov' => $this->faker->numberBetween(0, 360),
            'camera_model' => $this->faker->company,
            'camera_lens' => $this->faker->company,
            'camera_capture' => $this->faker->company
        ];

        $this->post('/v1/operator/stations', $data, self::DEFAULT_ADMIN_HEADERS);

        $station = json_decode($this->response->getContent(), true);

        $this->get('/v1/operator/stations/' . $station['station']['id'], self::DEFAULT_ADMIN_HEADERS);

        $this->assertResponseStatus(200);
    }

    /**
     * @test
     * @return void
     */
    public function viewStationsFromUser()
    {
        $this->markTestIncomplete();

        $data = [
            'user_id' => $this->faker->uuid,
            'name' => $this->faker->name,
            'latitude' => $this->faker->latitude,
            'longitude' => $this->faker->longitude,
            'azimuth' => $this->faker->numberBetween(0, 360),
            'elevation' => $this->faker->numberBetween(0, 90),
            'fov' => $this->faker->numberBetween(0, 360),
            'camera_model' => $this->faker->company,
            'camera_lens' => $this->faker->company,
            'camera_capture' => $this->faker->company
        ];

        $this->post('/v1/operator/stations', $data, self::DEFAULT_ADMIN_HEADERS);

        $station = json_decode($this->response->getContent(), true);

        $this->get('/v1/operator/stations/' . $station['station']['user_id'] . '/list', self::DEFAULT_ADMIN_HEADERS);

        $this->assertResponseStatus(200);
    }
}
