<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;

use App\Order;
class OrderController extends Controller
{
    public function create(Request $request){
        $carbon = Carbon::now();
        return $carbon->format('d-M-Y H:i A');

        $this->validate($request, [
         'mulai' => 'required|string',
         'akhir' => 'required|string',
         'projecttype' => 'required|string',
         'comment' => 'required|string',
         'latlng' => 'required|string',
        ]);

        $result_order = Order::create([
            'subject' => $subject
        ]);

    }
}
