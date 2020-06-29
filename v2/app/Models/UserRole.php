<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserRole extends Model
{
    protected $table = 'user_roles';
    protected $fillable = [
        'user_id', 'rf_role_id'
    ];
    public $timestamps = false;

    public function user(){
        return $this->belongsTo('App\User', 'user_id', 'id');
    }
}
