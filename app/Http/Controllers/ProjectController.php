<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Order;
use App\Order_status;
use App\Order_location;
use App\Order_output;
use App\Order_proposal;
use App\Order_feedback;
use App\User_feedback;
use App\User;


class ProjectController extends Controller
{
    public function baru_show($user_id){
        
     $results = Order_status::with(['order', 'user'])->where('changedby_id', $user_id)->whereIn('status_id', ['1'])->orderBy('id', 'desc')->get();

        if($results){
            return response()->json([
                'success' => true,
                'order_baru' => $results
            ]);
        }else{
            return response()->json([
                'success' => false,
            ]);
        }        
    }

    public function berjalan_show($user_id){
        
        $results = Order_status::with(['order', 'user'])->where('changedby_id', $user_id)->whereIn('status_id', ['2'])->orderBy('id', 'desc')->get();
   
           if($results){
               return response()->json([
                   'success' => true,
                   'order_berjalan' => $results
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
        $status = $request->json('status');
        $provider_id = $request->json('provider_id');
       
        if($provider_id){
             // GUNAKAN && SELESAI
            $result = Order_status::where('order_id',$order_id)->update(['status_id' => $status, 'provider_id' => $provider_id]);
        }else{
            // CANCEL
            $result = Order_status::where('order_id',$order_id)->update(['status_id' => $status]);
        }

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

    public function proposal($order_id, $filter){
        if($filter == '1'){
            // Default Data Terakhir
            $results = Order_proposal::with(['user', 'user_feedback'])
            ->where('order_id', $order_id)->orderBy('id', 'desc')->get();
        }elseif($filter == '2'){
            // Rating
            /*  $results = User_feedback::with(['user', 'proposal' => function ($query) use ($order_id) {
                $query->where('order_id', $order_id);
            }])->orderBy('total_rating', 'desc')->get(); */
            $rest = Order_proposal::with(['user', 'user_feedback'])
            ->where('order_id', $order_id)->orderBy('id', 'desc')->get();

            $rest = $rest->sortByDesc(function ($item, $key) {
                return ($item['user_feedback']['total_rating']);
            });

            $results = $rest->values()->all();
               
        }elseif($filter == '3'){
            // Termurah
            $results = Order_proposal::with(['user', 'user_feedback'])
            ->where('order_id', $order_id)->orderBy('offered_price', 'asc')->get();
        }elseif($filter == '4'){
            // Termahal
            $results = Order_proposal::with(['user', 'user_feedback'])
            ->where('order_id', $order_id)->orderBy('offered_price', 'desc')->get();
        }else{
            // Anythings
            $results = Order_proposal::with(['user', 'user_feedback'])
            ->where('order_id', $order_id)->get();
        }
        
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

    public function historyProvider($provider_id){
        $results = Order_status::with('order', 'output', 'order_feedback')->where(['provider_id' => $provider_id, 'status_id' => '3'])->get();

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
    public function getRating($order_id){
        $results = Order_status::with('user')->where('order_id', $order_id)->where('status_id', '3')->first();
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
    public function feedback(Request $request,$order_id){
        $writter = $request->json('writter');
        $for = $request->json('for');
        $rating = $request->json('rating');
        $comment = $request->json('comment');

        // INSERT ORDER FEEDBACK
        $results = Order_feedback::create([
            'writter' => $writter,
            'for' => $for,
            'order_id' => $order_id,
            'rating' => $rating,
            'comment' => $comment,
        ]);
        // TOTAL RATING PROVIDER ORDER_FEEDBACK
        $total_rating = Order_feedback::where('for', $for)->avg('rating');
        
        // CHECK PROVIDER
        $check_provider =  User_feedback::where('user_id', $for)->first();
            
        if($check_provider){
            // JIKA ADA
            $result_final = User_feedback::where('user_id', $for)->update([
                'total_rating' => $total_rating
            ]);
        }else{
            // TIDAK ADA
           $result_final = User_feedback::create([
                'user_id' => $for,
                'total_rating' => $total_rating,
            ]);
        }

        if($result_final){
            return response()->json([
                'success' => true,
            ]);
        }else{
            return response()->json([
                'success' => false,
            ]);
        } 
    }


    public function history ($user_id){
        $results = Order_status::with('order', 'user')->where('changedby_id', $user_id)->whereIn('status_id', ['3', '4'])->orderBy('id', 'desc')->get();
         
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

    public function profilProvider($user_id){
        $results = User::find($user_id);

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
