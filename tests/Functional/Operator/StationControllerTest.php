<?php

namespace Tests\Functional\Open;

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
        $this->get('/v1/public/stations');

        $this->assertNotEmpty($this->response->getContent());
        $this->assertResponseStatus(200);
    }
}
