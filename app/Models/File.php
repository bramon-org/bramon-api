<?php

namespace App\Models;

use App\Traits\AssignUuid;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class File extends Model
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
        'capture_id',
        'filename',
        'type',
        'extension',
        'url',
        'captured_at',
    ];

    /**
     * The relations to eager load on every query.
     *
     * @var array
     */
    protected $with = [];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array
     */
    protected $hidden = [
        'id',
        'capture_id',
        'created_at',
        'updated_at'
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'files' => 'array',
    ];

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
     * Get the capture related.
     *
     * @return BelongsTo
     */
    public function capture(): BelongsTo
    {
        return $this->belongsTo(Capture::class);
    }

    /**
     * Cast to url attribute.
     *
     * @param $value
     * @return string
     */
    public function getUrlAttribute($value): string
    {
        return env('STORAGE_URL') . $value;
    }
}
