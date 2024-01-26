<?php

namespace App\Models\Common;

use App\Models\Service\ServiceCityPrice;
use App\Traits\Encryptable;
use Illuminate\Contracts\Auth\Access\Authorizable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use Tymon\JWTAuth\Contracts\JWTSubject;

class Provider extends Authenticatable implements JWTSubject, Authorizable
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
        'email',
        'password',
        'country_code',
        'country_id',
        'mobile',
        'address',
        'picture',
        'gender',
        'jwt_token',
        'status',
        'is_online',
        'is_service',
        'is_document',
        'is_bankdetail',
        'latitude',
        'longitude',
        'status',
        'avatar',
        'social_unique_id',
        'fleet',
        'login_by',
        'company_id',
        'referral_unique_id',
        'rating',
        'zipcode',

    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */

    protected $appends = ['current_ride_vehicle', 'current_store'];

    protected $hidden = [
        'company_id',
        'password',
        'remember_token',
        'email_verified_at',
        'jwt_token',
        'created_type',
        'created_by',
        'modified_type',
        'modified_by',
        'deleted_type',
        'deleted_by',
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    public function getCurrentRideVehicleAttribute()
    {
        return (@$this->request()->first()->request_data != null) ?
            isset(json_decode($this->request()->first()->request_data)->provider_service_id) ?
                json_decode($this->request()->first()->request_data)->provider_service_id :
                null :
            null;
    }

    public function request(): HasOne
    {
        return $this->hasOne(UserRequest::class, 'provider_id', 'id');
    }

    public function getCurrentStoreAttribute()
    {
        return (@$this->request()->first()->request_data != null) ? isset(json_decode($this->request()->first()->request_data)->store_id) ? json_decode($this->request()->first()->request_data)->store_id : null : null;
    }

    public function document($id): Model
    {
        return $this->hasOne(ProviderDocument::class)->where('document_id', $id)->first();
    }

    public function totaldocument(): HasMany
    {
        return $this->hasmany(ProviderDocument::class, 'provider_id');
    }


    public function request_filter(): HasMany
    {
        return $this->hasMany(RequestFilter::class, 'provider_id');
    }

    public function providerservice(): HasMany
    {
        return $this->hasMany(ProviderService::class, 'provider_id');
    }

    public function provider_vehicle(): HasMany
    {
        return $this->hasMany(ProviderVehicle::class, 'provider_id');
    }

    public function service(): HasOne
    {
        return $this->hasOne(ProviderService::class, 'provider_id');
    }

    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class, 'country_id', 'id');
    }

    public function state(): BelongsTo
    {
        return $this->belongsTo(State::class, 'state_id', 'id');
    }

    public function admin(): BelongsTo
    {
        return $this->belongsTo(Admin::class, 'admin_id', 'id');
    }

    public function city(): BelongsTo
    {
        return $this->belongsTo(City::class, 'city_id', 'id');
    }

    public function rating(): BelongsTo
    {
        return $this->belongsTo(Rating::class, 'id', 'provider_id');
    }

    public function service_city(): HasOne
    {
        return $this->hasOne(ServiceCityPrice::class, 'city_id', 'city_id')->with('city');
    }

    public function current_vehicle(): HasOne
    {
        return $this->hasOne(ProviderService::class, 'provider_id', 'current_ride_vehicle');
    }

    public function scopeSearch($query, $searchText = '')
    {
        return $query
            ->where('first_name', 'like', "%" . $searchText . "%")
            ->orWhere('last_name', 'like', "%" . $searchText . "%")
            ->orWhere('email', 'like', "%" . $this->cusencrypt($searchText, env('DB_SECRET')) . "%")
            ->orWhere('mobile', 'like', "%" . $this->cusencrypt($searchText, env('DB_SECRET')) . "%")
            ->orWhere('wallet_balance', 'like', "%" . $searchText . "%")
            ->orWhere('rating', 'like', "%" . $searchText . "%");
    }

    public function scopeProviderSearch($query, $searchText = '', $type = '')
    {
        return $query
            ->with(['service' => function ($q) use ($searchText, $type) {
                if ($type == "ORDER") {
                    $q->where('admin_service', 'ORDER');
                    $q->where('category_id', $searchText);
                }
            }]);
    }

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims(): array
    {
        return [];
    }
}






