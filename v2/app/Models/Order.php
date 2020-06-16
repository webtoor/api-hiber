<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $table = 'orders';
    protected $fillable = [
        'subject', 'createdby', 'dtprojectstart', 'dtprojectend','projecttype', 'orderhourduration', 'comment'
    ];
    protected $hidden = ['orderhourduration', 'updated_at'];

    //public $timestamps = false;
    public function order_status(){
        return $this->hasOne('App\Models\OrderStatus', 'order_id', 'id');
    }

    public function user_client(){
        return $this->belongsTo('App\Models\User', 'createdby', 'id');
    }
}
