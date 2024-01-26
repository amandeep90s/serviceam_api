<?php

namespace App\Models\Service;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ServiceCancelProvider extends BaseModel
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
}
