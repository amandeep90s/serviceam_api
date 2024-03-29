<?php

namespace App\Models\Common;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AuthLog extends Model
{
    use HasFactory;

    protected $connection = 'common';

    protected $table = 'auth_logs';

    protected $fillable = [
        'user_type',
        'user_id',
        'type',
        'data',
    ];

    public function getDataAttribute()
    {
        return json_decode($this->attributes['data']);

    }
}
