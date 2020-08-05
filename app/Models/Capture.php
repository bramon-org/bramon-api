<?php

namespace App\Models;

use App\Traits\AssignUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Capture extends Model
{
    use AssignUuid;

    /**
     * @var string
     */
    protected $keyType = 'string';

    /**
     * Indicates if the IDs are auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'class',
        'mag',
        'sec',
        'lat1',
        'lat2',
        'lng1',
        'lng2',
        'Vo',
        'az1',
        'az2',
        'ev1',
        'ev2',
        'h1',
        'h2',
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [
        'created_at',
        'updated_at'
    ];

    protected $casts = [
        'files' => 'array',
    ];

    /**
     * @return BelongsTo
     */
    public function station(): BelongsTo
    {
        return $this->belongsTo(Station::class);
    }
}
