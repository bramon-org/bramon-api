<?php

use Tests\Functional\TestCase;

class PairingEndpointTest extends TestCase
{
    public function testPairingsEndpointReturnsExpectedStructure()
    {
        $date = date('Y-m-d');
        $this->get('/v1/public/pairings?captured_date=' . $date);

        $this->assertEquals(200, $this->response->status());

        $content = json_decode($this->response->getContent(), true);

        $this->assertIsArray($content);
        $this->assertArrayHasKey('total', $content);
        $this->assertArrayHasKey('data', $content);
        $this->assertArrayHasKey('per_page', $content);
        $this->assertArrayHasKey('page', $content);
    }

    public function testPairingServiceCanRunEmpty()
    {
        $service = new \App\Services\PairingService();
        $result = $service->findPairings(['captured_date' => date('Y-m-d')]);

        $this->assertIsArray($result);
        $this->assertArrayHasKey('total', $result);
        $this->assertArrayHasKey('data', $result);
    }
}
