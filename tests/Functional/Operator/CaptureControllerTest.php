<?php

namespace Tests\Functional\Operator;

use App\Models\Station;
use App\Models\User;
use Exception;
use Tests\Functional\TestCase;
use Illuminate\Http\UploadedFile;

class CaptureControllerTest extends TestCase
{
    /**
     * @test
     * @return void
     * @throws Exception
     */
    public function getCaptures()
    {
        $this->authenticate(User::ROLE_OPERATOR);

        $this->get('/v1/operator/captures', ['Authorization' => 'Bearer ' . $this->user->api_token]);

        $this->assertNotEmpty($this->response->getContent());
        $this->assertResponseStatus(200);
    }

    /**
     * @test
     * @return void
     * @throws Exception
     */
    public function uploadSingleCapture()
    {
        $this->authenticate(User::ROLE_OPERATOR);

        $headers = [
            'Content-Type' => 'multipart/form-data',
            'Authorization' => 'Bearer ' . $this->user->api_token,
        ];

        $files = [
            'files' => UploadedFile::fake()->create('TLP5/2020/202006/20200607/M20200608_005550_TLP_5.avi', 5*1000)
        ];

        $servers = [];

        foreach ($headers as $k => $header) {
            $servers["HTTP_" . $k] = $header;
        }

        $this->call(
            'POST',
            '/v1/operator/captures',
            ['station_id' => $this->station->id],
            [],
            $files,
            $servers
        );

        $this->assertResponseStatus(422);
    }

    /**
     * @test
     * @return void
     * @throws Exception
     */
    public function uploadMultipleCaptures()
    {
        $this->authenticate(User::ROLE_OPERATOR);

        $headers = [
            'Content-Type' => 'multipart/form-data',
            'Authorization' => 'Bearer ' . $this->user->api_token,
        ];

        $files = [
            'files' => [
                UploadedFile::fake()->create('TLP5/2020/202006/20200607/M20200608_005550_TLP_5.avi', 5*1000),
                UploadedFile::fake()->create('TLP5/2020/202006/20200607/M20200608_005550_TLP_5.txt', 5*10),
                UploadedFile::fake()->create('TLP5/2020/202006/20200607/M20200608_005550_TLP_5.xml', 5*10),
                UploadedFile::fake()->create('TLP5/2020/202006/20200607/M20200608_005550_TLP_5A.XML', 5*10),
                UploadedFile::fake()->create('TLP5/2020/202006/20200607/M20200608_005550_TLP_5M.bmp', 5*100),
                UploadedFile::fake()->create('TLP5/2020/202006/20200607/M20200608_005550_TLP_5P.jpg', 5*1000),
                UploadedFile::fake()->create('TLP5/2020/202006/20200607/M20200608_005550_TLP_5T.jpg', 5*1000),
            ]
        ];

        $servers = [];

        foreach ($headers as $k => $header) {
            $servers["HTTP_" . $k] = $header;
        }

        $this->call(
            'POST',
            '/v1/operator/captures',
            ['station_id' => $this->station->id],
            [],
            $files,
            $servers
        );

        $this->assertResponseStatus(201);
    }


    /**
     * @test
     * @return void
     * @throws Exception
     */
    public function uploadMultipleCapturesFromRMS()
    {
        $this->authenticate(User::ROLE_OPERATOR);

        $this->station->source = Station::SOURCE_RMS;
        $this->station->save();

        $headers = [
            'Content-Type' => 'multipart/form-data',
            'Authorization' => 'Bearer ' . $this->user->api_token,
        ];

        $files = [
            'files' => [
                UploadedFile::fake()->create('BR0004_20200302_220730_468533/BR0004_20200302_220730_468533.csv', 5*1000),
                UploadedFile::fake()->create('BR0004_20200302_220730_468533/BR0004_20200302_220730_468533_CAPTURED_thumbs.jpg', 5*10),
                UploadedFile::fake()->create('BR0004_20200302_220730_468533/BR0004_20200302_220730_468533_DETECTED_thumbs.jpg', 5*10),
                UploadedFile::fake()->create('BR0004_20200302_220730_468533/BR0004_20200302_220730_468533_fieldsums.png', 5*10),
                UploadedFile::fake()->create('BR0004_20200302_220730_468533/BR0004_20200302_220730_468533_fieldsums_noavg.png', 5*100),
                UploadedFile::fake()->create('BR0004_20200302_220730_468533/CALSTARS_BR0004_20200302_220730_468533.txt', 5*1000),
                UploadedFile::fake()->create('BR0004_20200302_220730_468533/FF_BR0004_20200303_080136_096_0960768.fits', 5*1000),
                UploadedFile::fake()->create('BR0004_20200302_220730_468533/FR_BR0004_20200303_073658_187_0921088.bin', 5*1000),
                UploadedFile::fake()->create('BR0004_20200302_220730_468533/FS_BR0004_20200302_220730_468533_fieldsums.tar.bz2', 5*1000),
                UploadedFile::fake()->create('BR0004_20200302_220730_468533/FTPdetectinfo_BR0004_20200302_220730_468533.txt', 5*1000),
                UploadedFile::fake()->create('BR0004_20200302_220730_468533/FTPdetectinfo_BR0004_20200302_220730_468533_uncalibrated.txt', 5*1000),
                UploadedFile::fake()->create('BR0004_20200302_220730_468533/mask.bmp', 5*1000),
                UploadedFile::fake()->create('BR0004_20200302_220730_468533/platepar_cmn2010.cal', 5*1000),
                UploadedFile::fake()->create('BR0004_20200302_220730_468533/platepars_all_recalibrated.json', 5*1000),
            ]
        ];

        $servers = [];

        foreach ($headers as $k => $header) {
            $servers["HTTP_" . $k] = $header;
        }

        $this->call(
            'POST',
            '/v1/operator/captures',
            ['station_id' => $this->station->id],
            [],
            $files,
            $servers
        );

        $this->assertResponseStatus(201);
    }
}
