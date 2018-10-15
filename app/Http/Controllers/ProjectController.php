<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Order;
use App\Order_status;
use App\Order_location;
use App\Order_output;

class ProjectController extends Controller
{
    public function show($user_id){
        
       $results = Order_status::with('order')->where('changedby_id', $user_id)->orderBy('id', 'desc')->get();
        
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
         $result_polygon = Order_location::where('order_id', $order_id)->get();
         $result_output = Order_output::where('order_id', $order_id)->get();

         if($result_polygon && $result_output){
             return response()->json([
            'success' => true,
            'polygon' => $result_polygon,
            'output' => $result_output
         ]);
         }else{
            return response()->json([
                'success' => false,
             ]);
         }

    }

    public function updateStatus (Request $request, $order_id){
        $result = Order_status::where('order_id',$order_id)->update(['status_id' => '4']);
        if($result){
            return response()->json([
                "success" => true,
            ]);
        }else{
            return response()->json([
                "success" => false,
            ]);
        }

    }

}
