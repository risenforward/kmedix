<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PreventiveMaintenance extends Model
{
    use SoftDeletes;

    protected $dates = ['deleted_at'];

    const EVERY = 3;

    public function device()
    {
        return $this->belongsTo(Device::class);
    }

    public function getFMaintenanceDateAttribute()
    {
        return Carbon::parse($this->maintenance_date)->format(DEFAULT_DATE_FORMAT);
    }

    public function getFCompletedAtAttribute()
    {
        return $this->completed_at ? Carbon::parse($this->completed_at)->format(DEFAULT_DATETIME_FORMAT) : null;
    }

    public static function getSchedule($start, $count, $extended = false)
    {
        $schedule = [];
        for($i = self::EVERY; $i < $count; $i += self::EVERY) {
            $maintenance = new PreventiveMaintenance();
            $maintenance->maintenance_date = Carbon::parse($start)->addMonth($i);
            $maintenance->extended = $extended ? 1 : 0;
            $maintenance->completed = 0;
            $schedule[] = $maintenance;
        }

        return $schedule;
    }

    public static function deleteMaintenances($device, $extended = 0)
    {
        $device->preventiveMaintenances->where('extended', $extended)->each(function ($maintenance) {
            $maintenance->delete();
        });
    }
}
