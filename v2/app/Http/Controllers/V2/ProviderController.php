<?php

namespace App\Http\Controllers\V2;

use App\Http\Controllers\Controller;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use App\Traits\ApiResponser;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Order;
use App\Models\OrderStatus;
use App\Models\OrderProposal;
use App\Models\OrderLocation;
use App\Models\OrderOutput;
use App\Models\DeviceToken;
use App\Models\UserFeedback;
use App\Models\OrderFeedback;

class ProviderController extends Controller
{
    use ApiResponser;
    public $accessToken;
    public $perPage = 5;

    public function __construct(){
        $this->accessToken = Auth::user()->token();
    }

    public function getRating(){
        try {
            $provider_id = $this->accessToken->user_id;
            $results =  UserFeedback::where('user_id', $provider_id)->first();
            return $this->successResponse($results);

        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage());
        }

    }
    public function getOffer($projecttype){
        $provider_id =  $this->accessToken->user_id;
        $status_id = '1';
        try {
            $order_id = OrderProposal::where('proposal_by', $provider_id)->get();
            $filterProject = $projecttype;
            if (count($order_id) > 0) {
                foreach($order_id as $orders){
                    $array_order_id[] = $orders['order_id'];
                }
                if($filterProject != '0'){
                   $results = OrderStatus::with(['order'  => function ($query) use ($filterProject, $status_id, $array_order_id) {
                        $query->where('projecttype', $filterProject);
                    },'user_clients'])->where('status_id', $status_id)
                    ->whereNotIn('order_id', $array_order_id)->orderBy('id', 'desc')->get()->filter(function ($value) {
                        return $value['order'] != null;
                    })->values();


                    $newresults = new \Illuminate\Pagination\LengthAwarePaginator(
                        $results->slice((\Illuminate\Pagination\LengthAwarePaginator::resolveCurrentPage() *
                        $this->perPage)-$this->perPage,
                        $this->perPage)->values(), count($results),
                        $this->perPage, null, ['path' => '']);
                }else{
                    //Default Show
                    $newresults = OrderStatus::with(['order','user_clients'])->where('status_id', $status_id)->whereNotIn('order_id', $array_order_id)->orderBy('id', 'desc')->paginate(5);
                }
            }else{
                if($filterProject != '0'){
                    $results = OrderStatus::with(['order'  => function ($query) use ($filterProject, $status_id) {
                        $query->where('projecttype', $filterProject);
                    },'user_clients'])->where('status_id', $status_id)->orderBy('id', 'desc')->get()->filter(function ($value) {
                        return $value['order'] != null;
                    })->values();

                    $newresults = new \Illuminate\Pagination\LengthAwarePaginator(
                        $results->slice((\Illuminate\Pagination\LengthAwarePaginator::resolveCurrentPage() *
                        $this->perPage)-$this->perPage,
                        $this->perPage)->values(), count($results),
                        $this->perPage, null, ['path' => '']);

                }else{
                    //Default Show
                    $newresults = OrderStatus::with(['order' ,'user_clients'])->where('status_id', $status_id)->orderBy('id', 'desc')->paginate(5);
                }

            }

            return $this->successResponse($newresults);

        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage());
        }
    }

    public function getOfferDetail($order_id){
        try {
            $results_polygon = OrderLocation::where('order_id', $order_id)->get();
            $results_output = OrderOutput::where('order_id', $order_id)->get();
            return response()->json([
                'status' => 200,
                'polygon' => $results_polygon,
                'output'   => $results_output,
            ]);
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage());
        }
    }

    public function postBidOffer(Request $request){

         $data = $this->validate($request, [
             'offered_price' => 'required',
             'proposa_by' => 'required',
             'order_id' => 'required',
             'comment' => 'nullable'
            ]);

        try {
            $order_results = Order::find($data["order_id"]);
            $results_token = DeviceToken::where(['role_id' => '2','user_id' => $order_results->createdby])->OrderBy('id', 'desc')->first();

                $checks = OrderProposal::where(['order_id' => $data["order_id"], 'proposal_by' => $data["proposa_by"]])->get();

                if (count($checks) < 1) {
                    //Problem here
                    $hasil = OrderProposal::create([
                    'order_id' => $data["order_id"],
                    'proposal_by' => $data["proposa_by"],
                    'offered_price' => $data["offered_price"],
                    'comment' => $data["comment"],
                ]);

                if ($results_token) {
                    $client = new \GuzzleHttp\Client();

                    $url = 'https://fcm.googleapis.com/fcm/send';
                    $headers = [
                    'Content-Type' =>'application/json',
                    'Authorization' => 'key=AIzaSyAoU0v2lfCkHgBcWV0xWzOb6l0lG8UcGDo'

                ];
                    $notification = [
                    "title" => "Status Proyek",
                    "body" => "Ada yang bid proyek anda ",
                    "order_id" => $data["order_id"],
                    "subject" => $order_results->subject,
                    "sound" => "default",
                    "click_action" => "FCM_PLUGIN_ACTIVITY",
                    "icon" =>"fcm_push_icon"
                ];

                    $params = [
                    "title" => "Status Proyek",
                    "body" => "Ada yang bid proyek anda",
                    "order_id" => $data["order_id"],
                    "subject" => $order_results->subject,
                    "action" => "bidprovider",
                    "forceStart" => "1"
                ];

                    $response = $client->post('https://fcm.googleapis.com/fcm/send', [
                'headers' => ['Content-Type' => 'application/json',
                'Authorization' => 'key=AIzaSyAoU0v2lfCkHgBcWV0xWzOb6l0lG8UcGDo'
            ],
                'body' => json_encode([
                    'notification'=> $notification,
                    'data' => $params,
                    "to" => $results_token->token,
                    "priority" => "high"
                ])
            ]);
            $result_subscribe = $response->getBody();
            }
            return response()->json([
                'status' => 200,
                'data' => $hasil
            ]);
            } else{
                $hasil = null;
                return response()->json([
                    'status' => "2",
                    'message' => "double",
                    'data' => $hasil
                ]);
            }
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage());
        }
     }

    public function getProjectRunFollow(){
        $status_id = '1';
        $provider_id = $this->accessToken->user_id;
        try {
            $results = OrderProposal::with(['order' => function ($query) {
                $query->with('user_client');
            }, 'order_status' => function ($query) use ($status_id) {
                $query->where('status_id', $status_id);
            }])->where('proposal_by', $provider_id)->orderBy('id', 'desc')->get();

            $filtered = $results->filter(function ($value) {
                return $value['order_status'] != null;
            })->values();

            return $this->successResponse($filtered);

        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage());
        }
   }

   public function postBidCancel(Request $request){
       $id = $request->json('id');
       try {
            $results = OrderProposal::where('id', $id)->delete();
            // ALTER TABLE tablename AUTO INCREMENT = 1
            $max = DB::table('order_proposals')->max('id') + 1;
            DB::statement("ALTER TABLE order_proposals AUTO_INCREMENT = $max");
            return $this->successPostResponse();

        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage());
        }
   }

   public function getProjectRunWork(){
        $status_id = '2';
        $provider_id = $this->accessToken->user_id;
        try {
            $results = OrderStatus::with(['order' => function ($query) use ($status_id) {
                $query->with('user_client');
            }, 'proposal_by'])->where(['provider_id' => $provider_id, 'status_id' => $status_id])->orderBy('id', 'desc')->get();
            return $this->successResponse($results);
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage());
        }
    }

    public function getOrderFeedback(){

        $provider_id = $this->accessToken->user_id;
        try {
            $results = OrderFeedback::with(['client', 'order'])->where('for', $provider_id)->orderBy('updated_at', 'desc')->get();
            return $this->successResponse($results);

        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage());
        }
    }


    public function postOfferDetailSendEmail(Request $request){
        $data = $this->validate($request, [
            'order_id' => 'required',
            'email' => 'required',
           ]);
        try {
            $results = User::where('email', $data["email"])->first();
            $result_order = Order::where('id', $data["order_id"])->first();
            $name_order = $result_order['subject'];
            $user = new \stdClass();
            $user->email = $data["email"];
            $user->name = $results['username'];
            $user->name_order = $result_order['subject'];

           /*  Mail::raw("Test",function ($mail) use ($user) {
                    $mail->to($user->email, $user->name)->subject('Test Subject');
                    $mail->from('fmunshi@eidaramata.com','Fityan Ali');
            }); */

            Mail::send(["html" => "emailExport"], ['order_id' => $data["order_id"], 'email' => $data["email"], 'username' => $user->name ], function($mail) use ($user){
                $mail->to($user->email, $user->name)->subject("Proyek $user->name_order");
                $mail->from('noreply@eidaramata.com','Hiber Eidara Matadata Presisi');
            });
            return response()->json([
                'status' => 200,
                'message' => 'Berhasil kirim email!',
                'email' => $data["email"]
            ]);
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage());
        }
    }

    public function exportLatLong($order_id){
        $results = OrderLocation::where('order_id', $order_id)->get();
        $result_order = Order::where('id', $order_id)->first();
        $name_order = $result_order['subject'];
        return view('export_kml', ['koordinat' => $results, 'name_order' => $name_order ]);
    }
}
