<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use App\User_role;

class AdminController extends Controller
{
    public function userShow($admin_id){
       return User_role::with('user')->where('rf_role_id', 1)->get();
    }   
}
