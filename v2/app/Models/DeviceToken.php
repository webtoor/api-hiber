<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DeviceToken extends Model
{
    protected $table = 'device_tokens';
    protected $fillable = [
        'user_id','role_id','token'
    ];
    public $timestamps = false;
}
