<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Order;
class ProviderProjectController extends Controller
{
    public function tawaranShow(){
        //return Order::with('order_status')->get();
        $status_id = '1';
        $results = Order::with(['order_status' => function ($query) use ($status_id) {
                            $query->where('status_id', $status_id);
        }])->get();  

        if($results){
            return response()->json([
                'success' => true,
                'data' => $results
            ]);
        }else{
            return response()->json([
                'success' => false,
                'data' => $results
            ]);
        }
                        
    }

    public function berjalanShow(){
            
    }
}
