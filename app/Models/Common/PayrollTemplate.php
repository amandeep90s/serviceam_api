<?php

namespace App\Models\Common;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PayrollTemplate extends Model
{
    protected $connection = 'common';

    protected $fillable = [
        'template_name',
        'company_id',
        'zone_id',
        'status'
    ];
    protected $hidden = [
        'created_at',
        'updated_at'
    ];

    public function zone(): BelongsTo
    {
        return $this->belongsTo(Zone::class, 'zone_id', 'id');
    }

    public function scopeSearch($query, $searchText = '')
    {
        return $query->whereHas('zone', function ($q) use ($searchText) {
            $q->where('name', 'like', "%" . $searchText . "%");
        })->Orwhere('template_name', 'like', "%" . $searchText . "%")
            ->orWhere('status', 'like', "%" . $searchText . "%");
    }
}
