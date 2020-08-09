<?php

namespace Tests\Functional\Open;

use Tests\Functional\TestCase;

class OperatorControllerTest extends TestCase
{
    /**
     * @test
     * @return void
     * @throws \Exception
     */
    public function getOperators()
    {
        $this->get('/v1/public/operators');

        $this->assertNotEmpty($this->response->getContent());
        $this->assertResponseStatus(200);
    }
}
