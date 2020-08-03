<?php

namespace Tests\Functional\Admin;

use Laravel\Lumen\Testing\DatabaseMigrations;
use Laravel\Lumen\Testing\DatabaseTransactions;
use Tests\Functional\TestCase;

class OperatorControllerTest extends TestCase
{
    /**
     * @test
     * @return void
     */
    public function listOperators()
    {
        $this->get('/v1/admin/operators', self::DEFAULT_HEADERS);

        $this->assertNotEmpty($this->response->getContent());
        $this->assertResponseStatus(200);
    }

    /**
     * @test
     * @return void
     */
    public function addOperator()
    {
        $data = [
            'email' => $this->faker->email,
            'name' => $this->faker->name,
            'mobile_phone' => substr($this->faker->phoneNumber, 0, 10),
            'role' => \App\Models\User::ROLE_OPERATOR,
        ];

        $this->post('/v1/admin/operators', $data, self::DEFAULT_HEADERS);

        $this->assertNotEmpty($this->response->getContent());
        $this->assertResponseStatus(201);
    }

    /**
     * @test
     * @return void
     */
    public function updateOperator()
    {
        $data = [
            'email' => $this->faker->email,
            'name' => $this->faker->name,
            'mobile_phone' => $this->faker->phoneNumber,
            'role' => \App\Models\User::ROLE_OPERATOR,
        ];

        $this->post('/v1/admin/operators', $data, self::DEFAULT_HEADERS);

        $operator = json_decode($this->response->getContent(), true);

        $data['mobile_phone'] = $this->faker->phoneNumber;

        $this->put('/v1/admin/operators/' . $operator['operator']['id'], $data, self::DEFAULT_HEADERS);

        $this->assertResponseStatus(204);
    }

    /**
     * @test
     * @return void
     */
    public function viewOperator()
    {
        $data = [
            'email' => $this->faker->email,
            'name' => $this->faker->name,
            'mobile_phone' => $this->faker->phoneNumber,
            'role' => \App\Models\User::ROLE_OPERATOR,
        ];

        $this->post('/v1/admin/operators', $data, self::DEFAULT_HEADERS);

        $operator = json_decode($this->response->getContent(), true);

        $this->get('/v1/admin/operators/' . $operator['operator']['id'], self::DEFAULT_HEADERS);

        $this->assertResponseStatus(200);
    }
}
