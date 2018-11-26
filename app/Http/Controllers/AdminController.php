<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use App\User_role;
use App\Order;

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
    
    public function orderShow(){
       $results = Order::all();
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
