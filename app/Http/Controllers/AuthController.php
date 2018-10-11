<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use App\User;
use App\User_role;

class AuthController extends Controller
{
    public function register(Request $request){
        /* $result = $request->json()->all(); */
            // Validasi
       $this->validate($request, [
            'username' => 'required|string',
            'email'    => 'required|email|unique:rf_users',
            'phonenumber' => 'required|numeric|min:10',
            'password' => 'required|string|min:5|confirmed',
            'registerType' => 'required|string'
        ]);

            // Create User
        $resultUser = User::create([
            'username' => $request->json('username'),
            'email' => $request->json('email'),
            'phonenumber' => $request->json('phonenumber'),
            'password' => Hash::make($request->json('password')),
        ]);
            // Create User role 
        $resultRole = User_role::create([
            'user_id' => $resultUser->id,
            'rf_role_id' => $request->json('registerType')
        ]);
        if($resultUser && $resultRole){
            return response()->json([
                'success' => true,
                'message' => 'Berhasil membuat akun!'
            ], 201);
        }else{
            return response()->json([
                'success' => false,
                'message' => 'Gagal membuat akun!'
            ], 400);
        }
     
    }

    public function login_user (Request $request){

         $this->validate($request,[
            'email' => 'required',
            'password' => 'required',
        ]);    

        global $app; 

        $email = $request->json('email');
        $password = $request->json('password');
        $user_role = '2';

        $resultUser = User::where('email', $email)->first();
            if(!$resultUser){
                // Email not exist 
                return response()->json([
                    "error" => "invalid_credentials",
                    "message" => "The user credentials were incorrect"
                   ]);
            }
            if(Hash::check($password, $resultUser->password) ) {

                // Email && Password exist + Check user_role
                if(($user_role) == ($resultUser->role->rf_role_id)){

                $params = [
                    'grant_type'=>'password',
                    'client_id' => '2',
                    'client_secret'=> 'QiH3rLWJ0aY0vYCh7czqiX8m9yOMaFEOvIdMFsFh',
                    'username'  => $email,
                    'password'  => $password,
                    'scope'     => '*'
                ];
                
                $proxy = Request::create('/oauth/token','post', $params);
                $response = $app->dispatch($proxy);
                $json = (array) json_decode($response->getContent());
                $json['id'] = $resultUser->id;
                $json['email'] = $resultUser->email;
                return $response->setContent(json_encode($json)); 

                }else{
                    // != User role
                    return response()->json([
                     "error" => "invalid_credentials",
                     "message" => "The user credentials were incorrect"
                    ]);
                }
            }else{
                //Email exist, Password not exist
                return response()->json([
                    "error" => "invalid_credentials",
                    "message" => "The user credentials were incorrect"
                   ]);
            } 
    }

    public function login_provider (Request $request){

        $this->validate($request,[
           'email' => 'required',
           'password' => 'required',
       ]);    

       global $app; 

       $email = $request->json('email');
       $password = $request->json('password');
       $user_role = '1';

       $resultUser = User::where('email', $email)->first();
           if(!$resultUser){
               // Email not exist 
               return response()->json([
                   "error" => "invalid_credentials",
                   "message" => "The user credentials were incorrect"
                  ]);
           }
           if(Hash::check($password, $resultUser->password) ) {

               // Email && Password exist + Check user_role
               if(($user_role) == ($resultUser->role->rf_role_id)){

               $params = [
                   'grant_type'=>'password',
                   'client_id' => '1',
                   'client_secret'=> 'R1lAPTRrfvY102gLzVC1TU2hCq2gqfOUosNah4Mj',
                   'username'  => $email,
                   'password'  => $password,
                   'scope'     => '*'
               ];
               
               $proxy = Request::create('/oauth/token','post', $params);
               $response = $app->dispatch($proxy);
               $json = (array) json_decode($response->getContent());
               $json['id'] = $resultUser->id;
               $json['email'] = $resultUser->email;
               return $response->setContent(json_encode($json)); 

               }else{
                   // != User role
                   return response()->json([
                    "error" => "invalid_credentials",
                    "message" => "The user credentials were incorrect"
                   ]);
               }
           }else{
               //Email exist, Password incorrect
               return response()->json([
                   "error" => "invalid_credentials",
                   "message" => "The user credentials were incorrect"
                  ]);
           } 
   }

    public function logout(){
       $accessToken = Auth::user()->token();
            DB::table('oauth_refresh_tokens')
                ->where('access_token_id', $accessToken->id)
                ->update(['revoked' => true]);
                
            DB::table('oauth_refresh_tokens')
                ->where('access_token_id', $accessToken->id)
                ->delete();
                $accessToken->revoke();
                $accessToken->delete();

        return response()->json([
            'success' => true,
            'message' => 'Berhasil logout']);
    }

    public function testsatu (Request $request){
        $email = $request->json('email');

         $resultUser = User::where('email', $email)->first();
         return $resultUser->role;
        
    }

}
