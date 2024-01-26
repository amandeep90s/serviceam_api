<?php

namespace App\Models\Common;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CmsPage extends BaseModel
{
    use HasFactory;

    protected $connection = 'common';

    protected $fillable = [
        'page_name',
        'description',
        'status',
        'page_name',
        'content',
        'status',
    ];
}
