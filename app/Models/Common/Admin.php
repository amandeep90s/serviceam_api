<?php

namespace App\Models\Common;

use App\Traits\Encryptable;
use Illuminate\Contracts\Auth\Access\Authorizable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use Tymon\JWTAuth\Contracts\JWTSubject;

class Admin extends Authenticatable implements JWTSubject, Authorizable
{
    use HasFactory, HasRoles, Encryptable, Notifiable;

    protected $connection = 'common';

    protected array $encryptable = [
        'email', 'mobile'
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'mobile',
        'country_code',
        'password',
        'company_id',
        'picture',
        'type',
        'country_id',
        'city_id',
        "zone_id"
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token', 'company_id', 'created_at', 'updated_at',
    ];

    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier(): mixed
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims(): array
    {
        return [];
    }

    public function scopeSearch($query, $searchText = '')
    {
        return $query
            ->where('name', 'like', "%" . $searchText . "%")
            ->orWhere('email', 'like', "%" . $this->customEncrypt($searchText, config('app.db_secret')) . "%");
    }
}
