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

   public function index(Request $request)
    {
        //show all users
        $user = User::all();

        try {
            if ($request->auth->role == 'admin') {
                return response()->json([
                    'success' => true,
                    'message' => 'User data successfully retrieved',
                    'data' => $user
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
                'message' => $e->getMesssage()
            ], 500);
        }
    }

    public function getUserById(Request $request, $id)
    {
        // Get data user by ID
        try {
            $userRequest = $request->auth;
            // dd($request->auth);

            if ($userRequest->role == 'user') {
                // Check if user has access
                if ($userRequest->id == $id) {
                    // return user data only
                    return response()->json([
                        'success' => true,
                        'message' => 'User data successfully retrieved',
                        'data' => [
                            'user' => User::find($id)
                        ]
                    ], 200);
                } else {
                    return response()->json([
                        'success' => false,
                        'message' => 'Data restricted'
                    ], 403);
                }
            } else {
                $user = User::find($id);

                if ($user) {
                    return response()->json([
                        'success' => true,
                        'message' => 'User data sucessfully retrieved',
                        'data' => $user
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
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function update(Request $request, $id)
    {
        $user = User::find($id);
        $validated = $this->validate($request, [
            'email'     => 'email',
            'role'      => 'in:Admin,User'
        ]);

        // Update data by ID user
        try {
            if ($request->auth->id == $id) {
                $user->name = $request->input('name');
                $user->save();
                return response()->json([
                    'success' => true,
                    'message' => 'Successfully update data user',
                    'data' => $user
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
                'message' => $e->getMessage()
            ], 500);
        }
    }
    
    public function delete(Request $request, $id)
    {
        $user = User::find($id);
        
        // Delete data by ID user
        try {
            if ($request->auth->id == $id || $request->auth->role == 'admin') {
                $user->delete();
                return response()->json([
                    'success' => true,
                    'message' => 'User successfully deleted'
                ], 200);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Delete not allowed'
                ], 403);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function restore(Request $request)
    {
        $id = $request->input('id');
        $user = User::onlyTrashed()->where('id', $id);
        // dd($user->first());
        try {
            if ($user->first()){
                $user->restore();
                $restoredUser = User::where('id', $id)->get();

                return response()->json([
                    'success' => true,
                    'message' => 'User succesfully restored',
                    'data' => $restoredUser
                ], 200);
            } else {
                return response()->json([
                    'success'   => false,
                    'message'   => 'No deleted record found',
                ], 404);
            }
        } catch (\Exception $e){
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ],500);
        }
    }


}