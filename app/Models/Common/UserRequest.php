<?php

namespace App\Models\Common;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserRequest extends BaseModel
{
    protected $connection = 'common';
    protected $appends = ['request'];

    protected $hidden = [
        'request_data',
        'created_by',
        'modified_type',
        'modified_by',
        'deleted_type',
        'deleted_by',
        'updated_at',
        'deleted_at'
    ];

    /**
     * The user who created the request.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function service(): BelongsTo
    {
        return $this->belongsTo(AdminService::class, 'admin_service', 'admin_service');
    }

    /**
     * The provider assigned to the request.
     */
    public function provider(): BelongsTo
    {
        return $this->belongsTo(Provider::class, 'provider_id');
    }

    public function getRequestAttribute()
    {
        return json_decode($this->attributes['request_data']);

    }
}
