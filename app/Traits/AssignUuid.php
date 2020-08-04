<?php

namespace App\Traits;

use Ramsey\Uuid\Uuid;

trait AssignUuid
{
    /**
     * Assigns UUID to the record being created – just relying on
     * SQL function generate_uuid_v4() won't return the ID while creating
     * a record so we need to set it before sending to the database.
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
