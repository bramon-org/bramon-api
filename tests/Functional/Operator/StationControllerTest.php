<?php

namespace Tests\Functional\Operator;

use App\Models\User;
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
        $this->authenticate(User::ROLE_OPERATOR);

        $this->get('/v1/operator/stations', ['Authorization' => 'Bearer ' . $this->user->api_token]);

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
        $this->authenticate(User::ROLE_OPERATOR);

        $data = [
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

        $this->post('/v1/operator/stations', $data, ['Authorization' => 'Bearer ' . $this->user->api_token]);

        $this->assertResponseStatus(201);
    }

    /**
     * @test
     * @return void
     * @throws Exception
     */
    public function updateStation()
    {
        $this->authenticate(User::ROLE_OPERATOR);

        $data = [
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

        $this->post('/v1/operator/stations', $data, ['Authorization' => 'Bearer ' . $this->user->api_token]);

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

        $this->put('/v1/operator/stations/' . $station['station']['id'], $newData, ['Authorization' => 'Bearer ' . $this->user->api_token]);

        $this->assertResponseStatus(204);
    }

    /**
     * @test
     * @return void
     * @throws Exception
     */
    public function viewStation()
    {
        $this->authenticate(User::ROLE_OPERATOR);

        $data = [
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

        $this->post('/v1/operator/stations', $data, ['Authorization' => 'Bearer ' . $this->user->api_token]);

        $station = json_decode($this->response->getContent(), true);

        $this->get('/v1/operator/stations/' . $station['station']['id'], ['Authorization' => 'Bearer ' . $this->user->api_token]);

        $this->assertResponseStatus(200);
    }
}
