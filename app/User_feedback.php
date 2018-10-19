<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class User_feedback extends Model
{
    protected $table = 'user_feedbacks';
    protected $fillable = [
        'user_id','total_rating'
    ];
    protected $hidden = ['created_at', 'updated_at', 'id'];

    public $timestamps = true;


}
