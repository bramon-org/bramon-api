<?php

namespace App\Helpers;

class Geohash
{
    // Base32 map
    private const BASE32 = '0123456789bcdefghjkmnpqrstuvwxyz';

    public static function encode(float $lat, float $lng, int $precision = 5): string
    {
        $latInterval = [-90.0, 90.0];
        $lngInterval = [-180.0, 180.0];

        $geohash = '';
        $isEven = true;
        $bit = 0;
        $ch = 0;

        while (strlen($geohash) < $precision) {
            if ($isEven) {
                $mid = ($lngInterval[0] + $lngInterval[1]) / 2;
                if ($lng > $mid) {
                    $ch |= 1 << (4 - $bit);
                    $lngInterval[0] = $mid;
                } else {
                    $lngInterval[1] = $mid;
                }
            } else {
                $mid = ($latInterval[0] + $latInterval[1]) / 2;
                if ($lat > $mid) {
                    $ch |= 1 << (4 - $bit);
                    $latInterval[0] = $mid;
                } else {
                    $latInterval[1] = $mid;
                }
            }

            $isEven = !$isEven;

            if ($bit < 4) {
                $bit++;
            } else {
                $geohash .= self::BASE32[$ch];
                $bit = 0;
                $ch = 0;
            }
        }

        return $geohash;
    }
}
