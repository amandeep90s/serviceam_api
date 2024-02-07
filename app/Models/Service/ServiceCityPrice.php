<?php

namespace App\Models\Service;

use App\Models\Common\City;
use App\Models\Common\Country;
use App\Models\Common\ProviderService;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ServiceCityPrice extends Model
{
    use HasFactory;

    protected $connection = 'service';

    protected $casts = [
        'base_fare' => 'float',
        'base_distance' => 'float',
        'per_miles' => 'float',
        'per_mins' => 'float',
        'minimum_fare' => 'float',
        'commission' => 'float',
        'fleet_commission' => 'float',
        'tax' => 'float',
        'cancellation_charge' => 'float'
    ];

    protected $hidden = [
        'company_id',
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

    public function provider_service(): HasMany
    {
        return $this->hasMany(ProviderService::class, 'service_id', 'id')->with('provider');
    }

    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class, 'country_id', 'id');
    }

    public function city(): BelongsTo
    {
        return $this->belongsTo(City::class, 'city_id', 'id');
    }

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class, 'service_id', 'id');
    }
}
