<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;

use App\Order;
class OrderController extends Controller
{
    public function create(Request $request){
        
        //return response()->json($request);
        $hasil_array = $request->json('hasil');
      
        $this->validate($request, [
         'mulai' => 'required|string',
         'akhir' => 'required|string',
         'kegunaan' => 'required|string',
        ]);
        
        // Generated subject
        $carbon = Carbon::now();
        $subject =  $carbon->format('d-M-Y H:i A');
      // date("j M, Y", strtotime($b));
        // Generated orderhours
        $mulai = Carbon::createFromFormat('Y-m-d', $request->json('mulai'));
        $akhir = Carbon::createFromFormat('Y-m-d', $request->json('akhir'));
        $orderhourduration = $mulai->diffInHours($akhir);
        // Store order
           $result_order = Order::create([
            'subject' => $subject,
            'createdby' => $request->json('createdby_id'),
            'dtprojectstart' => $mulai,
            'dtprojectend' => $akhir,
            'projecttype' => $request->json('kegunaan'),
            'orderhourduration' => $orderhourduration,
            'comment' => 'adssda'
        ]);  
          foreach($hasil_array as $hasil ){
             $result_order_output = adasdsa;
        }
        if($result_order){
            return response()->json([
                'success' => true
                ]);
        } 

    }
}
