<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Order;
class OrderController extends Controller
{
    public function create(Request $request){
        $this->validate($request, [
         'mulai' => 'required|string',
         'akhir' => 'required|string',
         'projecttype' => 'required|string',
         'comment' => 'required|string',
         'latlng' => 'required|string',
        ]);

        

    }
}
