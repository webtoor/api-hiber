<?php

namespace App\Models;

use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Database\Eloquent\Model;
use Laravel\Lumen\Auth\Authorizable;
use Laravel\Passport\HasApiTokens;

class User extends Model implements AuthenticatableContract, AuthorizableContract
{
    use HasApiTokens, Authenticatable, Authorizable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $table = 'rf_users';

    protected $fillable = [
        'measurement_id',
        'username',
        'email',
        'password',
        'firstname',
        'lastname',
        'address',
        'phonenumber',
        'post_code',
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [
        'password',
    ];

    public function role(){
        return $this->hasOne('App\Models\UserRole', 'user_id');
    }
    public function order_status(){
        return $this->hasOne('App\Models\Order_status', 'doneby_id');
    }
    public function proposal(){
        return $this->hasOne('App\Models\Order_proposal', 'proposal_by');
    }
    public function user_feedback(){
        return $this->hasOne('App\Models\User_feedback', 'user_id');
    }
    public function role_user(){
        return $this->hasOne('App\Models\UserRole', 'user_id')->where('rf_role_id' ,'2');
    }
    public function role_provider(){
        return $this->hasOne('App\Models\UserRole', 'user_id')->where('rf_role_id' ,'1');
    }
}
