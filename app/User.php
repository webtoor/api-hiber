<?php

namespace App;

use Illuminate\Auth\Authenticatable;
use Laravel\Lumen\Auth\Authorizable;
use Laravel\Passport\HasApiTokens;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;

class User extends Model implements AuthenticatableContract, AuthorizableContract
{
    use HasApiTokens, Authenticatable, Authorizable;

    protected $table = 'rf_users';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
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
         'updated_at'
    ];

    public function role(){
        return $this->hasOne('App\User_role', 'user_id');
    }
    public function order_status(){
        return $this->hasOne('App\Order_status', 'doneby_id');
    }
    public function proposal(){
        return $this->hasOne('App\Order_proposal', 'proposal_by');
    }
    public function user_feedback(){
        return $this->hasOne('App\User_feedback', 'user_id');
    }
    public function role_user(){
        return $this->hasOne('App\User_role', 'user_id')->where('rf_role_id' ,'2');
    }
    public function role_provider(){
        return $this->hasOne('App\User_role', 'user_id')->where('rf_role_id' ,'1');
    }
}
