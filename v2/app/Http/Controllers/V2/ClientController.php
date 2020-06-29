<?php

namespace App\Http\Controllers\V2;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use App\Traits\ApiResponser;
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

class ClientController extends Controller
{
    use ApiResponser;
    public $accessToken;
    public $perPage = 5;

    public function __construct(){
        $this->accessToken = Auth::user()->token();
    }

    public function getOrderNew(){
        try {
            $results = OrderStatus::with(['order', 'user'])->where('changedby_id', $this->accessToken->user_id)->whereIn('status_id', ['1'])->orderBy('id', 'desc')->get();
            return $this->successResponse($results);
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage());
        }
    }

    public function getOrderRun(){
        try {
            $results = OrderStatus::with(['order', 'user'])->where('changedby_id', $this->accessToken->user_id)->whereIn('status_id', ['2'])->orderBy('id', 'desc')->get();
            return $this->successResponse($results);
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage());
        }
    }

    public function createOrder(Request $request){

        // Validate
        $this->validate($request, [
           'mulai' => 'required|string',
           'akhir' => 'required|string',
           'kegunaan' => 'required|string',
          ]);

       $hasil_array = $request->json('hasil');
       // Generate polygon
       $latlng_array = $request->json('latlng');
       $odd = array();
       $even = array();
       foreach ($latlng_array as $key => $value) {
           if ($key % 2 == 0) {
               $even[] = $value;
           }
           else {
               $odd[] = $value;
           }
       }


     // date("j M, Y", strtotime($b));

       if ($request->json('subject') == '') {
           // Generated subject
           $carbon = Carbon::now();
           $subject =  $carbon->format('d-M-Y H:i A');
       }else{
           $subject = $request->json('subject');
       }
       // Generated orderhours
      /*  $mulai = Carbon::createFromFormat('Y-m-d', $request->json('mulai'));
       $akhir = Carbon::createFromFormat('Y-m-d', $request->json('akhir'));
       $orderhourduration = $mulai->diffInHours($akhir); */


       // Store order
       $result_order = Order::create([
           'subject' => $subject,
           'createdby' => $request->json('createdby_id'),
           'dtprojectstart' => $mulai,
           'dtprojectend' => $akhir,
           'projecttype' => $request->json('kegunaan'),
           'comment' => $request->json('comment')
       ]);

       // Store order_outputs
       foreach($hasil_array as $hasil ){
           $result_order_output = OrderOutput::create([
               'order_id' => $result_order->id,
               'output_id' => $hasil
            ]);
       }
           // Store order_location
        $count = count($even);
           for ($i = 0; $i < $count ; $i++) {
               $result_order_polygon = OrderLocation::create([
                   'order_id' => $result_order->id,
                   'latitude' => $even[$i],
                   'longitude' => $odd[$i]
               ]);
       }

       // Store OrderStatus
       $status_id = '1';
       $result_order_status = OrderStatus::create([
           'order_id' => $result_order->id,
           'status_id' => $status_id,
           'changedby_id' => $request->json('createdby_id')
       ]);
       $client = new \GuzzleHttp\Client();

       $url = 'https://fcm.googleapis.com/fcm/send';
       $headers = [
           'Content-Type' =>'application/json',
           'Authorization' => 'key=AIzaSyBBM08AA_Gt0U0ov0pB0swrvfN9qiDKcqs'

       ];
       $notification = [
           "title" => "Proyek Tawaran",
           "body" => "Ada Tawaran Baru",
           "sound" => "default",
           "click_action" => "FCM_PLUGIN_ACTIVITY",
           "icon" =>"fcm_push_icon"
       ];

       $data = [
           "title" => "Proyek Tawaran",
           "body" => "Ada Tawaran Baru",
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
   $result_subscribe =  $response->getBody();
       if($result_order && $result_order_output && $result_order_polygon){
           return response()->json([
               'success' => true
               ]);
       }

   }

   public function updateOrderStatus (Request $request, $order_id){
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

   public function createFeedback(Request $request,$order_id){
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



    public function historyProvider($provider_id){
        try {
            $results = OrderStatus::with('order', 'output', 'order_feedback')->where(['provider_id' => $provider_id, 'status_id' => '3'])->get();
            return $this->successResponse($results);
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage());
        }
    }

    public function getPolygon($order_id){
        try {
            $result_polygon = OrderLocation::where('order_id', $order_id)->get();
            $result_output = OrderOutput::where('order_id', $order_id)->get();

                return response()->json([
                    'status' => 200,
                    'polygon' => $result_polygon,
                    'output' => $result_output
                ]);

        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage());
        }
   }

   public function getOrderProposal($order_id, $filter){
    try {
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

            return $this->successResponse($results);

        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage());
        }
    }

    public function getOrderRating($order_id){
        try {
            $results = OrderStatus::with('user')->where('order_id', $order_id)->where('status_id', '3')->first();
            return $this->successResponse($results);

        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage());
        }
    }

    public function getOrderHistory(){
        try {
            $results = OrderStatus::with('order', 'user')->where('changedby_id', $this->accessToken->user_id)->whereIn('status_id', ['3', '4'])->orderBy('updated_at', 'desc')->get();
            return $this->successResponse($results);

        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage());
        }
     }

    public function getProfilProvider($provider_id){
        try {
            $results = User::find($provider_id);
            return $this->successResponse($results);
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage());
        }
    }
}
