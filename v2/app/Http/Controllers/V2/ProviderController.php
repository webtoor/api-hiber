<?php

namespace App\Http\Controllers\V2;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Traits\ApiResponser;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\Order;
use App\Models\OrderStatus;
use App\Models\OrderProposal;

class ProviderController extends Controller
{
    use ApiResponser;
    public $accessToken;
    public $perPage = 5;

    public function __construct(){
        $this->accessToken = Auth::user()->token();
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
}
