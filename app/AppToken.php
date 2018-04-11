<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AppToken extends Model
{
    use SoftDeletes;

    protected $fillable = ['app_token', 'platform'];

    public static $rules = [
        'app_token' => 'required',
        'platform' => 'required'
    ];

    const IOS = 1;
    const ANDROID = 2;
}
