<?php

namespace App\Models\Common;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Country extends BaseModel
{
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

    public function states(): HasMany
    {
        return $this->hasMany(State::class);
    }

    public function city(): HasMany
    {
        return $this->hasMany(City::class, 'country_id', 'id');
    }

    public function company_city(): HasMany
    {
        return $this->hasMany(CompanyCity::class, 'country_id', 'id');
    }

    public function bank_form(): HasMany
    {
        return $this->hasMany(CountryBankForm::class, 'country_id', 'id');
    }
}
