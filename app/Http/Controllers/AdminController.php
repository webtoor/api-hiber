<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use App\User_role;
use App\Order;
use App\Order_location;
use App\Order_output;

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
       $results = Order::with('user_client')->get();
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

    public function orderDetailShow($id_order){
        $results_location = Order_location::where('order_id', $id_order)->get();
        $results_output = Order_output::where('order_id', $id_order)->get();

        if($results_location && $results_output){
            return response()->json([
                'success' => true,
                'location' => $results_location,
                'output' => $results_output
            ]);
            }else{
            return response()->json([
                'success' => false,
            ]);
            } 
     }

     public function providerShow(){
        $results = User_role::with('user')->where('rf_role_id', 1)->get();

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
