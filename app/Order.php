<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $table = 'orders';
    protected $fillable = [
        'subject', 'createdby', 'dtprojectstart', 'dtprojectend','projecttype', 'orderhourduration', 'comment'
    ];
    //public $timestamps = false;
    public function order_status(){
        return $this->hasOne('App\Order_status', 'order_id', 'id');
    }
}
