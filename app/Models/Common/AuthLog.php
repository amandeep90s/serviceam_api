<?php

namespace App\Models\Common;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class AuthLog extends BaseModel
{
    use HasFactory;

    protected $connection = 'common';

    protected $fillable = [
        'user_type', 'user_id', 'type', 'data'
    ];

    public function getDataAttribute()
    {
        return json_decode($this->attributes['data']);

    }
}
