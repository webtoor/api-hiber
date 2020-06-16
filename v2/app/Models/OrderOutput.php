<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderOutput extends Model
{
    protected $table = 'order_outputs';
    protected $fillable = [
        'order_id','output_id'
    ];
    protected $hidden = ['id'];
    public $timestamps = false;
}
