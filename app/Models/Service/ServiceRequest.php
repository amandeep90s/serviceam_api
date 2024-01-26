<?php

namespace App\Models\Service;

use App\Models\BaseModel;
use App\Models\Common\Chat;
use App\Models\Common\Provider;
use App\Models\Common\ProviderService;
use App\Models\Common\Rating;
use App\Models\Common\User;
use App\Models\Common\UserRequest;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class ServiceRequest extends BaseModel
{
    use HasFactory;

    protected $connection = 'service';

    protected $hidden = [
        'company_id',
        'created_by',
        'modified_type',
        'modified_by',
        'deleted_type',
        'deleted_by',
        'updated_at',
        'deleted_at'
    ];

    protected array $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
        'assigned_at',
        'schedule_at',
        'started_at',
        'finished_at',
    ];
    protected $appends = ['assigned_time', 'schedule_time', 'started_time', 'finished_time', 'created_time'];


    public function scopeuserHistorySearch($query, $searchText = '')
    {
        if ($searchText != '') {
            return $query
                ->where('booking_id', 'like', "%" . $searchText . "%")
                ->orWhere('status', 'like', "%" . $searchText . "%")
                ->orWhere('payment_mode', 'like', "%" . $searchText . "%");
        }
        return null;
    }

    public function scopeProviderhistorySearch($query, $searchText = '')
    {
        if ($searchText != '') {
            return $query
                ->where('booking_id', 'like', "%" . $searchText . "%")
                ->OrwhereHas('service', function ($q) use ($searchText) {
                    $q->where('service_name', 'like', "%" . $searchText . "%");
                })
                ->OrwhereHas('payment', function ($q) use ($searchText) {
                    $q->where('total', 'like', "%" . $searchText . "%");
                });

        }
        return null;
    }

    public function scopeHistoryProvider($query, $provider_id, $historyStatus)
    {
        return $query->where('provider_id', $provider_id)
            ->whereIn('status', $historyStatus)
            ->orderBy('created_at', 'desc');
    }

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class, 'service_id');
    }

    public function serviceCategory(): BelongsTo
    {
        return $this->belongsTo(ServiceCategory::class, 'service_id');
    }

    /**
     * The user who created the request.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function chat(): HasOne
    {
        return $this->hasOne(Chat::class, 'request_id');
    }

    /**
     * The provider assigned to the request.
     */
    public function provider(): BelongsTo
    {
        return $this->belongsTo(Provider::class, 'provider_id');
    }

    public function service_type(): BelongsTo
    {
        return $this->belongsTo(ProviderService::class, 'provider_id', 'provider_id');
    }

    public function user_request(): BelongsTo
    {
        return $this->belongsTo(UserRequest::class, 'id', 'request_id');
    }

    /**
     * UserRequestPayment Model Linked
     */
    public function payment(): HasOne
    {
        return $this->hasOne(ServiceRequestPayment::class, 'service_request_id');
    }

    public function rating(): HasOne
    {
        return $this->hasOne(Rating::class, 'request_id');
    }

    public function getAssignedTimeAttribute(): string
    {
        return (isset($this->attributes['assigned_at'])) ? (Carbon::createFromFormat('Y-m-d H:i:s', $this->attributes['assigned_at'], 'UTC'))->setTimezone($this->attributes['timezone'])->format('d-m-Y g:i A') : '';

    }

    public function getScheduleTimeAttribute(): string
    {
        return (isset($this->attributes['schedule_at'])) ? (Carbon::createFromFormat('Y-m-d H:i:s', $this->attributes['schedule_at'], 'UTC'))->setTimezone($this->attributes['timezone'])->format('d-m-Y g:i A') : '';

    }

    public function getStartedTimeAttribute(): string
    {
        return (isset($this->attributes['started_at'])) ? (Carbon::createFromFormat('Y-m-d H:i:s', $this->attributes['started_at'], 'UTC'))->setTimezone($this->attributes['timezone'])->format('d-m-Y g:i A') : '';

    }

    public function getFinishedTimeAttribute(): string
    {
        return (isset($this->attributes['finished_at'])) ? (Carbon::createFromFormat('Y-m-d H:i:s', $this->attributes['finished_at'], 'UTC'))->setTimezone($this->attributes['timezone'])->format('d-m-Y g:i A') : '';

    }

    public function scopePendingRequest($query, $user_id)
    {
        return $query->where('user_id', $user_id)
            ->whereNotIn('status', ['CANCELLED', 'COMPLETED', 'SCHEDULED'])
            ->where('user_rated', 0);
    }

    public function scopeServiceRequestStatusCheck($query, $user_id, $check_status)
    {
        return $query->where('service_requests.user_id', $user_id)
            ->where('service_requests.user_rated', 0)
            ->whereNotIn('service_requests.status', $check_status)
            ->select('service_requests.*')
            ->with('user', 'provider', 'service', 'payment', 'chat');
    }

    public function scopeServiceRequestAssignProvider($query, $user_id, $check_status)
    {
        return $query->where('service_requests.user_id', $user_id)
            ->where('service_requests.user_rated', 0)
            ->where('service_requests.provider_id', 0)
            ->whereIn('service_requests.status', $check_status)
            ->select('service_requests.*');
    }

    public function scopeHistoryUserTrips($query, $user_id, $showType = '')
    {
        if ($showType != '') {
            if ($showType == 'past') {
                $history_status = array('CANCELLED', 'COMPLETED');
            } else if ($showType == 'upcoming') {
                $history_status = array('SCHEDULED');
            } else if ($showType == 'all') {
                $history_status = array('SCHEDULED', 'SEARCHING', 'ACCEPTED', 'STARTED', 'ARRIVED', 'PICKEDUP', 'DROPPED', 'CANCELLED', 'COMPLETED');

            } else {
                $history_status = array('SEARCHING', 'ACCEPTED', 'STARTED', 'ARRIVED', 'PICKEDUP', 'DROPPED');
            }
            return $query->where('service_requests.user_id', $user_id)
                ->whereIn('service_requests.status', $history_status)
                ->orderBy('service_requests.created_at', 'desc');
        }
        return null;
    }

    public function scopeServiceSearch($query, $searchText = '')
    {
        return $query->
        whereHas('payment', function ($q) use ($searchText) {
            $q->where('payment_mode', 'like', "%" . $searchText . "%");
        })
            ->OrwhereHas('service', function ($q) use ($searchText) {
                $q->where('service_name', 'like', "%" . $searchText . "%");
            })
            ->Orwhere('booking_id', 'like', "%" . $searchText . "%")
            ->orWhere('status', 'like', "%" . $searchText . "%");

    }

    public function scopeUserUpcomingTrips($query, $user_id)
    {
        return $query->where('service_requests.user_id', $user_id)
            ->where('service_requests.status', 'SCHEDULED')
            ->orderBy('service_requests.created_at', 'desc');
    }

    public function dispute(): BelongsTo
    {
        return $this->belongsTo(ServiceRequestDispute::class, 'id', 'service_request_id');
    }

    public function getCreatedTimeAttribute(): string
    {
        return (isset($this->attributes['created_at'])) ? (Carbon::createFromFormat('Y-m-d H:i:s', $this->attributes['created_at'], 'UTC'))->setTimezone($this->attributes['timezone'])->format('d-m-Y g:i A') : '';

    }
}
