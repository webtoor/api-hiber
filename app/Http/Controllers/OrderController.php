<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;

use App\Order;
class OrderController extends Controller
{
    public function create(Request $request){
        
        //return response()->json($request);

        $this->validate($request, [
         'mulai' => 'required|string',
         'akhir' => 'required|string',
         'kegunaan' => 'required|string',
        ]);

        $carbon = Carbon::now();
        $subject =  $carbon->format('d-M-Y H:i A');
      // date("j M, Y", strtotime($b));
        $mulai = Carbon::createFromFormat('Y-m-d', $request->json('mulai'));
        $akhir = Carbon::createFromFormat('Y-m-d', $request->json('akhir'));
        $orderhourduration = $mulai->diffInHours($akhir);
         $result_order = Order::create([
            'subject' => $subject,
            'createdby' => $request->json('createdby_id'),
            'dtprojectstart' => $mulai,
            'dtprojectend' => $akhir,
            'projecttype' => $request->json('kegunaan'),
            'orderhourduration' => $orderhourduration,
            'comment' => 'adssda'
        ]);  
        if($result_order){
            return response()->json([
                'success' => true
                ]);
        }

    }
}
