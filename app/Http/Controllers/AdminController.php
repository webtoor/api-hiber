<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use App\User_role;

class AdminController extends Controller
{
    public function userShow(){
       $results = User_role::with('user')->where('rf_role_id', 2)->get();

       if($results){
        return response()->json([
            'success' => true,
            'data' => $results
        ]);
        }else{
        return response()->json([
            'success' => false,
        ]);
        }  
    }   
}
