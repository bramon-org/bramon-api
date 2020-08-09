<?php

namespace Tests\Functional\Open;

use Exception;
use Tests\Functional\TestCase;

class CaptureControllerTest extends TestCase
{
    /**
     * @test
     * @return void
     * @throws Exception
     */
    public function getCaptures()
    {
        $this->get('/v1/public/captures');

        $this->assertNotEmpty($this->response->getContent());
        $this->assertResponseStatus(200);
    }
}
