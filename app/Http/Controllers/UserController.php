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
        $user = User::all();

        if ($user->isEmpty()) {
            return response()->json([
                'success' => true,
                'message' => 'There are no users.',
            ], 200);
        }

        return response()->json([
            'success' => true,
            'message' => 'Users are listed.',
            'data' => [
                'user' => $user,
            ],
        ], 200);
    }

    public function show(Request $request, $idUser)
    {
        $user = User::find($idUser);

        // No data user
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User not found.',
            ], 404);
        }

        // User 'Admin' is found
        if($request->user->hasRole('admin')) {
            return response()->json([
                'success' => true,
                'message' => 'User is found.',
                'data' => [
                    'user' => $user,
                ],
            ], 200);
        }

        // Access denial
        if ($user->email != $request->user->email) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid data.',
            ], 403);
        }

        // Other role is found
        return response()->json([
            'success' => true,
            'message' => 'User is found',
            'data' => [
                'user' => $user,
            ],
        ], 200);
    }

    public function update(Request $request, $idUser)
    {
        $user = User::find($idUser);

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User not found',
            ], 404);
        }

        if ($user->email != $request->user->email) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid data.',
            ], 403);
        }

        $user->update($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Data user has been updated!',
            'data' => [
                'user' => $user,
            ],
        ], 200);
    }

    public function destroy(Request $request, $idUser)
    {
        $user = User::find($idUser);

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User not found.',
            ], 404);
        }

        $user->delete($idUser);

        return response()->json([
            'success' => true,
            'message' => 'Data user has been deleted.',
        ], 200);
    }
}