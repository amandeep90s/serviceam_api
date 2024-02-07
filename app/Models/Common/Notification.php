<?php

namespace App\Models\Common;

use App\Models\BaseModel;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Notification extends BaseModel
{
    use HasFactory;

    protected $connection = 'common';

    protected $appends = ['expiry_time'];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'notify_type',
        'company_id',
        'service',
        'image',
        'description',
        'expiry_date',
        'status'
    ];

    public function scopeSearch($query, $searchText = '')
    {
        return $query
            ->where('notify_type', 'like', "%" . $searchText . "%")
            ->orWhere('descriptions', 'like', "%" . $searchText . "%")
            ->orWhere('expiry_date', 'like', "%" . $searchText . "%")
            ->orWhere('status', 'like', "%" . $searchText . "%");
    }

    public function getExpiryTimeAttribute(): string
    {
        return (isset($this->attributes['expiry_date'])) ?
            (Carbon::createFromFormat('Y-m-d H:i:s', $this->attributes['expiry_date'], 'UTC'))->format('m-d-Y') : '';
    }
}
