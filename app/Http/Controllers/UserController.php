<?php 
 
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\User;
 
class UserController extends Controller
{

    public function show(){
        /* $result = $request->json()->all(); */

        $result = User::all();

        return response()->json($result);
    }

    

    public function logins(){

        return response()->json(["Test" =>"1"]);
    }
}