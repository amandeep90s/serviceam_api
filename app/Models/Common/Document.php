<?php

namespace App\Models\Common;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Auth;

class Document extends BaseModel
{
    protected $connection = 'common';

    protected $fillable = [
        'name',
        'type',
        'company_id'
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
        'deleted_at'
    ];

    public function scopeSearch($query, $searchText = '')
    {
        return $query
            ->where('name', 'like', "%" . $searchText . "%")
            ->orWhere('type', 'like', "%" . $searchText . "%");
    }

    public function provider_document(): BelongsTo
    {
        return $this->belongsTo(ProviderDocument::class, 'id', 'document_id')
            ->where('provider_id', Auth::guard('provider')->user()->id);
    }
}
