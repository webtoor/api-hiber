<?php 
 
 namespace App\Http\Controllers;

 use Illuminate\Http\Request;
 use Illuminate\Support\Facades\Auth;

 
class LoginController extends Controller
{

    public function login(Request $request){
        /* $result = $request->json()->all(); */

        $result = $request->all();

        return response()->json($result);
    }

    

    public function logins(){

        return response()->json(["Test" =>"1"]);
    }
}