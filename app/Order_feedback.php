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
}
