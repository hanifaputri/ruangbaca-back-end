<?php

namespace App\Http\Controllers;

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

   public function index(Request $request)
    {
        //show all users
        $user = User::all();

        if ($user) {
            return response()->json([
                'success' => true,
                'message' => 'Users are found',
            ], 200);
        } else if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to get users'
            ], 400);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Internal server error'
            ], 500);
        }
    }

    public function getUser(Request $request, $idUser)
    {
        // Request user access if role == admin
        $userRequest = $request->auth;
        if ($userRequest->role == 'admin') {
            User::find($idUser);
        } else {
            User::find($userRequest->id);
        }

        // Get data user by ID
        if ($user) {
            if ($user->role != 'user') {
                return response()->json([
                    'success' => true,
                    'message' => 'User is found',
                    'data' => [
                        'user' => $user
                    ]
                ], 200);
            } else if ($user->role != $idUser) {
                return response()->json([
                    'success' => false,
                    'message' => 'Forbidden data request'
                ], 403);
            } else {
                return response()->json([
                    'success' => true,
                    'message' => 'User is found'
                ], 200);
            }

        // If ID user is not found
        } else if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User not found',
            ], 404);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Internal server error'
            ], 500);
        }
    }

    public function updateUser(Request $request, $idUser)
    {
        $user = User::find($idUser);

        // Update data by ID user
        if ($user) {
            if ($request->auth->id == $idUser) {
                $user->update($request->all());
                return response()->json([
                    'success' => true,
                    'message' => 'User has been updated'
                ], 200);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Forbidden data request'
                ], 403);
            }
        
        // If ID user is not found
        } else if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User update is failed'
            ], 404);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Internal server error'
            ]);
        }
    }
    
    public function destroy(Request $request, $idUser)
    {
        $user = User::find($idUser);

        // Delete data by ID user
        if ($user) {
            if ($request->auth->id == $idUser) {
                $user->delete($idUser);
                return response()->json([
                    'success' => true,
                    'message' => 'User has been deleted'
                ], 200);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Forbidden data request'
                ]);
            }
            
        // If ID user is not found
        } else if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete data user'
            ], 404);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Internal server error'
            ], 500);
        }
    }
}