<?php

namespace App\Http\Controllers;

use App\Models\User;
use Firebase\JWT\JWT;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }
    // pengaturan JWT
    private function jwt($user) {
        $payload =array(
                'data' => $user,
                'iat' => time(),
                'exp' => time() + 60 * 60,
                'role' => $user->role,
        );
            return JWT::encode($payload,env('JWT_KEY'),'HS256');
    }

    public function register(Request $request)
    {
        $name = $request->input('name');
        $email = $request->input('email');
        $password = Hash::make($request->input('password'));

        $validated = $this->validate($request, [
            'name' => 'required',
            'email' => 'required|email',
            'password' => 'required',
        ]);

        try {
            $register = User::create([
                'name' => $name,
                'email' => $email,
                'password' => $password
            ]);
            if($register){
                return response()->json([
                    'success' => true,
                    'message' => 'Register Success!',
                    'data' => ['token'=>$this->jwt($register),
                ]
            ], 201);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Register Failed!'
                ], 400);
            }
        } catch(\Exception $e){
            return response()->json([
                'success'=>false,
                'message'=>$e->getMessage()
            ], 500);
        }

    }

    public function login(Request $request){
        $this->validate($request, [
            'email'=>'required|string',
            'password'=>'required|string'
        ]);
        try {
            $user = User::where('email', $request->email)->first();
            if (Hash::check($request->password,$user->password)){
                return response()->json([
                    'success'=>true,
                    'message'=>'Successfully Logged in',
                    'data'=>[
                        'token'=>$this->jwt($user)
                    ]
                ]);
            } else {
                return response()->json([
                    'success'=>false,
                    'message'=>'Wrong Password!'
                ]);
            };
        } catch(\Exception $e) {
            return response()->json([
                'success'=>false,
                'message'=>$e->getMessage()
            ], 500);
        }
    }

    public function test(){
        return response()->json([
            'success'=>true,
            'message'=>'masuk'
        ], 200);
    }
}
