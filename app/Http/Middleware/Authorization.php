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
        if (!$jwt)
            return response()->json([
                'success'=>false,
                'message'=>'JWT Tidak ada'
            ], 403);
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
            if ($user && $this->hasRole($role, $user)){
               $request->auth = $user->data;
               return $next($request);
               
            } else {
                return response()->json([
                    'success'=>false,
                    'message'=>'You are not allowed to access'
                ], 403);
            }
    }
    private function hasRole($role, $user){
        return User::where('id', $user->data->id)->where('role', $user->role);
        //dd($user->data->id);
    }
}
