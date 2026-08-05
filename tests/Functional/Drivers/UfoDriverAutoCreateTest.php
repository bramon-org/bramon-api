<?php

namespace Tests\Functional\Drivers;

use App\Drivers\UfoDriver;
use App\Models\Capture;
use App\Models\Station;
use Illuminate\Http\UploadedFile;
use Tests\Functional\TestCase;

class UfoDriverAutoCreateTest extends TestCase
{
    /** @test */
    public function it_creates_station_from_analyze_file_when_configured()
    {
        // Create default user (and default station, but we will use the user id)
        $this->authenticate();

        // Ensure driver will create stations using our test user id
        putenv('CAPTURE_AUTO_CREATE_USER_ID=' . $this->user->id);
        $_ENV['CAPTURE_AUTO_CREATE_USER_ID'] = $this->user->id;

        $xml = <<<XML
<?xml version="1.0"?>
<UA lat="12.345" lng="67.890" az="160" ev="30" alt="0" rot="0" cx="0" cy="0" vx="52.23" cam="TEST_CAM_AUTO" lens="TEST_LENS" cap="TEST_CAP">
    <ua2_objects>
        <ua2_object fps="25" frames="100" dc1="0" dc2="0" />
    </ua2_objects>
</UA>
XML;

        $tmpFile = sys_get_temp_dir() . '/test_A.XML';
        file_put_contents($tmpFile, $xml);

        $uploaded = new UploadedFile($tmpFile, 'M20200608_005550_TLP_5A.XML', null, null, null, true);

        $capture = new Capture();
        $driver = new UfoDriver();

        $driver->readAnalyzeData($uploaded, $capture);

        // After processing, capture should have station_id and station should exist
        $this->assertNotNull($capture->station_id);
        $station = Station::find($capture->station_id);
        $this->assertNotNull($station);

        // Station should have the camera_model parsed
        $this->assertEquals('TEST_CAM_AUTO', $station->camera_model);
    }
}
