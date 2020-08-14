<?php

namespace Tests\Unit\Drivers;

use App\Drivers\RmsDriver;
use Tests\Unit\TestCase;

class RmsDriverTest extends TestCase
{
    /**
     * @test
     *
     * @dataProvider validFilenames()
     *
     * @param string
     */
    public function getFileDateMustReturnDateTimeImmutableObject($filename)
    {
        $result = (new RmsDriver)->getFileDate($filename);

        $this->assertInstanceOf(\DateTimeImmutable::class, $result);
    }

    public function validFilenames()
    {
        return [
            [
                'BR0005_20200811_211456_509757_detected.tar.bz2',
            ],
        ];
    }

    /**
     * @test
     *
     * @dataProvider invalidFilenames()
     *
     * @param string
     *
     * @expectedException \InvalidArgumentException
     */
    public function getFileDateMustReturnFalseWhenInvalidFilename($filename)
    {
        (new RmsDriver)->getFileDate($filename);
    }

    /**
     * @return \string[][]
     */
    public function invalidFilenames()
    {
        return [
            [
                'M20200608_005550_TLP_5.avi',
            ],
            [
                'M20200608_005550_TLP_5.txt',
            ],
            [
                'M20200608_005550_TLP_5.xml',
            ],
            [
                'M20200608_005550_TLP_5A.XML',
            ],
            [
                'M20200608_005550_TLP_5M.bmp',
            ],
            [
                'M20200608_005550_TLP_5P.jpg',
            ],
            [
                'M20200608_005550_TLP_5T.jpg',
            ]
        ];
    }
}
