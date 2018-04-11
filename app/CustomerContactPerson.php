<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CustomerContactPerson extends Model
{
    use SoftDeletes;

    protected $dates = ['deleted_at'];

    protected $table = 'customer_contact_persons';

    protected $fillable = [
        'first_name', 'middle_name', 'last_name', 'phone_number'
    ];

    public static $rules = [
        'phone_number' => 'phone:AUTO',
        'first_name' => 'required|max:255',
        'last_name' => 'required|max:255',
        'middle_name' => 'max:255',
    ];

    public function getFullNameAttribute()
    {
        return $this->first_name . ' ' . $this->last_name . ($this->middle_name ? ' ' . $this->middle_name : '');
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }
}
