<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    protected $dates = ['created_at'];

    protected $fillable = ['user_id', 'status', 'data'];

    const IS_NEW = 1;
    const IS_VIEW = 2;

    public function model()
    {
        return $this->morphTo();
    }
}
