<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class order_proposal extends Model
{
    protected $table = 'order_proposals';
    protected $fillable = [
        'order_id','proposal_by','offered_price', 'comment'
    ];
    protected $hidden = ['created_at', 'updated_at', 'id',];

    public $timestamps = true;

    public function user(){
        return $this->belongsTo('App\User', 'proposal_by', 'id');
    }

    public function user_feedback(){
        return $this->hasOne('App\User_feedback', 'user_id', 'proposal_by');
    }
}
