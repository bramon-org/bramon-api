<?php

namespace App\Models;

use App\Traits\AssignUuid;
use App\Traits\Encryptable;
use Illuminate\Auth\Authenticatable;
use Laravel\Lumen\Auth\Authorizable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;

class User extends Model implements AuthenticatableContract, AuthorizableContract
{
    use Authenticatable, Authorizable, AssignUuid, Encryptable;

    const ROLE_ADMIN = 'admin';
    const ROLE_OPERATOR = 'operator';
    const ROLE_EDITOR = 'editor';

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
        'email',
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [
        'password',
    ];

    /**
     * The attributes that encrypted on the database.
     * @var array
     */
    protected $encryptable = [
        'api_token',
    ];
}
