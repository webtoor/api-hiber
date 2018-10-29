<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Order;
use App\Order_status;
use App\Order_location;
use App\User;

use Illuminate\Support\Facades\Auth;

class ProviderProjectController extends Controller
{
    public function tawaranShow(){
        $status_id = '1';
        $results = Order::with(['user_client','order_status' => function ($query) use ($status_id) {
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

    public function berjalanShow($provider_id){
        $status_id = '2';
        $results = Order_status::with(['order'])->where('provider_id', $provider_id)->get(); 
        
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

    public function detailShow($order_id){
        $results = Order_location::where('order_id', $order_id)->get();
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
}
