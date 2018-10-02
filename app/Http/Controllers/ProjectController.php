<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Order;
use App\Order_status;
class ProjectController extends Controller
{
    public function show($user_id){
        
       $results = Order_status::with('order')->where('changedby_id', $user_id)->get();
        
        if($results){
            return response()->json([
                'succes' => true,
                'order' => $results
            ]);
        }else{
            return response()->json([
                'succes' => false,
            ]);
        }

       
    }
}
