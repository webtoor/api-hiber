<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderProposal extends Model
{
    protected $table = 'order_proposals';
    protected $fillable = [
        'order_id','proposal_by','offered_price', 'comment'
    ];
    protected $hidden = ['created_at', 'updated_at'];

    public $timestamps = true;

    public function user(){
        return $this->belongsTo('App\Models\User', 'proposal_by', 'id');
    }

    public function user_feedback(){
        return $this->hasOne('App\Models\UserFeedback', 'user_id', 'proposal_by');
    }

    public function order(){
        return $this->hasOne('App\Models\Order', 'id', 'order_id');
    }

    public function order_status(){
        return $this->hasOne('App\Models\OrderStatus', 'order_id', 'order_id');
    }
}
