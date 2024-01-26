<?php

namespace App\Models\Common;

use App\Models\BaseModel;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Auth;

class ProviderWallet extends BaseModel
{
    protected $connection = 'common';

    protected $appends = ['created_time'];

    protected $hidden = [
        'created_type', 'created_by', 'modified_type', 'modified_by', 'deleted_type', 'deleted_by', 'updated_at', 'deleted_at'
    ];

    public function payment_log(): BelongsTo
    {
        return $this->belongsTo(PaymentLog::class, 'transaction_id', 'id');
    }

    public function provider(): BelongsTo
    {
        return $this->belongsTo(Provider::class, 'provider_id', 'id');
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(ProviderWallet::class, 'transaction_alias', 'transaction_alias');
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
        $timezone = isset(Auth::guard('provider')->user()->state_id) ? State::find(Auth::guard('provider')->user()->state_id) : "UTC";
        return (isset($this->attributes['created_at'])) ? (Carbon::createFromFormat('Y-m-d H:i:s', $this->attributes['created_at'], 'UTC'))->setTimezone($timezone->timezone)->format('Y-m-d H:i:s') : '';

    }
}
