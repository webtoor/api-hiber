<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

global $app;
use App\User;
use Illuminate\Support\Facades\Hash;
use GuzzleHttp\Client;


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
            'registerType' => 'required|string'
        ]);

        $resultUser = User::create([
            'username' => $request->json('username'),
            'email' => $request->json('email'),
            'password' => Hash::make($request->json('password')),
            'firstname' => $request->json('firstname'),
            'lastname' => $request->json('lastname')
        ]);

        $resultRole = User_role::create([
            'user_id' => $resultUser->id,
            'rf_role_id' => $request->json('register_type')
        ]);


        return response()->json([
            'succes' => true,
            'message' => 'Berhasil membuat akun!'
        ], 201);
    }

    public function login (Request $request){
        /* $this->validate($request,[
            'email' => 'required',
            'password' => 'required'
        ]); */
            
      /*   $client = new Client();
        $response = $client->get('http://localhost:8000/test', 
        [ 'proxy'   => 'tcp://localhost:80',
      
            
        ]);
        echo $response->getStatusCode(); 
                
        $http = new GuzzleHttp\Client;

        $response = $http->post('http://localhost:8000/oauth/token', [
            'form_params' => [
                'grant_type' => 'password',
                'client_id' => 'client-id',
                'client_secret' => 'client-secret',
                'username' => 'taylor@laravel.com',
                'password' => 'my-password',
                'scope' => '',
            ],
        ]);

        return json_decode((string) $response->getBody(), true);*/
        global $app; 
        $proxy = Request::create(
            '/oauth/token',
            'post',
            [
                'grant_type'=>'password',
                'client_id'=>'1',
                'client_secret'=>'R1lAPTRrfvY102gLzVC1TU2hCq2gqfOUosNah4Mj',
                'username'=>'vaughn53@hotmail.com',
                'password'=>'rahasia',
                'scope' => '*'
            ]
    
        );
        $response = $app->dispatch($proxy);
         //dd($result);
         $json = (array) json_decode($response->getContent());
         $json['new_value'] = '123456';
         $response->setContent(json_encode($json));
         return $response;
    }
}
