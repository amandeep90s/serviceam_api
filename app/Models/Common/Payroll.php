<?php

namespace App\Models\Common;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Payroll extends Model
{
    protected $connection = 'common';

    protected $fillable = [
        'template_id',
        'company_id',
        'transaction_id',
        'status',
        'provider_id',
        'wallet',
        'zone_id',
        'payroll_type',
        'type',
        'admin_service',
        'created_at',
        'updated_at'
    ];

    public function provider(): BelongsTo
    {
        return $this->belongsTo(Provider::class, 'provider_id', 'id');
    }

    public function scopeSearch($query, $searchText = '')
    {
        return $query->where('transaction_id', 'like', "%" . $searchText . "%")
            ->orWhere('payroll_type', 'like', "%" . $searchText . "%");
    }

    public function bankDetails(): HasMany
    {
        return $this->hasmany(ProviderBankDetail::class, 'type_id', 'provider_id')->where('created_type', 'PROVIDER');
    }
}
