<?php

use Laravel\Lumen\Testing\TestCase;

class PairingEndpointTest extends TestCase
{
    /**
     * Creates the application.
     *
     * @return \Laravel\Lumen\Application
     */
    public function createApplication()
    {
        return require __DIR__ . '/../../bootstrap/app.php';
    }

    public function testPairingsEndpointReturnsExpectedStructure()
    {
        $date = date('Y-m-d');
        $this->get('/v1/public/pairings?captured_date=' . $date);

        $this->assertEquals(200, $this->response->status());

        $content = json_decode($this->response->getContent(), true);

        $this->assertIsArray($content);
        $this->assertArrayHasKey('total', $content);
        $this->assertArrayHasKey('data', $content);
    }
}
