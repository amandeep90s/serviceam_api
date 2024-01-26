<?php

namespace App\Models\Service;

use App\Models\BaseModel;
use App\Models\Common\ProviderService;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Service extends BaseModel
{
    use HasFactory;

    protected $connection = 'service';
    protected $hidden = [
        'company_id',
        'created_by',
        'modified_type',
        'modified_by',
        'deleted_type',
        'deleted_by',
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    public function scopeSearch($query, $searchText = '')
    {
        $word = 'active';
        $word2 = 'inactive';
        if (str_contains($word, $searchText)) {
            $result = $query
                ->where('service_name', 'like', "%" . $searchText . "%")
                ->orWhere('service_status', 1);
        }
        if (str_contains($word2, $searchText)) {
            $result = $query
                ->where('service_name', 'like', "%" . $searchText . "%")
                ->orWhere('service_status', 2);
        } else {
            $result = $query
                ->where('service_name', 'like', "%" . $searchText . "%")
                ->orWhere('service_status', 'like', "%" . $searchText . "%");
        }
        return $result;
    }

    public function serviceCategory(): BelongsTo
    {
        return $this->belongsTo(ServiceCategory::class, 'service_category_id', "id");
    }

    public function servicesubCategory(): BelongsTo
    {
        return $this->belongsTo(ServiceSubcategory::class, 'service_subcategory_id', 'id');
    }

    public function subCategories(): HasMany
    {
        return $this->hasMany(ServiceSubcategory::class, 'id', 'service_subcategory_id');
    }

    public function providerservices(): HasMany
    {
        return $this->hasMany(ProviderService::class, 'service_id', 'id')
            ->where('admin_service', 'SERVICE')
            ->where('provider_id', Auth::guard('provider')->user()->id);
    }

    public function provideradminservice(): HasOne
    {
        return $this->hasOne(ProviderService::class, 'service_id', 'id')->where('admin_service', 'SERVICE');
    }

    public function servicescityprice(): HasOne
    {
        return $this->hasone(ServiceCityPrice::class, 'id', 'service_id');
    }

    public function service_city(): BelongsTo
    {
        return $this->belongsTo(ServiceCityPrice::class, 'id', 'service_id');
    }

    public function scopehistorySearch($query, $searchText = '')
    {
        $result = '';
        if ($searchText != '') {
            $result = $query->where('service_name', 'like', "%" . $searchText . "%")
                ->OrwhereHas('serviceCategory', function ($q) use ($searchText) {
                    $q->where('service_category_name', 'like', "%" . $searchText . "%");
                })
                ->OrwhereHas('servicesubCategory', function ($q) use ($searchText) {
                    $q->where('service_subcategory_name', 'like', "%" . $searchText . "%");
                });
        }
        return $result;
    }
}
