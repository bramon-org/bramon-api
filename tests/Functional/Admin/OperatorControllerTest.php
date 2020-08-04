<?php

namespace Tests\Functional\Admin;

use Exception;
use Tests\Functional\TestCase;

class OperatorControllerTest extends TestCase
{
    /**
     * @test
     * @return void
     * @throws Exception
     */
    public function listOperators()
    {
        $this->authenticate();

        $this->get('/v1/admin/operators', ['Authorization' => 'Bearer ' . $this->user->api_token]);

        $this->assertNotEmpty($this->response->getContent());
        $this->assertResponseStatus(200);
    }

    /**
     * @test
     * @return void
     * @throws Exception
     */
    public function addOperator()
    {
        $this->authenticate();

        $data = [
            'email'         => $this->faker->email,
            'name'          => $this->faker->name,
            'city'          => $this->faker->city,
            'state'         => $this->faker->state,
            'mobile_phone'  => $this->faker->phoneNumber,
            'role'          => \App\Models\User::ROLE_OPERATOR,
        ];

        $this->post('/v1/admin/operators', $data, ['Authorization' => 'Bearer ' . $this->user->api_token]);

        $this->assertNotEmpty($this->response->getContent());
        $this->assertResponseStatus(201);
    }

    /**
     * @test
     * @return void
     * @throws Exception
     */
    public function updateOperator()
    {
        $this->authenticate();

        $data = [
            'name' => $this->faker->name,
            'city' => $this->faker->city,
            'state' => $this->faker->state,
            'mobile_phone' => $this->faker->phoneNumber,
            'role' => \App\Models\User::ROLE_OPERATOR,
        ];

        $this->put('/v1/admin/operators/' . $this->user->id, $data, ['Authorization' => 'Bearer ' . $this->user->api_token]);

        $this->assertResponseStatus(204);
    }

    /**
     * @test
     * @return void
     * @throws Exception
     */
    public function viewOperator()
    {
        $this->authenticate();

        $this->get('/v1/admin/operators/' . $this->user->id, ['Authorization' => 'Bearer ' . $this->user->api_token]);

        $this->assertResponseStatus(200);
    }
}
