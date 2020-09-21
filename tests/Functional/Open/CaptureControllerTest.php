<?php

namespace Tests\Functional\Open;

use App\Models\Capture;
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

    /**
     * @test
     * @return void
     * @throws Exception
     */
    public function viewCapture()
    {
        $this->authenticate();

        $capture = Capture::firstOrNew(['station_id' => $this->station->id, 'capture_hash' => md5(uniqid())]);
        $capture->captured_at = new \DateTimeImmutable();
        $capture->save();

        $this->get('/v1/public/captures/' . $capture->id);

        $this->assertNotEmpty($this->response->getContent());
        $this->assertResponseStatus(200);
    }
}
