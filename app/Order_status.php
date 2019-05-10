<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Order_status extends Model
{
    protected $table = 'order_status';
    protected $fillable = [
        'order_id','status_id','changedby_id', 'provider_id'
    ];
    protected $hidden = ['created_at', 'updated_at', 'id', 'changedby_id'];

    public $timestamps = true;

    public function order(){
        return $this->belongsTo('App\Order', 'order_id', 'id');
    }

    public function user(){
        return $this->belongsTo('App\User', 'provider_id', 'id');
    }
 /*    public function proposal_by(){
        return $this->belongsTo('App\Order_proposal', 'provider_id', 'proposal_by');
    } */
    public function proposal_by(){
        return $this->belongsTo('App\Order_proposal', 'order_id', 'order_id');
    }

    public function user_client(){
        return $this->belongsTo('App\User', 'provider_id', 'id');
    }
    public function user_clients(){
        return $this->belongsTo('App\User', 'changedby_id', 'id');
    }
    public function output(){
        return $this->hasMany('App\Order_output', 'order_id', 'order_id');
    }

    public function order_feedback(){
        return $this->belongsTo('App\Order_feedback', 'order_id', 'order_id');
    }
}
