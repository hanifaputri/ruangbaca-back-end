<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
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

   public function getUserAll(Request $request)
    {
        //show all users
        $user = User::all();

        try {
            if ($request->auth->role == 'admin') {
                return response()->json([
                    'success' => true,
                    'message' => 'User found',
                    'data' => [
                        'users' => $user
                    ]
                ], 200);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Data restricted'
                ], 403);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan pada server'
            ], 500);
        }
    }

    public function getUserById(Request $request, $userId)
    {

        // Get data user by ID
        try {
            $userRequest = $request->auth;

            if ($userRequest->role == 'user') {
                // Check if user has access
                if ($userRequest->id == $userId) {
                    // return user data only
                    return response()->json([
                        'success' => true,
                        'message' => 'User found',
                        'data' => [
                            'user' => User::find($userId)
                        ]
                    ], 200);
                } else {
                    return response()->json([
                        'success' => false,
                        'message' => 'Data restricted'
                    ], 403);
                }
            } else {
                $user = User::find($userId);

                if ($user) {
                    return response()->json([
                        'success' => true,
                        'message' => 'User found',
                        'data' => [
                            'user' => $user
                        ]
                    ], 200);
                } else {
                    return response()->json([
                        'success' => false,
                        'message' => 'User not found'
                    ], 404);
                }
            }     
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan pada server'
            ], 500);
        }
    }

    public function updateUser(Request $request, $userId)
    {
        $user = User::find($userId);
        $validated = $this->validate($request, [
            'email'     => 'email',
            'role'      => 'in:Admin,User'
        ]);

        // Update data by ID user
        try {
            if ($request->auth->id == $userId) {
                $user->name = $request->input('name');
                $user->save();
                return response()->json([
                    'success' => true,
                    'message' => 'Successfully update data user',
                    'data' => [
                        'user' => $user
                    ]
                ], 200);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Update not allowed'
                ], 403);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan pada server'
            ], 500);
        }
    }
    
    public function destroy(Request $request, $userId)
    {
        $user = User::find($userId);
        
        // Delete data by ID user
        try {
            if ($request->auth->id == $userId) {
                $user->delete();
                return response()->json([
                    'success' => true,
                    'message' => 'User is deleted'
                ], 200);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Forbidden data request'
                ], 403);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan pada server'
            ], 500);
        }
    }
}