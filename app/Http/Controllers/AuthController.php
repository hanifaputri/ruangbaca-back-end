<?php

namespace App\Http\Controllers;

use App\Models\User;
use Firebase\JWT\JWT;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

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

    private function jwt($user) {
        $payload =array(
            'sub' => $user->email,
            'iss' => 'http://localhost:8000/',
            'aud' => 'http://localhost:8000/',
            'iat' => time(),
            'exp' => time() + 60 * 60,
            'role' => $user->role,
        );
        return JWT::encode($payload,env('JWT_KEY'),'HS256');
    }

    public function refreshToken(Request $request)
    {
        // Accept old token
        $oldToken = $request->input('token');

        try {
            $payload = JWT::decode($oldToken,env('JWT_KEY'),['HS256']);
            
            // Renew token expiration time
            $payload->iat = time();
            $payload->exp = time() + 60 * 60;

            $newToken = JWT::encode($payload,env('JWT_KEY'),'HS256');
            // var_dump($newToken);
            // die();

            return response()->json([
                'success'=>true,
                'access_token'=> $newToken
            ], 200);
        } catch (\Exception $e){
            return response()->json([
                'success'=>false,
                'message'=>'JWT error: '. $e->getMessage()
            ], 401);
        }
    }

    public function register(Request $request)
    {
        $name = $request->input('name');
        $email = $request->input('email');
        // dd($email);
        $password = Hash::make($request->input('password'));
        
        $role = $request->input('role') ?? 'User';
        
        // Role validation
        $val_role = Validator::make($request->all(), [
            'role'      => 'in:Admin,User'
        ]);

        // Empty field validation
        $val_required = Validator::make($request->all(), [
            'name'      => 'required',
            'email'     => 'required|email',
            'password'  => 'required',
        ]);

        if ($val_required->fails()){
            return response()->json([
                'success' => false,
                'message' => 'Field should not be empty'
            ], 400);
        }

        try {
            $user = User::where('email', $request->email)->first();

            if (!$user){
                $register = User::create([
                    'name'      => $name,
                    'email'     => $email,
                    'password'  => $password,
                    'role'      => $role
                ]);

                if($register){
                    return response()->json([
                        'success' => true,
                        'message' => 'Register successful',
                        'data' => [
                            'id' => $register->id,
                            'name' => $register->name
                        ],
                        'access_token' => $this->jwt($register)
                    ], 201);
                } else {
                    return response()->json([
                        'success' => false,
                        'message' => 'Register failed'
                    ], 400);
                }
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Email already exists'
                ], 400);
            };
        } catch(\Exception $e){
            return response()->json([
                'success'=>false,
                'message'=>$e->getMessage()
            ], 500);
        }
    }

    public function login(Request $request){
        $this->validate($request, [
            'email'     =>'required|string',
            'password'  =>'required|string'
        ]);
        try {
            $user = User::where('email', $request->email)->first();
            if (!$user){
                return response()->json([
                    'success'=>false,
                    'message'=>'User not found'
                ], 404);
            };

            if (Hash::check($request->password, $user->password)){
                return response()->json([
                    'success'=> true,
                    'message'=>'Login successful',
                    'data'=>[
                        'id' => $user->id,
                        'name' => $user->name
                    ],
                    'access_token' => $this->jwt($user)
                ],200);
            } else {
                return response()->json([
                    'success'=>false,
                    'message'=>'Credential not match'
                ], 400);
            };
        } catch(\Exception $e) {
            return response()->json([
                'success'=>false,
                'message'=>$e->getMessage()
            ], 500);
        }
    }
}
