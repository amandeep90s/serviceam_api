<?php

namespace App\Models\Common;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Promocode extends BaseModel
{
    protected $connection = 'common';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'promo_code',
        'percentage',
        'max_amount',
        'expiration',
        'promo_description',
        'service'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
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
    
    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected array $dates = ['deleted_at'];

    public function promousage(): HasMany
    {
        return $this->hasMany(PromocodeUsage::class, 'promocode_id');
    }

    public function scopeSearch($query, $searchText = '')
    {
        return $query
            ->where('promo_code', 'like', "%" . $searchText . "%")
            ->orWhere('percentage', 'like', "%" . $searchText . "%")
            ->orWhere('max_amount', 'like', "%" . $searchText . "%")
            ->orWhere('expiration', 'like', "%" . $searchText . "%");
    }
}
