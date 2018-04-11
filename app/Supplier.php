<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Supplier extends Model
{
    use SoftDeletes;

    protected $dates = ['deleted_at'];

    protected $fillable = [
        'name', 'phone_number', 'fax_number', 'country', 'address', 'web_address'
    ];

    public static $rules = [
        'name' => 'required|max:255',
        'phone_number' => 'phone:AUTO',
        'fax_number' => 'phone:AUTO',
        'country' => 'required|not_in:0',
        'address' => 'required|max:255',
        'web_address' => 'required|url|max:100',
        'image' => 'max:3096',
    ];

    public function contactPersons()
    {
        return $this->hasMany(SupplierContactPerson::class);
    }

    public function deviceModels()
    {
        return $this->hasMany(DeviceModel::class);
    }

    public static function prepareSupplier($data, $supplier = null)
    {
        if (is_null($supplier)) {
            $supplier = new Supplier();
        }

        $supplier->fill($data);
        $supplier->active = isset($data['active']) ? 1 : 0;

        return $supplier;
    }
}
