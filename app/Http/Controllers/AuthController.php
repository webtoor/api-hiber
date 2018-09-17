<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\User;
use Illuminate\Support\Facades\Hash;

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

        User::create([
            'username' => $request->json('username'),
            'email' => $request->json('email'),
            'password' => Hash::make($request->json('password')),
            'firstname' => $request->json('firstname'),
            'lastname' => $request->json('lastname')
        ]);
        return response()->json([
            'succes' => true,
            'message' => 'Berhasil membuat akun!'
        ], 201);
    }
}
