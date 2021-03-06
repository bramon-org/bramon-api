<?php

namespace App\Models;

use App\Traits\AssignUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletes;

class Capture extends Model
{
    use AssignUuid, SoftDeletes;

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
        'station_id',

        'capture_hash',

        'class',

        'fs',
        'fe',
        'sec',
        'av',
        'mag',

        'cdeg',
        'cdegmax',

        'av1',
        'azm',
        'evm',

        'ra1',
        'ra2',
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
        'dist1',
        'dist2',

        'captured_at',
    ];

    /**
     * The relations to eager load on every query.
     *
     * @var array
     */
    protected $with = [
        'station',
        'files',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array
     */
    protected $hidden = [
        'station_id',
        'created_at',
        'updated_at',
        'deleted_at',
        'capture_hash',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [];

    /**
     * The "booting" method of the model.
     *
     * @return void
     */
    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope('captured_at', function (Builder $builder) {
            $builder->orderByDesc('captures.captured_at');
        });
    }

    /**
     * @return BelongsTo
     */
    public function station(): BelongsTo
    {
        return $this->belongsTo(Station::class);
    }

    /**
     * @return HasMany
     */
    public function files()
    {
        return $this->hasMany(File::class);
    }
}
