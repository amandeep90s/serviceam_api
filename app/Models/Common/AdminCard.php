<?php

namespace App\Models\Common;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class AdminCard extends BaseModel
{
    use HasFactory;

    protected $connection = 'common';

    public function scopeSearch($query, $searchText = '')
    {
        return $query->where('brand', 'like', "%" . $searchText . "%")
            ->orWhere('last_four', 'like', "%" . $searchText . "%");

    }
}
