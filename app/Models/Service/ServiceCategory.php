<?php

namespace App\Models\Service;

use App\Models\BaseModel;
use App\Models\Common\ProviderService;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ServiceCategory extends BaseModel
{
    use HasFactory;

    protected $connection = 'service';

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
     * @param $query
     * @param $searchText
     * @return mixed
     */
    public function scopeSearch($query, $searchText = '')
    {
        $word = 'active';
        $word2 = 'inactive';
        if (str_contains($word, $searchText)) {
            $result = $query
                ->where('service_category_name', 'like', "%" . $searchText . "%")
                ->orWhere('service_category_order', 'like', "%" . $searchText . "%")
                ->orWhere('service_category_status', 1);
        }
        if (str_contains($word2, $searchText)) {
            $result = $query
                ->where('service_category_name', 'like', "%" . $searchText . "%")
                ->orWhere('service_category_order', 'like', "%" . $searchText . "%")
                ->orWhere('service_category_status', 2);
        } else {
            $result = $query
                ->where('service_category_name', 'like', "%" . $searchText . "%")
                ->orWhere('service_category_order', 'like', "%" . $searchText . "%")
                ->orWhere('service_category_status', 'like', "%" . $searchText . "%");
        }
        return $result;
    }

    public function providerservicecategory(): HasMany
    {
        return $this->hasMany(ProviderService::class, 'category_id', 'id')->where('admin_service', 'Service')->where('provider_id', Auth::guard('provider')->user()->id);
    }


    public function subcategories(): HasMany
    {
        return $this->hasMany(ServiceSubCategory::class, 'service_category_id', 'id');
    }

    public function services(): HasMany
    {
        return $this->hasMany(Service::class);
    }
}
