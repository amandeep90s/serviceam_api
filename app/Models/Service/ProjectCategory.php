<?php

namespace App\Models\Service;

use App\Models\BaseModel;
use App\Models\Common\ProviderService;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProjectCategory extends BaseModel
{
    use HasFactory;

    protected $connection = 'service';

    protected $table = 'service_projectcategories';

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

    public function scopeSearch($query, $searchText = '')
    {
        $word = 'active';
        $word2 = 'inactive';
        if (str_contains($word, $searchText)) {
            $result = $query
                ->where('service_projectcategory_name', 'like', "%" . $searchText . "%")
                ->orWhere('service_projectcategory_order', 'like', "%" . $searchText . "%")
                ->orWhere('service_projectcategory_status', 1);
        }
        if (str_contains($word2, $searchText)) {
            $result = $query
                ->where('service_projectcategory_name', 'like', "%" . $searchText . "%")
                ->orWhere('service_projectcategory_order', 'like', "%" . $searchText . "%")
                ->orWhere('service_projectcategory_status', 2);
        } else {
            $result = $query
                ->where('service_projectcategory_name', 'like', "%" . $searchText . "%")
                ->orWhere('service_projectcategory_order', 'like', "%" . $searchText . "%")
                ->orWhere('service_projectcategory_status', 'like', "%" . $searchText . "%");
        }
        return $result;
    }

    public function serviceCategory(): BelongsTo
    {
        return $this->belongsTo(ServiceCategory::class);
    }

    public function service(): HasMany
    {
        return $this->hasMany(Service::class, 'service_subcategory_id', 'id');
    }

    public function providerservicesubcategory(): HasMany
    {
        return $this->hasMany(ProviderService::class, 'sub_category_id', 'id')->where('admin_service', 'SERVICE')->where('provider_id', Auth::guard('provider')->user()->id);
    }
}
