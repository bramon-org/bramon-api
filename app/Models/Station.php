<?php

namespace App\Models;

use App\Traits\AssignUuid;
use App\Traits\Encryptable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Station extends Model
{
    use AssignUuid, Encryptable, SoftDeletes;

    const SOURCE_UFO = 'UFO';
    const SOURCE_RMS = 'RMS';

    const AVAILABLE_SOURCES = [
        self::SOURCE_UFO,
        self::SOURCE_RMS,
    ];

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
        'name',
        'user_id',
        'latitude',
        'longitude',
        'azimuth',
        'elevation',
        'fov',
        'camera_model',
        'camera_lens',
        'camera_capture',
        'active',
        'source',
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [
        'user_id',
        'created_at',
        'updated_at',
        'deleted_at',
        'user_id',
    ];

    /**
     * The relations to eager load on every query.
     *
     * @var array
     */
    protected $with = [
        'user',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'active' => 'bool'
    ];

    /**
     * The attributes that encrypted on the database.
     * @var array
     */
    protected $encryptable = [];

    /**
     * The "booting" method of the model.
     *
     * @return void
     */
    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope('created_at', function (Builder $builder) {
            $builder->orderBy('created_at', 'desc')
                    ->orderBy('name', 'asc');
        });
    }

    /**
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
