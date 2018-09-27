<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;

use App\Order;
class OrderController extends Controller
{
    public function create(Request $request){
  

        $this->validate($request, [
         'mulai' => 'required|string',
         'akhir' => 'required|string',
         'kegunaan' => 'required|string',
         'comment' => 'required|string',
         'latlng' => 'required|string',
        ]);

        $carbon = Carbon::now();
        $subject =  $carbon->format('d-M-Y H:i A');
        $awal = '2018-10-15';
        $b = '2018-10-17';
        return response()->json(["date" => date("j M, Y", strtotime($b))]);
        $mulai =  Carbon::createFromFormat('Y-m-d', $awal);
        $akhir = Carbon::createFromFormat('Y-m-d', $b);
         $orderhourduration = $mulai->diffInHours($akhir);
      /*   $result_order = Order::create([
            'subject' => $subject,
            'createdby' => $request->json('createdby_id'),
            'dtprojectstart' => $mulai,
            'dtprojectend' => $akhir,
            'projecttype' => $request->json('projecttype_id'),
            'orderhourduration' => ,
            'comment' => ''
        ]); */

    }
}
