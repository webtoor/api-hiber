<?php

namespace App\Http\Controllers\V2;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Mail;
use App\Traits\ApiResponser;
use App\Models\User;
use App\Models\UserRole;
use App\Models\UserFeedback;
use App\Models\DeviceToken;


class AuthController extends Controller
{
    use ApiResponser;

    public function register(Request $request){

        $data = $this->validate($request, [
              'username' => 'required|string',
              'email'    => 'required|email|unique:rf_users',
              'phonenumber' => 'required|min:10',
              'password' => 'required|string|min:5|confirmed',
              'registerType' => 'required|string'
        ]);

        try {
            $resultUser = User::create([
                'username' => $data["username"],
                'email' => $data["email"],
                'phonenumber' => $data["phonenumber"],
                'password' => Hash::make($data["password"]),
            ]);

            $resultRole = UserRole::create([
                'user_id' => $resultUser->id,
                'rf_role_id' => $data["registerType"]
            ]);

            // If Provider
            if($request->json('registerType') == '1')
                $resultFeedback = UserFedback::create([
                'user_id' => $resultUser->id,
                'total_rating' => 0,
            ]);

            return $this->successPostResponse();

        } catch (\Exception $e) {

            return $this->errorResponse($e->getMessage());
        }
    }

    public function loginClient(Request $request){

        $data = $this->validate($request,[
            'email' => 'required',
            'password' => 'required',
            'device_token' => 'required'
        ]);
        try {
            global $app;
            $user_role = '2';
            $checkUser = User::where('email', $data['email'])->first();
            if($checkUser){
                if (Hash::check($data['password'], $checkUser->password)) {
                    if (($user_role) == ($checkUser->role_user->rf_role_id)) {

                        $params = [
                            'grant_type'=>'password',
                            'client_id' => '2',
                            'client_secret'=> 'QiH3rLWJ0aY0vYCh7czqiX8m9yOMaFEOvIdMFsFh',
                            'username'  => $data["email"],
                            'password'  => $data["password"],
                            'scope'     => 'client_hiber'
                        ];

                        $proxy = Request::create('/oauth/token','post', $params);
                        $response = $app->dispatch($proxy);
                        $json = (array) json_decode($response->getContent());
                        $json['id'] = $checkUser->id;
                        $json['email'] = $checkUser->email;
                        $json['role_id'] = $checkUser->role->rf_role_id;
                        DeviceToken::where(['user_id' => $checkUser->id, 'role_id' => '2',])->delete();
                        DB::statement("ALTER TABLE device_tokens AUTO_INCREMENT = 1");

                        $resultDToken = DeviceToken::create([
                            'user_id' => $checkUser->id,
                            'role_id' => '2',
                            'token' => $data["device_token"]
                        ]);

                       return $response->setContent(json_encode($json));
                    }
                }
            }
        } catch (\Exception $e) {

            return $this->errorResponse($e->getMessage());
        }
    }

    public function loginProvider(Request $request){

        $data = $this->validate($request,[
            'email' => 'required',
            'password' => 'required',
            'device_token' => 'required'
        ]);

        try {
            global $app;
            $user_role = '1';
            $checkUser = User::where('email', $data['email'])->first();
            if($checkUser){
                if (Hash::check($data['password'], $checkUser->password)) {
                    if (($user_role) == ($checkUser->role_provider->rf_role_id)) {

                        $params = [
                            'grant_type'=>'password',
                            'client_id' => '1',
                            'client_secret'=> 'R1lAPTRrfvY102gLzVC1TU2hCq2gqfOUosNah4Mj',
                            'username'  => $data["email"],
                            'password'  => $data["password"],
                            'scope'     => 'droner_hiber'
                        ];

                        $proxy = Request::create('/oauth/token','post', $params);
                        $response = $app->dispatch($proxy);
                        $json = (array) json_decode($response->getContent());
                        $json['id'] = $checkUser->id;
                        $json['email'] = $checkUser->email;
                        $json['role_id'] = $checkUser->role->rf_role_id;
                        DeviceToken::where(['user_id' => $checkUser->id, 'role_id' => '1',])->delete();
                        DB::statement("ALTER TABLE device_tokens AUTO_INCREMENT = 1");

                        $resultDToken = DeviceToken::create([
                            'user_id' => $checkUser->id,
                            'role_id' => '1',
                            'token' => $data["device_token"]
                        ]);

                       return $response->setContent(json_encode($json));
                    }
                }
            }
        } catch (\Exception $e) {

            return $this->errorResponse($e->getMessage());
        }
    }
}
