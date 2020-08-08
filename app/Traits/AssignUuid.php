<?php

namespace App\Traits;

use Ramsey\Uuid\Uuid;

trait AssignUuid
{
    /**
     * Assigns UUID to the record being created.
     */
    public static function bootAssignUuid()
    {
        static::creating(function ($record) {
            if (!isset($record->id)) {
                $record->id = Uuid::uuid4()->toString();
            }
        });
    }
}
