<?php

namespace App\Models\Common;

use App\Models\BaseModel;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PeakHour extends BaseModel
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'start_time',
        'end_time',
        'status',
        'company_id',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'created_at', 'updated_at'
    ];

    protected $appends = ['started_time', 'ended_time'];

    public function scopeSearch($query, $searchText = '')
    {
        return $query
            ->where('start_time', 'like', "%" . $searchText . "%")
            ->orWhere('end_time', 'like', "%" . $searchText . "%");


    }

    public function city(): BelongsTo
    {
        return $this->belongsTo(City::class, 'city_id', 'id');
    }

    public function getStartedTimeAttribute(): string
    {
        $timezone = $this->attributes['timezone'] ?? "UTC";
        return (isset($this->attributes['start_time'])) ?
            (Carbon::createFromFormat('H:i:s', $this->attributes['start_time'], 'UTC'))->setTimezone($timezone)->format('H:i:s') :
            '';
    }

    public function getEndedTimeAttribute(): string
    {

        $timezone = $this->attributes['timezone'] ?? "UTC";
        return (isset($this->attributes['end_time'])) ?
            (Carbon::createFromFormat('H:i:s', $this->attributes['end_time'], 'UTC'))->setTimezone($timezone)->format('H:i:s') :
            '';
    }
}
