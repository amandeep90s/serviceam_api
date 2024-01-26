<?php

namespace App\Models\Service;

use App\Models\BaseModel;
use App\Models\Common\Promocode;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ServiceRequestPayment extends BaseModel
{
    use HasFactory;

    protected $connection = 'service';

    protected $hidden = [
        'company_id',
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

    protected $casts = [
        'fixed' => 'float',
        'distance' => 'float',
        'minute' => 'float',
        'hour' => 'float',
        'commision' => 'float',
        'commision_percent' => 'float',
        'fleet' => 'float',
        'fleet_percent' => 'float',
        'discount' => 'float',
        'discount_percent' => 'float',
        'tax' => 'float',
        'tax_percent' => 'float',
        'wallet' => 'float',
        'extra_charges' => 'float',
        'extra_charges_notes' => 'float',
        'surge' => 'float',
        'cash' => 'float',
        'card' => 'float',
        'tips' => 'float',
        'total' => 'float',
        'payable' => 'float',
        'provider_pay' => 'float',
    ];

    public function promoCode(): BelongsTo
    {
        return $this->belongsTo(Promocode::class, 'promocode_id');
    }
}
