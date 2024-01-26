<?php

namespace App\Models\Common;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CompanyCityAdminService extends BaseModel
{
    use HasFactory;

    protected $connection = 'common';

    public function admin_service(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(AdminService::class, 'admin_service', 'admin_service');
    }
}
