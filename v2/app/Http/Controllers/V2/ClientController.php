<?php

namespace App\Http\Controllers\V2;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
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

}
