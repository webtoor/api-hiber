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
}
