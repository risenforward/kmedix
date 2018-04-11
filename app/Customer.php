<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Customer extends Model
{
    use SoftDeletes;

    protected $dates = ['deleted_at'];

    protected $fillable = [
        'clinic_name', 'address', 'specialization'
    ];

    public static $specializations = [
        'Specialized Clinic',
        'Polyclinic',
        'Medical Center',
        'Day Care Center',
        'Hospital',
    ];

    public static $rules = [
        'username' => 'required|max:255|unique:users,username,{id}',
        'email' => 'email|max:255|unique:users,email,{id}',
        'password' => 'required|min:6',
        'clinic_name' => 'required|max:255',
        'address' => 'required|max:255',
        'phone_number' => 'required',
        'specialization' => 'required|not_in:0',
        'image' => 'max:3096',
    ];

    public static function prepareCustomer($data, $customer = null)
    {
        if (is_null($customer)) {
            $customer = new Customer();
        }
        $customer->fill($data);

        return $customer;
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function contactPersons()
    {
        return $this->hasMany(CustomerContactPerson::class);
    }

    public function devices()
    {
        return $this->hasMany(Device::class);
    }

    public function salesRequests()
    {
        return $this->hasMany(SalesRequest::class);
    }

    public function complainRequests()
    {
        return $this->hasMany(Complain::class);
    }

    public function getLogoUrlAttribute()
    {
        return $this->attributes['logo'] ?
            \URL::to('/uploads/customers/customer-' . $this->attributes['id'] . '/' . $this->attributes['logo']) :
            \URL::to('/assets/img/clinic-icon2.png');
    }

    public static function active($transform = false)
    {
        $customer = Customer::all()->load('user')->filter(function ($customer) {
            return $customer->user->active;
        });
        return $transform ? $customer->transform(function ($customer) {
            return ['id' => $customer->id, 'name' => $customer->clinic_name];
        }) : $customer;
    }
}
