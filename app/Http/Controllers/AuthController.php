<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
class AuthController extends Controller
{


    public function register(Request $request){
        /* $result = $request->json()->all(); */
        $this->validate($request, [
            'username' => 'required|string',
            'email'    => 'required|email|unique:rf_users',
            'password' => 'required|string|min:5|confirmed',
            'firstname'=> 'required|string',
            'lastname' => 'required|string',
        ]);
      return response()->json($request->all());
    }
}
