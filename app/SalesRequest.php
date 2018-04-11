<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SalesRequest extends Model
{
    use SoftDeletes;

    protected $dates = ['deleted_at'];

    protected $fillable = ['request_date', 'request_details', 'status'];

    const NOT_PROCESSED = 1;
    const PROCESSED = 2;
    const PENDING = 3;
    const DISMISS = 4;

    static $statuses = [
        self::NOT_PROCESSED => 'Not processed',
        self::PROCESSED => 'Processed',
        self::PENDING => 'Pending',
        self::DISMISS => 'Dismiss',
    ];

    public function getFRequestDateAttribute()
    {
        return Carbon::parse($this->request_date)->format(DEFAULT_DATE_FORMAT);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function notifications()
    {
        return $this->morphMany(Notification::class, 'model');
    }
}
