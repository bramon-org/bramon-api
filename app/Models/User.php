<?php

namespace App\Models;

use App\Traits\AssignUuid;
use App\Traits\Encryptable;
use Exception;
use Illuminate\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laravel\Lumen\Auth\Authorizable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Ramsey\Uuid\Uuid;

class User extends Model implements AuthenticatableContract, AuthorizableContract
{
    use Authenticatable, Authorizable, AssignUuid, Encryptable, SoftDeletes;

    const ROLE_ADMIN = 'admin';
    const ROLE_OPERATOR = 'operator';
    const ROLE_EDITOR = 'editor';

    const AVAILABLE_ROLES = [
        self::ROLE_EDITOR,
        self::ROLE_OPERATOR,
        self::ROLE_ADMIN,
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
        'mobile_phone',
        'city',
        'state',
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [
        'email',
        'password',
        'mobile_phone',
        'api_token',
        'last_request_ip',
        'last_request_at',
        'active',
        'role',
        'created_at',
        'updated_at',
    ];

    /**
     * The attributes that encrypted on the database.
     * @var array
     */
    protected $encryptable = [
        'password',
        'mobile_phone',
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
     * Generate an user password.
     *
     * @return string
     * @throws Exception
     */
    public function generatePassword(): string
    {
        return substr(password_hash(Uuid::uuid4()->toString(), PASSWORD_BCRYPT), 0, 8);
    }

    /**
     * Generate an user api token.
     *
     * @return string
     */
    public function generateApiToken(): string
    {
        return uniqid(uniqid(), true);
    }

    /**
     * @return HasMany
     */
    public function stations(): HasMany
    {
        return $this->hasMany(Station::class);
    }
}
