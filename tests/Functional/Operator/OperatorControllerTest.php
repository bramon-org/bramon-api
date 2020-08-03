<?php

namespace Tests\Functional\Operator;

use Laravel\Lumen\Testing\DatabaseMigrations;
use Laravel\Lumen\Testing\DatabaseTransactions;
use Tests\Functional\TestCase;

class OperatorControllerTest extends TestCase
{
    /**
     * @test
     * @return void
     */
    public function getCurrentOperatorDetail()
    {
        $this->get('/v1/operator/me', self::DEFAULT_OPERATOR_HEADERS);

        $this->assertNotEmpty($this->response->getContent());
        $this->assertResponseStatus(200);
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

        $this->post('/v1/admin/operators', $data, self::DEFAULT_ADMIN_HEADERS);

        $data['mobile_phone'] = $this->faker->phoneNumber;

        $this->put('/v1/operator/me', $data, self::DEFAULT_OPERATOR_HEADERS);

        $this->assertResponseStatus(204);
    }
}
