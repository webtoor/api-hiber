<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderLocation extends Model
{
    protected $table = 'order_locations';
    protected $fillable = [
        'order_id','latitude','longitude'
    ];
    protected $hidden = ['id'];
    public $timestamps = false;
}
