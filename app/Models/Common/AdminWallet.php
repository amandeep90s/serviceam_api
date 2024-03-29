<?php

namespace App\Models\Common;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class AdminWallet extends BaseModel
{
    use HasFactory;

    protected $connection = 'common';

    protected $hidden = [
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
        return $query->where('transaction_id', 'like', "%" . $searchText . "%")
            ->orWhere('updated_at', 'like', "%" . $searchText . "%")
            ->orWhere('description', 'like', "%" . $searchText . "%")
            ->orWhere('status', 'like', "%" . $searchText . "%")
            ->orWhere('amount', 'like', "%" . $searchText . "%");
    }
}
