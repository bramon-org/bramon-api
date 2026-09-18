<?php

namespace App\Models;

use App\Traits\AssignUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class File extends Model
{
    use AssignUuid, SoftDeletes;

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'capture_id',
        'file_hash',
        'filename',
        'type',
        'extension',
        'url',
        'captured_at',
    ];

    public function capture(): BelongsTo
    {
        return $this->belongsTo(Capture::class);
    }
}
