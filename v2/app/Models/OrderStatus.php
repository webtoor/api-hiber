<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderStatus extends Model
{
    protected $table = 'order_status';
    protected $fillable = [
        'order_id','status_id','changedby_id', 'provider_id'
    ];
    protected $hidden = ['created_at', 'updated_at', 'id', 'changedby_id'];

    public $timestamps = true;

    public function order(){
        return $this->belongsTo('App\Models\Order', 'order_id', 'id');
    }

    public function user(){
        return $this->belongsTo('App\User', 'provider_id', 'id');
    }
     public function proposal_by(){
        return $this->belongsTo('App\Models\OrderProposal', 'provider_id', 'proposal_by');
    }
    public function order_proposal_by(){
        return $this->belongsTo('App\Models\OrderProposal', 'order_id', 'order_id');
    }

    public function user_client(){
        return $this->belongsTo('App\Models\User', 'provider_id', 'id');
    }
    public function user_clients(){
        return $this->belongsTo('App\Models\User', 'changedby_id', 'id');
    }
    public function output(){
        return $this->hasMany('App\Models\OrderOutput', 'order_id', 'order_id');
    }

    public function order_feedback(){
        return $this->belongsTo('App\Models\OrderFeedback', 'order_id', 'order_id');
    }
}
