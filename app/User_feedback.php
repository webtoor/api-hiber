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

    public function user_feedbacks(){
        return $this->belongsTo('App\Order_proposal', 'user_id', 'proposal_by');
    }
    public function proposal(){
        return $this->hasOne('App\Order_proposal', 'proposal_by', 'user_id');
    }
    public function user(){
        return $this->belongsTo('App\User', 'user_id', 'id');
    }

}
