<?php

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
        $this->get('/v1/admin/operators', ['Authorization' => 'Bearer eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9']);

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
            'mobile_phone' => $this->faker->phoneNumber,
            'role' => \App\Models\User::ROLE_OPERATOR,
        ];

        $this->post('/v1/admin/operators', $data, ['Authorization' => 'Bearer eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9']);

        $this->assertNotEmpty($this->response->getContent());
        $this->assertResponseStatus(201);
    }
}
