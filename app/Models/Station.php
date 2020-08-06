<?php

namespace App\Models;

use App\Traits\AssignUuid;
use App\Traits\Encryptable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Station extends Model
{
    use AssignUuid, Encryptable;

    const SOURCE_BRAMON = 'BRAMON';
    const SOURCE_RMS = 'RMS';

    const AVAILABLE_SOURCES = [
        self::SOURCE_BRAMON,
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
        'latitude',
        'longitude',
        'azimuth',
        'elevation',
        'fov',
        'camera_model',
        'camera_lens',
        'camera_capture',
        'active',
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [
        'created_at',
        'updated_at',
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
