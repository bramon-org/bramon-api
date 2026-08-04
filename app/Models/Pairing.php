<?php

namespace App\Models;

use App\Traits\AssignUuid;
use Illuminate\Database\Eloquent\Model;

class Pairing extends Model
{
    use AssignUuid;

    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'capture_a_id',
        'capture_b_id',
        'time_difference_seconds',
        'distance_km',
        'azimuth_diff',
        'elevation_diff',
        'fov_diff',
        'pairing_date',
    ];

    protected $casts = [
        'time_difference_seconds' => 'integer',
        'distance_km' => 'float',
        'azimuth_diff' => 'float',
        'elevation_diff' => 'float',
        'fov_diff' => 'float',
        'pairing_date' => 'date',
    ];

    public function captureA()
    {
        return $this->belongsTo(Capture::class, 'capture_a_id');
    }

    public function captureB()
    {
        return $this->belongsTo(Capture::class, 'capture_b_id');
    }
}
