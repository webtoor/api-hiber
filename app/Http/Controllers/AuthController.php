<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{


    public function register(Request $request){
        /* $result = $request->json()->all(); */

        $result = $request->all();

        return response()->json($result);
    }
}
