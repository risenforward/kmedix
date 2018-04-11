<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DeviceModel extends Model
{
    use SoftDeletes;

    protected $dates = ['deleted_at'];

    protected $fillable = [
        'supplier_id', 'name', 'description', 'counter_1', 'counter_2', 'counter_3',
    ];

    public static $rules = [
        'supplier_id' => 'required|not_in:0',
        'name' => 'required|max:255',
        'description' => 'required|max:1000',
        'image' => 'max:3096',
    ];

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function devices()
    {
        return $this->hasMany(Device::class);
    }

    public static function prepareDeviceModel($data, $model = null)
    {
        if (is_null($model)) {
            $model = new DeviceModel();
        }
        $model->fill($data);
        $model->active = isset($data['active']) ? 1 : 0;

        return $model;
    }

    public function getPhotoUrlAttribute()
    {
        return $this->attributes['photo'] ?
            \URL::to('/uploads/models/devicemodel-' . $this->attributes['id'] . '/' . $this->attributes['photo']) :
            \URL::to('/assets/img/model_default.png');
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
