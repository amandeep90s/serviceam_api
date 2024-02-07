<?php

namespace App\Models\Common;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class CompanyCity extends BaseModel
{
    use HasFactory;

    protected $connection = 'common';

    protected $hidden = [
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

    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class, 'country_id', 'id');
    }

    public function state(): BelongsTo
    {
        return $this->belongsTo(State::class, 'state_id', 'id');
    }

    public function city(): BelongsTo
    {
        return $this->belongsTo(City::class, 'city_id', 'id');
    }

    public function city_list(): BelongsTo
    {
        return $this->belongsTo(City::class, 'city_id', 'id');
    }

    public function city_service(): HasMany
    {
        return $this->hasMany(CompanyCityAdminService::class, 'company_city_service_id', 'id')->with('admin_service');
    }

    public function menu_city(): HasOne
    {
        return $this->hasone(MenuCity::class, 'city_id', 'city_id');
    }

    public function scopeSearch($query, $searchText = '')
    {
        return $query
            ->whereHas('country', function ($q) use ($searchText) {
                $q->where('country_name', 'like', "%" . $searchText . "%");
            })
            ->orwhereHas('state', function ($q) use ($searchText) {
                $q->where('state_name', 'like', "%" . $searchText . "%");
            })
            ->orwhereHas('city', function ($q) use ($searchText) {
                $q->where('city_name', 'like', "%" . $searchText . "%");
            })
            ->orWhere('status', 'like', "%" . $searchText . "%");
    }
}
