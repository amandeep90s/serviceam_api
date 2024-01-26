<?php

namespace App\Models\Common;

use Illuminate\Database\Eloquent\Model;

class ServiceArea extends Model
{
    protected $connection = 'common';

    protected $fillable = [
        'provider_id',
        'type',
        'miles',
        'location_name',
        'ranges'
    ];
    
    protected $hidden = [
        'created_at',
        'updated_at'
    ];
}

