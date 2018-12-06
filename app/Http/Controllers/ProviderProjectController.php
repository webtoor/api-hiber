<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use App\Order;
use App\Order_status;
use App\Order_location;
use App\Order_output;
use App\Order_proposal;
use App\Order_feedback;
use App\User;
use App\User_feedback;

use Illuminate\Support\Facades\Auth;

class ProviderProjectController extends Controller
{   
    public function getRatingShow($provider_id){
        $results =  User_feedback::where('user_id', $provider_id)->first();
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

    public function tawaranShow(){
        $status_id = '1';
        $results = Order::with(['user_client','order_status' => function ($query) use ($status_id) {
                $query->where('status_id', $status_id); }])->orderBy('id', 'desc')->get();  

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
        $results_polygon = Order_location::where('order_id', $order_id)->get();
        $results_output = Order_output::where('order_id', $order_id)->get();
        if($results_polygon && $results_output){
            return response()->json([
                'success' => true,
                'polygon' => $results_polygon,
                'output'   => $results_output,
            ]);
        }else{
            return response()->json([
                'success' => false,
                'polygon' => $results_polygon,
                'output'   => $results_output,
            ]);
        }
    }

    public function bidding(Request $request){
        $this->validate($request, [
            'offered_price' => 'required',
           ]);
        $order_id = $request->json('order_id');
        $proposal_by = $request->json('proposal_by');
        $offered_price = $request->json('offered_price');
        $comment = $request->json('comment');

       $results = Order_proposal::create([
            'order_id' => $order_id,
            'proposal_by' => $proposal_by,
            'offered_price' => $offered_price,
            'comment' => $comment,
        ]);

        if($results){
            return response()->json([
                'success' => true,
                'data' => $results
            ]);
            return response()->json([
                'success' => false,
                'data' => $results
            ]);
        }

    }

    public function berjalanIkutiShow($provider_id){
        $status_id = '1';
         $results = Order_proposal::with(['order' => function ($query) {
            $query->with('user_client');
        }, 'order_status' => function ($query) {
            $query->where('status_id', '1');
        }])->where('proposal_by', $provider_id)->get();
        
        $filtered = $results->filter(function ($value) {
            return $value['order_status'] != null;
        })->values();

          if($filtered){
            return response()->json([
                'success' => true,
                'data' => $filtered,
            ]);
        }else{
            return response()->json([
                'success' => false,
                'data' => $filtered,
            ]); 
        } 
    }

    public function berjalanKerjaShow($provider_id){
        $status_id = '2';
        $results = Order_status::with(['order' => function ($query) {
            $query->with('user_client');
        }, 'proposal_by'])->where(['provider_id' => $provider_id, 'status_id' => $status_id])->get(); 
        
        if($results){
            return response()->json([
                'success' => true,
                'data' => $results,
            ]);
        }else{
            return response()->json([
                'success' => false,
                'data' => $results,
            ]);
        }
    }

    public function orderFeedbackShow($provider_id){
        $results =  Order_feedback::with(['client', 'order'])->where('for', $provider_id)->get();
        if($results){
            return response()->json([
                'success' => true,
                'data' => $results,
            ]);
        }else{
            return response()->json([
                'success' => false,
                'data' => $results,
            ]);
        }
    }
}
