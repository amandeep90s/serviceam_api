<?php

namespace App\Models\Common;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProviderVehicle extends BaseModel
{
    protected $connection = 'common';

    protected $fillable = [
        'vehicle_service_id',
        'vehicle_model',
        'vehicle_no',
    ];

    protected $hidden = [
        'created_type',
        'created_by',
        'modified_type',
        'modified_by',
        'deleted_type',
        'deleted_by',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    public function provider_service(): BelongsTo
    {
        return $this->belongsTo(ProviderService::class, 'id', 'provider_vehicle_id')->with('admin_service');
    }
}
