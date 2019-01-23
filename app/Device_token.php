<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Device_token extends Model
{
    protected $table = 'device_tokens';
    protected $fillable = [
        'user_id','token'
    ];
    public $timestamps = false;

}
