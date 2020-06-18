<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use GuzzleHttp\Client;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;
use App\Models\Order;
use App\Models\OrderStatus;
use App\Models\OrderLocation;
use App\Models\OrderOutput;
use App\Models\OrderProposal;
use App\Models\OrderFeedback;
use App\Models\User;
use App\Models\UserFeedback;
use App\Models\DeviceToken;

use Illuminate\Support\Facades\Auth;

class ProviderProjectController extends Controller
{
    public $perPage = 5;

    public function getRatingShow($provider_id){
        $results =  UserFeedback::where('user_id', $provider_id)->first();
        if($results){
            return response()->json([
                'status' => "1",
                'data' => $results
            ]);
        }else{
            return response()->json([
                'status' => "0",
                'data' => $results
            ]);
        }
    }
    public function tawaranShow($provider_id, $projecttype){
        $status_id = '1';
        $order_id = OrderProposal::where('proposal_by', $provider_id)->get();
        $filterProject = $projecttype;
        if (count($order_id) > 0) {
            foreach($order_id as $orders){
                $array_order_id[] = $orders['order_id'];
            }
            if($filterProject != '0'){
               $results = OrderStatus::with(['order'  => function ($query) use ($filterProject) {
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
                $results = OrderStatus::with(['order'  => function ($query) use ($filterProject) {
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


        if($newresults){
            return response()->json([
                'status' => "1",
                'data' => $newresults
            ]);
        }else{
            return response()->json([
                'status' => "0",
                'data' => $newresults
            ]);
        }

    }

    public function detailShow($order_id){
        $results_polygon = OrderLocation::where('order_id', $order_id)->get();
        $results_output = OrderOutput::where('order_id', $order_id)->get();
        if($results_polygon && $results_output){
            return response()->json([
                'status' => '1',
                'polygon' => $results_polygon,
                'output'   => $results_output,
            ]);
        }else{
            return response()->json([
                'status' => '0',
                'polygon' => $results_polygon,
                'output'   => $results_output,
            ]);
        }
    }

    public function bidding(Request $request){
       /*  return response()->json([
            'status' => "2",
            'data' => $request->all()
        ]); */
        $this->validate($request, [
            'offered_price' => 'required',
           ]);
        $order_id = $request->json('order_id');
        $proposal_by = $request->json('proposal_by');
        $offered_price = $request->json('offered_price');
        $comment = $request->json('comment');
        $order_results = Order::find($order_id);
        $results_token = DeviceToken::where(['role_id' => '2','user_id' => $order_results->createdby])->OrderBy('id', 'desc')->first();

            $checks = OrderProposal::where(['order_id' => $order_id, 'proposal_by' => $proposal_by])->get();

            if (count($checks) < 1) {
                //Problem here
                $hasil = OrderProposal::create([
                'order_id' => $order_id,
                'proposal_by' => $proposal_by,
                'offered_price' => $offered_price,
                'comment' => $comment,
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
                "order_id" => $order_id,
                "subject" => $order_results->subject,
                "sound" => "default",
                "click_action" => "FCM_PLUGIN_ACTIVITY",
                "icon" =>"fcm_push_icon"
            ];

                $data = [
                "title" => "Status Proyek",
                "body" => "Ada yang bid proyek anda",
                "order_id" => $order_id,
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
                'data' => $data,
                "to" => $results_token->token,
                "priority" => "high"
            ])
        ]);
                $result_subscribe =  $response->getBody();
        }
        return response()->json([
            'status' => "1",
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




    }

    /* public function berjalanIkutiShow($provider_id){
        $status_id = '1';
        $results = OrderProposal::with(['order' => function ($query) {
            $query->with('user_client');
        }, 'order_status' => function ($query) {
            $query->where('status_id', '1');
        }])->where('proposal_by', $provider_id)->orderBy('id', 'desc')->paginate(5);
        $results_data =  $results->getCollection()->transform(function ($value) {
            // Your code here
            return $value;
        });
        $filtered = $results_data->filter(function ($value) {
            return $value['order_status'] != null;
        })->values();
        $filtered_count = $results_data->filter(function ($value) {
            return $value['order_status'] == null;
        })->values();

        $newresults = new \Illuminate\Pagination\LengthAwarePaginator(
            $filtered,
            $results->total() - count($filtered_count),
            $results->perPage()
        );
          if($filtered){
            return response()->json([
                'status' => '1',
                'data' => $newresults
            ]);
        }else{
            return response()->json([
                'status' => '0',
                'data' => $newresults
            ]);
        }
    } */

    public function berjalanIkutiShow($provider_id){
         $status_id = '1';
         $results = OrderProposal::with(['order' => function ($query) {
            $query->with('user_client');
        }, 'order_status' => function ($query) {
            $query->where('status_id', '1');
        }])->where('proposal_by', $provider_id)->orderBy('id', 'desc')->get();

        $filtered = $results->filter(function ($value) {
            return $value['order_status'] != null;
        })->values();

          if($filtered){
            return response()->json([
                'status' => '1',
                'data' => $filtered,
            ]);
        }else{
            return response()->json([
                'status' => '0',
                'data' => $filtered,
            ]);
        }
    }

    public function cancelBid(Request $request){

        $id = $request->json('id');
        $results = OrderProposal::where('id', $id)->delete();
         // ALTER TABLE tablename AUTO INCREMENT = 1
         $max = DB::table('order_proposals')->max('id') + 1;
         DB::statement("ALTER TABLE order_proposals AUTO_INCREMENT = $max");

         return response()->json([
            'status' => '1',
        ]);
    }

    public function editPenawaran(){
        $id = $request->json('id');
        $offered_price = $request->json('offered_price');

        $results = OrderProposal::where('id', $id)->update(['offered_price'=> $offered_price]);
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
    public function berjalanKerjaShow($provider_id){

        $status_id = '2';
        $results = OrderStatus::with(['order' => function ($query) {
            $query->with('user_client');
        }, 'proposal_by'])->where(['provider_id' => $provider_id, 'status_id' => $status_id])->orderBy('id', 'desc')->get();


        if($results){
            return response()->json([
                'status' => '1',
                'data' => $results,
            ]);
        }else{
            return response()->json([
                'status' => '0',
                'data' => $results,
            ]);
        }
    }

    public function orderFeedbackShow($provider_id){
        $results = OrderFeedback::with(['client', 'order'])->where('for', $provider_id)->orderBy('updated_at', 'desc')->get();
        if($results){
            return response()->json([
                'status' => "1",
                'data' => $results,
            ]);
        }else{
            return response()->json([
                'status' => "0",
                'data' => $results,
            ]);
        }
    }


    public function sendEmail(Request $request){
        $order_id = $request->json('order_id');
        $email = $request->json('email');
        $results = User::where('email', $email)->first();
        $result_order = Order::where('id', $order_id)->first();
        $name_order = $result_order['subject'];
        $user = new \stdClass();
        $user->email = $email;
        $user->name = $results['username'];
        $user->name_order = $result_order['subject'];

       /*  Mail::raw("Test",function ($mail) use ($user) {
                $mail->to($user->email, $user->name)->subject('Test Subject');
                $mail->from('fmunshi@eidaramata.com','Fityan Ali');
        }); */

        Mail::send(["html" => "emailExport"], ['order_id' => $order_id, 'email' => $email, 'username' => $user->name ], function($mail) use ($user){
            $mail->to($user->email, $user->name)->subject("Proyek $user->name_order");
            $mail->from('noreply@eidaramata.com','Hiber Eidara Matadata Presisi');
        });
        return response()->json([
                'status' => "1",
                'message' => 'Berhasil kirim email!',
                'email' => $email
        ]);
    }

    public function exportLatLong($order_id){
        $results = OrderLocation::where('order_id', $order_id)->get();
        $result_order = Order::where('id', $order_id)->first();
        $name_order = $result_order['subject'];
        return view('export_kml', ['koordinat' => $results, 'name_order' => $name_order ]);
    }
}
