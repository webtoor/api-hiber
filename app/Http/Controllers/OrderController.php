<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Order;
use App\Order_output;
use App\Order_location;

class OrderController extends Controller
{
    public function create(Request $request){
        
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
            'comment' => $request->json('comment')
        ]);  
        foreach($hasil_array as $hasil ){
            $result_order_output = Order_output::create([
                'order_id' => $result_order->id,
                'output_id' => $hasil
             ]);
        }
            // Store order_location
         $count = count($even);
            for ($i = 0; $i < $count ; $i++) {
                $result_order_polygon = Order_location::create([
                    'order_id' => $result_order->id,
                    'latitude' => $even[$i],
                    'longitude' => $odd[$i]
                ]);
        }

        if($result_order && $result_order_output && $result_order_polygon){
            return response()->json([
                'success' => true
                ]);
        } 

    }
}
