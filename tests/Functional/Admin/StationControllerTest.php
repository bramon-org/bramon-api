<?php

namespace Tests\Functional\Admin;

use Exception;
use Tests\Functional\TestCase;

class StationControllerTest extends TestCase
{
    /**
     * @test
     * @return void
     * @throws Exception
     */
    public function getStations()
    {
        $this->authenticate();

        $this->get('/v1/admin/stations', ['Authorization' => 'Bearer ' . $this->user->api_token]);

        $this->assertNotEmpty($this->response->getContent());
        $this->assertResponseStatus(200);
    }

    /**
     * @test
     * @return void
     * @throws Exception
     */
    public function addStation()
    {
        $this->authenticate();

        $data = [
            'user_id' => $this->user->id,
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

        $this->post('/v1/admin/stations', $data, ['Authorization' => 'Bearer ' . $this->user->api_token]);

        $this->assertResponseStatus(201);
    }

    /**
     * @test
     * @return void
     * @throws Exception
     */
    public function updateStation()
    {
        $this->authenticate();

        $data = [
            'user_id' => $this->user->id,
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

        $this->post('/v1/admin/stations', $data, ['Authorization' => 'Bearer ' . $this->user->api_token]);

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

        $this->put('/v1/admin/stations/' . $station['station']['id'], $newData, ['Authorization' => 'Bearer ' . $this->user->api_token]);

        $this->assertResponseStatus(204);
    }

    /**
     * @test
     * @return void
     * @throws Exception
     */
    public function viewStation()
    {
        $this->authenticate();

        $data = [
            'user_id' => $this->user->id,
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

        $this->post('/v1/admin/stations', $data, ['Authorization' => 'Bearer ' . $this->user->api_token]);

        $station = json_decode($this->response->getContent(), true);

        $this->get('/v1/admin/stations/' . $station['station']['id'], ['Authorization' => 'Bearer ' . $this->user->api_token]);

        $this->assertResponseStatus(200);
    }

    /**
     * @test
     * @return void
     * @throws Exception
     */
    public function viewStationsFromUser()
    {
        $this->authenticate();

        $data = [
            'user_id' => $this->user->id,
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

        $this->post('/v1/admin/stations', $data, ['Authorization' => 'Bearer ' . $this->user->api_token]);

        $this->get('/v1/admin/stations/' . $this->user->id . '/list', ['Authorization' => 'Bearer ' . $this->user->api_token]);

        $this->assertResponseStatus(200);
    }
}
