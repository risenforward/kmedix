<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ServiceLog extends Model
{
    use SoftDeletes;

    protected $dates = ['deleted_at'];

    protected $table = 'service_log';

    protected $fillable = [
        'service_date', 'part_number', 'quantity', 'description',
    ];

    public function getFServiceDateAttribute()
    {
        return Carbon::parse($this->service_date)->format(DEFAULT_DATE_FORMAT);
    }

    public function device()
    {
        return $this->belongsTo(Device::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getCounters()
    {
        $names = [];
        $counters = [];
        if ($this->counter_1) {
            $counters['1'] = $this->counter_1;
        }
        if ($this->counter_2) {
            $counters['2'] = $this->counter_2;
        }
        if ($this->counter_3) {
            $counters['3'] = $this->counter_3;
        }

        return $counters;
    }
}
