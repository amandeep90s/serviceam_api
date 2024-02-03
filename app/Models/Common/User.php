<?php

namespace App\Models\Common;

use App\Traits\Encryptable;
use Illuminate\Contracts\Auth\Access\Authorizable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject, Authorizable
{
    use HasFactory, HasRoles, Encryptable, Notifiable;

    protected $connection = 'common';
    protected array $encryptable = [
        'email',
        'mobile'
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'gender',
        'email',
        'mobile',
        'picture',
        'password',
        'device_type',
        'jwt_token',
        'login_by',
        'device_token',
        'payment_mode',
        'social_unique_id',
        'device_id',
        'wallet_balance',
        'referral_unique_id',
        'user_type',
        'qrcode_url',
        'country_code',
        'company_id',
        'country_id',
        'company_id',
        'city_id',
        'state_id'
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
        'company_id',
        'jwt_token',
        'device_id',
        'device_type',
        'social_unique_id',
        'device_token',
        'stripe_cust_id',
        'otp',
        'qrcode_url',
        'referral_unique_id',
        'referal_count',
        'created_type',
        'created_by',
        'modified_type',
        'modified_by',
        'deleted_type',
        'deleted_by',
        'updated_at',
    ];

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims(): array
    {
        return [];
    }

    public function country()
    {
        return $this->belongsTo(Country::class, 'country_id', 'id');
    }

    public function state()
    {
        return $this->belongsTo(State::class, 'state_id', 'id');
    }

    public function city()
    {
        return $this->belongsTo(City::class, 'city_id', 'id');
    }

    public function scopeSearch($query, $searchText = '')
    {
        return $query
            ->where('first_name', 'like', "%" . $searchText . "%")
            ->orWhere('last_name', 'like', "%" . $searchText . "%")
            ->orWhere('email', 'like', "%" . $this->customEncrypt($searchText, config('app.db_secret')) . "%")
            ->orWhere('mobile', 'like', "%" . $this->customEncrypt($searchText, config('app.db_secret')) . "%")
            ->orWhere('wallet_balance', 'like', "%" . $searchText . "%")
            ->orWhere('rating', 'like', "%" . $searchText . "%");
    }
}
