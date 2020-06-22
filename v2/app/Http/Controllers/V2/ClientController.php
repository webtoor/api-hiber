<?php

namespace App\Http\Controllers\V2;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
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

    public function orderNew(){
        try {
            $results = OrderStatus::with(['order', 'user'])->where('changedby_id', $this->accessToken->user_id)->whereIn('status_id', ['1'])->orderBy('id', 'desc')->get();
            return $this->successResponse($results);
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage());
        }
    }

    public function orderRun(){
        try {
            $results = OrderStatus::with(['order', 'user'])->where('changedby_id', $this->accessToken->user_id)->whereIn('status_id', ['2'])->orderBy('id', 'desc')->get();
            return $this->successResponse($results);
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage());
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

}
