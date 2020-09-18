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
        // $this->markTestSkipped();

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
        // $this->markTestSkipped();

        $this->authenticate(User::ROLE_OPERATOR);

        $this->station->source = Station::SOURCE_RMS;
        $this->station->save();

        $headers = [
            'Content-Type' => 'multipart/form-data',
            'Authorization' => 'Bearer ' . $this->user->api_token,
        ];

        $files = [
            'files' => [
                UploadedFile::fake()->create('BR0004_20200623_205351_612441_detected.tar.bz2', 5*1000),
                UploadedFile::fake()->create('BR0004_20200615_205213_315369_detected.tar.bz2', 5*1000),
                UploadedFile::fake()->create('BR0004_20200130_223427_830080_detected.tar.bz2', 5*1000),
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
    public function deleteCaptures()
    {
        $this->authenticate(User::ROLE_OPERATOR);

        $data = [
            'station_id' => $this->station->id,
            'files' => [
                'M20200608_005550_TLP_5.avi',
                'M20200608_005550_TLP_5.txt',
                'M20200608_005550_TLP_5.xml',
                'M20200608_005550_TLP_5A.XML',
                'M20200608_005550_TLP_5M.bmp',
                'M20200608_005550_TLP_5P.jpg',
                'M20200608_005550_TLP_5T.jpg',
            ]
        ];

        $this->delete('/v1/operator/captures', $data, ['Authorization' => 'Bearer ' . $this->user->api_token]);

        $this->assertResponseStatus(204);
    }
}
