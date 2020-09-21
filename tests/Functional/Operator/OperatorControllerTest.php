<?php

namespace Tests\Functional\Operator;

use App\Models\User;
use Tests\Functional\TestCase;

class OperatorControllerTest extends TestCase
{
    /**
     * @test
     * @return void
     * @throws \Exception
     */
    public function getCurrentOperatorDetail()
    {
        $this->authenticate(User::ROLE_OPERATOR);

        $this->get('/v1/operator/operators', ['Authorization' => 'Bearer ' . $this->user->api_token]);

        $this->assertNotEmpty($this->response->getContent());
        $this->assertResponseStatus(200);
    }

    /**
     * @test
     * @return void
     * @throws \Exception
     */
    public function updateOperator()
    {
        $this->authenticate(User::ROLE_OPERATOR);

        $data = [
            'name' => $this->faker->name,
            'mobile_phone' => $this->faker->phoneNumber,
            'city' => $this->faker->city,
            'state' => $this->faker->state,
        ];

        $this->put('/v1/operator/operators', $data, ['Authorization' => 'Bearer ' . $this->user->api_token]);

        $this->assertResponseStatus(204);
    }
}
