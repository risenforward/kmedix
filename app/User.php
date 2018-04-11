<?php

namespace App;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Zizaco\Entrust\Traits\EntrustUserTrait;

class User extends Authenticatable
{
    use EntrustUserTrait;

    protected $isEngineer = null;

    protected $fillable = [
        'first_name', 'middle_name', 'last_name', 'phone_number', 'api_token',
    ];

    protected $hidden = [
        'password', 'remember_token', 'api_token',
    ];

    public static $rules = [
        'email' => 'required|email|max:255|unique:users,email,{id}',
        'password' => 'required|min:6',
        'first_name' => 'required|max:255',
        'last_name' => 'required|max:255',
        'phone_number' => 'required',//'phone:AUTO',
        'role' => 'required|not_in:0'
    ];

    public function getFullNameAttribute()
    {
        return $this->first_name . ' ' . $this->last_name . ($this->middle_name ? ' ' . $this->middle_name : '');
    }

    public function getRoleIdAttribute()
    {
        return $this->roles()->first()->id;
    }

    public function getRoleAttribute()
    {
        return $this->roles()->first()->display_name;
    }

    public function getRoleNameAttribute()
    {
        return $this->roles()->first()->name;
    }

    public function customer()
    {
        return $this->hasOne(Customer::class);
    }

    public function serviceRequests()
    {
        return $this->hasMany(ServiceRequest::class, 'attended_by', 'id');
    }

    public function serviceLogs()
    {
        return $this->hasMany(ServiceLog::class);
    }

    public function ratings()
    {
        return $this->morphMany(Rating::class, 'model');
    }

    public function appTokens()
    {
        return $this->hasMany(AppToken::class);
    }

    public function isEngineer()
    {
        if ($this->isEngineer) {
            return true;
        }

        $this->isEngineer = $this->hasRole('TECHNICAL_SUPPORT_ENGINEER') || $this->hasRole('CLINICAL_SUPPORT_ENGINEER');
        return $this->isEngineer;
    }

    public static function prepareUser($data, $user = null)
    {
        if (is_null($user)) {
            $user = new User();
            $user->password = bcrypt($data['password']);
            $user->api_token = str_random(60);
        }
        $user->fill($data);
        if (isset($data['username']) && $data['username'] != '') {
            $user->username = $data['username'];
        }
        if ($data['email'] != '') {
            $user->email = $data['email'];
        }
        $user->active = isset($data['active']) ? 1 : 0;

        return $user;
    }

    public static function active($role, $transform = false)
    {
        $users = User::all()->load('roles')->filter(function ($user) use ($role) {
            return $user->roles->first()->name == $role;
        });

        return $transform ? $users->transform(function ($user) {
            return ['id' => $user->id, 'name' => $user->full_name];
        }) : $users;
    }

    public function getRatingAttribute()
    {
        $ratings = $this->ratings;
        $total = 0;
        foreach($ratings as $rating) {
            $total += $rating->rating;
        }

        return $ratings->count() ? round($total / $ratings->count(), 1) : 0;
    }
}
