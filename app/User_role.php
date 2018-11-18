<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class User_role extends Model
{
    protected $table = 'user_roles';
    protected $fillable = [
        'user_id', 'rf_role_id'
    ];
    public $timestamps = false;
    protected $hidden = [
        'id',
    ];

    public function user(){
        return $this->belongsTo('App\User', 'user_id', 'id');
    }
}
