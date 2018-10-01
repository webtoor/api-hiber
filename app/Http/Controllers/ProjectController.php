<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Order;
use App\Order_status;
class ProjectController extends Controller
{
    public function show($createdby){
       /* $result = Order_status::where('changedby_id', $createdby)->first();
       return $result->order; */
       $results = Order::with('order_status')->where('createdby', $createdby)->get();
        
        if($results){
            return response()->json([
                'succes' => true,
                'order' => $results
            ]);
        }

       
    }
}
