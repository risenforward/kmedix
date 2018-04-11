<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SupplierContactPerson extends Model
{
    use SoftDeletes;

    protected $dates = ['deleted_at'];

    protected $table = 'supplier_contact_persons';

    protected $fillable = [
        'email', 'phone_number', 'first_name', 'middle_name', 'last_name',
    ];

    public static $rules = [
        'email' => 'required|email|max:255|unique:supplier_contact_persons,email,{id}',
        'phone_number' => 'phone:AUTO',
        'first_name' => 'required|max:255',
        'last_name' => 'required|max:255',
        'middle_name' => 'max:255',
    ];

    public function getFullNameAttribute()
    {
        return $this->first_name . ' ' . $this->last_name . ($this->middle_name ? ' ' . $this->middle_name : '');
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }
}
