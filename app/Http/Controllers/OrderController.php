<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function create(Request $request){
        $this->validate($request, [
         'dtprojectstart' => 'require|string|min:8',
         'dtprojectend' => 'require|string|min:8',
         'projecttype' => 'require|string',
         'comment' => 'required|string'
        ]);

    }
}
