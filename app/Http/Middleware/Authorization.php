<?php

namespace App\Http\Middleware;
use Firebase\JWT\JWT;
use App\Models\User;

use Closure;

class Authorization
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next, $role=null)
    {
        $jwt = $request->header('Authorization')?? $request->header('authorization');
        if (!$jwt) {
            return response()->json([
                'success'=>false,
                'message'=>'JWT Tidak ada'
            ], 403);
        }

        $jwt = str_replace('Bearer ','',$jwt);

        $user = null;
        try {
            $user = JWT::decode($jwt,env('JWT_KEY'),['HS256']);
            // dd($user->data->id);
            // dd($user->data);
        } catch (BeforeValidException $bve) {
            return response()->json([
                'success'=>false,
                'message'=>'JWT error: ',$bve->getMessage()
            ], 401);
        } catch (ExpiredException $ee){
            return response()->json([
                'success'=>false,
                'message'=>'JWT error: ',$ee->getMessage()
            ], 401);
        } catch (SignatureInvalidException $sie){
            return response()->json([
                'success'=>false,
                'message'=>'JWT error: ',$sie->getMessage()
            ], 401);
        } catch (Exception $e){
            return response()->json([
                'success'=>false,
                'message'=>'Terjadi kesalahan server'
            ], 500);
        }   

        $id = $user->data->id;
        // dd($this->hasRole($id, $role));
        // role = null

        if ($this->isUserExist($id)){
            $request->auth = $user->data;
            // Append role to auth
            $request->auth->role = $this->getRole($id);

            if ($role) {
                if ($this->hasRole($id, $role)){
                    // var_dump($request->auth);
                    // die();
                    return $next($request);
                } else {
                    return response()->json([
                        'success' => false,
                        'message' => 'Forbiden access'
                    ], 403);
                }
            } else {
                // Accept request if no specific parameter included
                return $next($request);
            }
        } else {
            return response()->json([
                'success'=>false,
                'message'=>'User not exist'
            ], 404);
        }
    }
    private function getRole($id){
        return User::find($id)->role;
    }

    private function hasRole($id, $role){
        return User::where('id', $id)->where('role', $role)->exists();
    }
    
    private function isUserExist($id){
        return User::where('id', $id)->exists();
    }
}
