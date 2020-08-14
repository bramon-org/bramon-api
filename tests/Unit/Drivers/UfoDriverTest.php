<?php

namespace Tests\Unit\Drivers;

use App\Drivers\UfoDriver;
use Tests\Unit\TestCase;

class UfoDriverTest extends TestCase
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
        $result = (new UfoDriver)->getFileDate($filename);

        $this->assertInstanceOf(\DateTimeImmutable::class, $result);
    }

    /**
     * @return \string[][]
     */
    public function validFilenames()
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
        (new UfoDriver)->getFileDate($filename);
    }

    public function invalidFilenames()
    {
        return [
            [
                'BR0004_20200302_220730_468533.csv',
            ],
            [
                'BR0004_20200302_220730_468533_CAPTURED_thumbs.jpg',
            ],
            [
                'BR0004_20200302_220730_468533_DETECTED_thumbs.jpg',
            ],
            [
                'BR0004_20200302_220730_468533_fieldsums.png',
            ],
            [
                'BR0004_20200302_220730_468533_fieldsums_noavg.png',
            ],
            [
                'CALSTARS_BR0004_20200302_220730_468533.txt',
            ],
            [
                'FF_BR0004_20200303_080136_096_0960768.fits',
            ],
            [
                'FR_BR0004_20200303_073658_187_0921088.bin',
            ],
            [
                'FS_BR0004_20200302_220730_468533_fieldsums.tar.bz2',
            ],
            [
                'FTPdetectinfo_BR0004_20200302_220730_468533.txt',
            ],
            [
                'FTPdetectinfo_BR0004_20200302_220730_468533_uncalibrated.txt',
            ],
            [
                'mask.bmp',
            ],
            [
                'platepar_cmn2010.cal',
            ],
            [
                'platepars_all_recalibrated.json',
            ],
        ];
    }
}
