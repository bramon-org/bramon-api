<?php

namespace App\Models;

use App\Traits\AssignUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;

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
        'user_id',
        'station_id',
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
        'user_id',
        'created_at',
        'updated_at'
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
            $builder->orderBy('captured_at', 'desc');
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
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return HasMany
     */
    public function files()
    {
        return $this->hasMany(File::class);
    }
}
