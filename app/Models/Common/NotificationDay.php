<?php

namespace App\Models\Common;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class NotificationDay extends BaseModel
{
    use HasFactory;

    protected $connection = 'common';

    protected $table = 'notification_days';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'days',
        'company_id',
    ];

    public function scopeSearch($query, $searchText = '')
    {
        return $query
            ->where('notify_type', 'like', "%" . $searchText . "%")
            ->orWhere('descriptions', 'like', "%" . $searchText . "%")
            ->orWhere('expiry_date', 'like', "%" . $searchText . "%")
            ->orWhere('status', 'like', "%" . $searchText . "%");
    }
}
