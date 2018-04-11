<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ServiceRequest extends Model
{
    use SoftDeletes;

    protected $dates = ['deleted_at'];

    protected $fillable = [
        'device_id', 'type', 'request_date', 'description', 'status'
    ];

    const TECHNICAL = 1;
    const CLINICAL = 2;

    public static $types = [
        self::TECHNICAL => 'Technical',
        self::CLINICAL => 'Clinical',
    ];

    const REQUESTED = 1;
    const ASSIGNED = 2;
    const COMPLETED = 3;
    const PENDING = 4;
    const CLOSED = 5;

    public static $statuses = [
        self::REQUESTED => 'Unassigned',
        self::ASSIGNED => 'Assigned',
        self::COMPLETED => 'Completed',
        self::PENDING => 'Pending',
        self::CLOSED => 'Closed',
    ];

    public function getFRequestDateAttribute()
    {
        return Carbon::parse($this->request_date)->format(DEFAULT_DATETIME_FORMAT);
    }

    public function getFAttendedAtAttribute()
    {
        return $this->attended_at ? Carbon::parse($this->attended_at)->format(DEFAULT_DATETIME_FORMAT) : '';
    }

    public function getHAttendedAtAttribute()
    {
        return $this->attended_at ? date_f($this->attended_at) : '';
    }

    public function getFCompletedAtAttribute()
    {
        return $this->completed_at ? Carbon::parse($this->completed_at)->format(DEFAULT_DATETIME_FORMAT) : '';
    }

    public function getFClosedAtAttribute()
    {
        return $this->closed_at ? Carbon::parse($this->closed_at)->format(DEFAULT_DATETIME_FORMAT) : '';
    }

    public function device()
    {
        return $this->belongsTo(Device::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'attended_by', 'id');
    }

    public function rating()
    {
        return $this->morphMany(Rating::class, 'model');
    }

    public function photos()
    {
        return $this->hasMany(ServiceRequestPhoto::class);
    }
}
