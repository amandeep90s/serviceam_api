<?php

namespace App\Models\Common;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProviderDocument extends BaseModel
{
    protected $connection = 'common';

    protected $fillable = [
        'provider_id',
        'document_id',
        'company_id',
        'url',
        'status',
        'expires_at'
    ];

    protected $hidden = [
        'created_type',
        'created_by',
        'modified_type',
        'modified_by',
        'deleted_type',
        'deleted_by',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    public function document(): BelongsTo
    {
        return $this->belongsTo(Document::class, 'document_id', 'id');
    }

    public function provider(): BelongsTo
    {
        return $this->belongsTo(Provider::class, 'provider_id', 'id');
    }

    public function getUrlAttribute()
    {
        return $this->attributes['url'] != null ? json_decode($this->attributes['url']) : $this->attributes['url'];
    }
}
