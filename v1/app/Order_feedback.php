<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Order_feedback extends Model
{
    protected $table = 'order_feedbacks';
    protected $fillable = [
        'writter','for','order_id', 'rating', 'comment'
    ];
    protected $hidden = ['created_at', 'updated_at'];

    public $timestamps = true;

    public function client(){
        return $this->belongsTo('App\User', 'writter', 'id');
    }

    public function order(){
        return $this->belongsTo('App\Order', 'order_id', 'id');
    }
}
