<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Complain extends Model
{
    use SoftDeletes;

    protected $dates = ['deleted_at'];

    protected $fillable = [
        'description', 'notes', 'status'
    ];

    const NOT_PROCESSED = 1;
    const PROCESSED = 2;

    public static $statuses = [
        self::NOT_PROCESSED => 'Not processed',
        self::PROCESSED => 'Processed'
    ];

    public function getFCreatedAt($format = DEFAULT_DATE_FORMAT)
    {
        return Carbon::parse($this->created_at)->format($format);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }
}
