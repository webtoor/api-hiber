<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class order_proposal extends Model
{
    protected $table = 'order_proposals';
    protected $fillable = [
        'order_id','proposal_by','offered_price', 'comment'
    ];
    protected $hidden = ['created_at', 'updated_at'];

    public $timestamps = true;

    public function user(){
        return $this->belongsTo('App\User', 'proposal_by', 'id');
    }

    public function user_feedback(){
        return $this->hasOne('App\User_feedback', 'user_id', 'proposal_by');
    }

    public function order(){
        return $this->hasOne('App\Order', 'id', 'order_id');
    }

    public function order_status(){
        return $this->hasOne('App\Order_status', 'order_id', 'order_id');
    }
}
