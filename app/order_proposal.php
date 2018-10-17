<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class order_proposal extends Model
{
    protected $table = 'order_status';
    protected $fillable = [
        'order_id','proposal_by','offered_by', 'doneby_id'
    ];
    protected $hidden = ['created_at', 'updated_at', 'id',];

    public $timestamps = true;
}
