<?php

namespace Tests\Functional\Operator;

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
        $this->markTestIncomplete();

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
}
