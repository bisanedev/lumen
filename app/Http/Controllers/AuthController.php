<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use DateTimeImmutable;

class AuthController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login','register']]);
    }

    public function login(Request $request)
    {
        $now = new DateTimeImmutable(); 

        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email',
            'password' => 'required|string',
            'remember' => 'required|boolean'
        ]);

        // pesan jika  validator error
        if ($validator->fails()) {
            return response()->json([
               'status' => "error",
               'message' => $validator->errors(),
            ], 400);
        }    

        $credentials = $request->only('email', 'password');

        if($request->remember == true){
            // token berumur setahun
            User::where('email', $request->email)->update(['expiredToken' => $now->modify('+1 year')->getTimestamp()]);            
            $token = Auth::setTTL(525600)->claims(['domain' => $_SERVER['SERVER_NAME']])->attempt($credentials);
        }else{
            // token berumur sehari
            User::where('email', $request->email)->update(['expiredToken' => $now->modify('+1 day')->getTimestamp()]);            
            $token = Auth::setTTL(1440)->claims(['domain' => $_SERVER['SERVER_NAME']])->attempt($credentials);
        }         

        if (!$token) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized',
            ], 401);
        }

        $user = Auth::user();
        return response()->json([
                'status' => 'success',
                'user' => $user,
                'authorisation' => [
                    'token' => $token,
                    'type' => 'bearer',
                ]
        ]);

    }


    //
}
