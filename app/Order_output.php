<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Order_output extends Model
{
    protected $table = 'order_outputs';
    protected $fillable = [
        'order_id','output_id'
    ];
    public $timestamps = false;
}
