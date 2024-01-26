<?php

namespace App\Models\Common;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Menu extends BaseModel
{
    protected $connection = 'common';

    protected $fillable = [
        'bg_color',
        'icon',
        'title',
        'admin_service',
        'menu_type_id',
        'company_id',
        'sort_order'
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
        'updated_at', 'deleted_at'
    ];

    public function service(): BelongsTo
    {
        return $this->belongsTo(AdminService::class, 'admin_service', 'admin_service');
    }

    public function cities(): HasMany
    {
        return $this->hasMany(MenuCity::class);
    }

    public function adminservice(): BelongsTo
    {
        return $this->belongsTo(AdminService::class, 'admin_service', 'admin_service');
    }

    public function scopeSearch($query, $searchText = '')
    {
        return $query
            ->whereHas('adminservice', function ($q) use ($searchText) {
                $q->where('admin_service', 'like', "%" . $searchText . "%");
            })
            ->orWhere('title', 'like', "%" . $searchText . "%");
    }
}
