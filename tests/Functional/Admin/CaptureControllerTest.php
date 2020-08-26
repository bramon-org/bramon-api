<?php

namespace Tests\Functional\Admin;

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
        $this->authenticate(User::ROLE_ADMIN);

        $this->get('/v1/admin/captures', ['Authorization' => 'Bearer ' . $this->user->api_token]);

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
        $this->authenticate(User::ROLE_ADMIN);

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
            '/v1/admin/captures',
            [
                'station_id' => $this->station->id,
                'user_id' => $this->user->id,
            ],
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
        $this->markTestSkipped();

        $this->authenticate(User::ROLE_ADMIN);

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
            '/v1/admin/captures',
            [
                'station_id' => $this->station->id,
                'user_id' => $this->user->id,
            ],
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
        $this->authenticate(User::ROLE_ADMIN);

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

        $this->delete('/v1/admin/captures', $data, ['Authorization' => 'Bearer ' . $this->user->api_token]);

        $this->assertResponseStatus(204);
    }
}
