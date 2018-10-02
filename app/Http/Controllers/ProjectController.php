<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Order;
use App\Order_status;
use App\Order_location;

class ProjectController extends Controller
{
    public function show($user_id){
        
       $results = Order_status::with('order')->where('changedby_id', $user_id)->get();
        
        if($results){
            return response()->json([
                'success' => true,
                'order' => $results
            ]);
        }else{
            return response()->json([
                'success' => false,
            ]);
        }        
    }
    public function showPolygon($order_id){
        return $result = Order_location::where('order_id', $order_id)->get();
    }

}
