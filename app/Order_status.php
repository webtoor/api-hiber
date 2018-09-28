<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Order_status extends Model
{
    protected $table = 'order_status';
    protected $fillable = [
        'order_id','status_id','changedby_id'
    ];
    public $timestamps = true;
}
