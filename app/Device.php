<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Device extends Model
{
    use SoftDeletes;

    protected $dates = ['deleted_at'];

    protected $fillable = [
        'device_model_id', 'customer_id', 'serial_number', 'installed_by', 'warranty', 'consumable_warranty', 'contract_level', 'preventive_maintenance'
    ];

    public static $rules =[
        'device_model_id' => 'required|integer|not_in:0',
        'customer_id' => 'required|integer|not_in:0',
        'serial_number' => 'required|max:255|unique:devices,serial_number,{id}',
        'install_date' => 'required|date',
        'installed_by' => 'required|integer|not_in:0',
        'warranty' => 'integer',
        'extended_warranty' => 'integer',
        'extended_warranty_start' => 'required_with:extended_warranty|date',
    ];

    public static $extWarrantyRules = [
        'extended_warranty' => 'integer',
        'extended_warranty_start' => 'required_with:extended_warranty|date',
    ];

    public function getFInstallDate($format = DEFAULT_DATE_FORMAT)
    {
        return Carbon::parse($this->attributes['install_date'])->format($format);
    }

    public function getFExtWarrantyStartDate($format = DEFAULT_DATE_FORMAT)
    {
        return Carbon::parse($this->attributes['extended_warranty_start'])->format($format);
    }

    public function setConsumableWarrantyAttribute($value)
    {
        $this->attributes['consumable_warranty'] = $value != '' ? $value : null;
    }

    public function deviceModel()
    {
        return $this->belongsTo(DeviceModel::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'installed_by', 'id');
    }

    public function serviceRequests()
    {
        return $this->hasMany(ServiceRequest::class);
    }

    public function serviceLogs()
    {
        return $this->hasMany(ServiceLog::class);
    }

    public function preventiveMaintenances()
    {
        return $this->hasMany(PreventiveMaintenance::class);
    }

    public function notification()
    {
        return $this->morphOne(Notification::class, 'model');
    }

    public static function prepare($data, $device = null)
    {
        if (is_null($device)) {
            $device = new Device();
        }
        $device->fill($data);
        if (isset($data['install_date'])) {
            $device->install_date = Carbon::parse($data['install_date']);
        }
        if (isset($data['preventive_maintenance'])) {
            $device->preventive_maintenance = true;
        } else {
            $device->preventive_maintenance = false;
        }
        $device->contract_level = isset($data['contract_level']) ? $data['contract_level'] : null;
        $device->extended_warranty = isset($data['extended_warranty']) ? $data['extended_warranty'] : null;
        $device->extended_warranty_start =
            isset($data['extended_warranty_start']) ? Carbon::parse($data['extended_warranty_start']) : null;

        return $device;
    }

    public static function getCounters($device, $log)
    {
        $counters = [];
        if ($device->deviceModel->counter_1) {
            $counters[$device->deviceModel->counter_1] = $log->counter_1;
        }
        if ($device->deviceModel->counter_2) {
            $counters[$device->deviceModel->counter_2] = $log->counter_2;
        }
        if ($device->deviceModel->counter_3) {
            $counters[$device->deviceModel->counter_3] = $log->counter_3;
        }

        return $counters;
    }

    public function getCountersNames()
    {
        $counters = [];
        if ($this->deviceModel->counter_1) {
            $counters[] = $this->deviceModel->counter_1;
        }
        if ($this->deviceModel->counter_2) {
            $counters[] = $this->deviceModel->counter_2;
        }
        if ($this->deviceModel->counter_3) {
            $counters[] = $this->deviceModel->counter_3;
        }

        return $counters;
    }
}
