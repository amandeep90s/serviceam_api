<?php

namespace App\Models\Common;

use App\Models\BaseModel;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserWallet extends BaseModel
{
    protected $connection = 'common';

    protected $appends = ['created_time'];

    protected $hidden = [
        'created_type',
        'created_by',
        'modified_type',
        'modified_by',
        'deleted_type',
        'deleted_by',
        'updated_at',
        'deleted_at'
    ];

    public function payment_log(): BelongsTo
    {
        return $this->belongsTo(PaymentLog::class, 'transaction_id', 'id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function scopeSearch($query, $searchText = '')
    {
        if ($searchText != '') {
            return $query
                ->where('transaction_alias', 'like', "%" . $searchText . "%")
                ->orWhere('transaction_desc', 'like', "%" . $searchText . "%")
                ->orWhere('amount', 'like', "%" . $searchText . "%")
                ->orWhere('type', 'like', "%" . $searchText . "%");
        }
        return null;
    }

    public function getCreatedTimeAttribute(): string
    {
        return isset($this->attributes['created_at']) ?
            (Carbon::now()->diffForHumans($this->attributes['created_at'], 'UTC')) :
            '';
    }
}
