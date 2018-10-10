<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Order_location extends Model
{
    protected $table = 'order_locations';
    protected $fillable = [
        'order_id','latitude','longitude'
    ];
    protected $hidden = ['id'];
    public $timestamps = false;
}
