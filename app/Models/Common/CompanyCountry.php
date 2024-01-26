<?php

namespace App\Models\Common;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CompanyCountry extends BaseModel
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

    public function companyCountryCities(): HasMany
    {
        return $this->hasMany(CompanyCity::class, 'country_id', 'country_id')->with('city');
    }

    public function scopeSearch($query, $searchText = '')
    {
        return $query
            ->whereHas('country', function ($q) use ($searchText) {
                $q->where('country_name', 'like', "%" . $searchText . "%");
            })
            ->orwhere('currency', 'like', "%" . $searchText . "%")
            ->orwhere('currency_code', 'like', "%" . $searchText . "%")
            ->orWhere('status', 'like', "%" . $searchText . "%");
    }
}
