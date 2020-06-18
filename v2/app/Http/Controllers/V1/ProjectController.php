<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use GuzzleHttp\Client;
use App\Models\Order;
use App\Models\OrderStatus;
use App\Models\OrderLocation;
use App\Models\OrderOutput;
use App\Models\OrderProposal;
use App\Models\OrderFeedback;
use App\Models\UserFeedback;
use App\Models\User;
use App\Models\DeviceToken;

class ProjectController extends Controller
{
    public function baru_show($user_id){

     $results = OrderStatus::with(['order', 'user'])->where('changedby_id', $user_id)->whereIn('status_id', ['1'])->orderBy('id', 'desc')->get();

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

        $results = OrderStatus::with(['order', 'user'])->where('changedby_id', $user_id)->whereIn('status_id', ['2'])->orderBy('id', 'desc')->get();

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
         $result_polygon = OrderLocation::where('order_id', $order_id)->get();
         $result_output = OrderOutput::where('order_id', $order_id)->get();

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
        $results_token = DeviceToken::where('user_id', $provider_id)->OrderBy('id', 'desc')->first();
        if($provider_id){
             // GUNAKAN
             if($status == "2"){
                $result = OrderStatus::where('order_id',$order_id)->update(['status_id' => $status, 'provider_id' => $provider_id]);
                $client = new \GuzzleHttp\Client();

                        $url = 'https://fcm.googleapis.com/fcm/send';
                        $headers = [
                            'Content-Type' =>'application/json',
                            'Authorization' => 'key=AIzaSyBBM08AA_Gt0U0ov0pB0swrvfN9qiDKcqs'

                        ];
                        $notification = [
                            "title" => "Proyek Berjalan",
                            "body" => "Ada yang menggunakan jasa anda",
                            "sound" => "default",
                            "click_action" => "FCM_PLUGIN_ACTIVITY",
                            "icon" =>"fcm_push_icon"
                        ];

                        $data = [
                            "title" => "Proyek Berjalan",
                            "body" => "Ada yang menggunakan Jasa anda",
                            "action" => "bekerja",
                            "forceStart" => "1"
                        ];

                    $response = $client->post('https://fcm.googleapis.com/fcm/send', [
                        'headers' => ['Content-Type' => 'application/json',
                        'Authorization' => 'key=AIzaSyBBM08AA_Gt0U0ov0pB0swrvfN9qiDKcqs'
                    ],
                        'body' => json_encode([
                            'notification'=> $notification,
                            'data' => $data,
                            "to" => $results_token->token,
                            "priority" => "high"
                        ])
                    ]);
                    $result_subscribe =  $response->getBody();
             }elseif($status == "3"){
               // SELESAI
                $result = OrderStatus::where('order_id',$order_id)->update(['status_id' => $status, 'provider_id' => $provider_id]);
             }
        }else{
            // CANCEL
            $result = OrderStatus::where('order_id',$order_id)->update(['status_id' => $status]);
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
            $results = OrderProposal::with(['user', 'user_feedback'])
            ->where('order_id', $order_id)->orderBy('id', 'desc')->get();
        }elseif($filter == '2'){
            // Rating
            /*  $results = UserFeedback::with(['user', 'proposal' => function ($query) use ($order_id) {
                $query->where('order_id', $order_id);
            }])->orderBy('total_rating', 'desc')->get(); */
            $rest = OrderProposal::with(['user', 'user_feedback'])
            ->where('order_id', $order_id)->orderBy('id', 'desc')->get();

            $rest = $rest->sortByDesc(function ($item, $key) {
                return ($item['user_feedback']['total_rating']);
            });

            $results = $rest->values()->all();

        }elseif($filter == '3'){
            // Termurah
            $results = OrderProposal::with(['user', 'user_feedback'])
            ->where('order_id', $order_id)->orderBy('offered_price', 'asc')->get();
        }elseif($filter == '4'){
            // Termahal
            $results = OrderProposal::with(['user', 'user_feedback'])
            ->where('order_id', $order_id)->orderBy('offered_price', 'desc')->get();
        }else{
            // Anythings
            $results = OrderProposal::with(['user', 'user_feedback'])
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
        $results = OrderStatus::with('order', 'output', 'order_feedback')->where(['provider_id' => $provider_id, 'status_id' => '3'])->get();

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
        $results = OrderStatus::with('user')->where('order_id', $order_id)->where('status_id', '3')->first();
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
        $results = OrderFeedback::create([
            'writter' => $writter,
            'for' => $for,
            'order_id' => $order_id,
            'rating' => $rating,
            'comment' => $comment,
        ]);
        // TOTAL RATING PROVIDER ORDER_FEEDBACK
        $total_rating = OrderFeedback::where('for', $for)->avg('rating');

        // CHECK PROVIDER
        $check_provider =  UserFeedback::where('user_id', $for)->first();

        if($check_provider){
            // JIKA ADA
            $result_final = UserFeedback::where('user_id', $for)->update([
                'total_rating' => $total_rating
            ]);
        }else{
            // TIDAK ADA
           $result_final = UserFeedback::create([
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
        $results = OrderStatus::with('order', 'user')->where('changedby_id', $user_id)->whereIn('status_id', ['3', '4'])->orderBy('updated_at', 'desc')->get();

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

    public function testFCM(Request $request){
        $client = new \GuzzleHttp\Client();

        $url = 'https://fcm.googleapis.com/fcm/send';
        $headers = [
            'Content-Type' =>'application/json',
            'Authorization' => 'key=AIzaSyBBM08AA_Gt0U0ov0pB0swrvfN9qiDKcqs'

        ];
        $notification = [
            "title" => "Notification title",
            "body" => "Notification body",
            "sound" => "default",
            "click_action" => "FCM_PLUGIN_ACTIVITY",
            "icon" =>"fcm_push_icon"
        ];

        $data = [
            "title" => "actual data title",
            "body" => "actual data body",
            "action" => "tawaran",
            "forceStart" => "1"
        ];
        $params = [
            'notification'=> $notification,
            'data' => $data,
            "to" => "/topics/tawaran",
            "priority" => "high"
        ];

    $response = $client->post('https://fcm.googleapis.com/fcm/send', [
        'headers' => ['Content-Type' => 'application/json',
        'Authorization' => 'key=AIzaSyBBM08AA_Gt0U0ov0pB0swrvfN9qiDKcqs'
    ],
        'body' => json_encode([
            'notification'=> $notification,
            'data' => $data,
            "to" => "/topics/tawaran",
            "priority" => "high"
        ])
    ]);
    return $response->getBody();
    }
}

