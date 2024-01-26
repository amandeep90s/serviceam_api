<?php

namespace App\Models\Common;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Rating extends BaseModel
{
    protected $connection = 'common';

    protected $fillable = [
        'admin_service',
        'request_id',
        'user_id',
        'provider_id',
        'company_id',
        'user_rating',
        'provider_rating',
        'store_rating',
        'user_comment',
        'provider_comment',
        'store_comment'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function provider(): BelongsTo
    {
        return $this->belongsTo(Provider::class, 'provider_id');
    }
}
