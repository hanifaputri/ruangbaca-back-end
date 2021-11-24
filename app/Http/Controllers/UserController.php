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
            if ($user) {
                return response()->json([
                    'success' => true,
                    'message' => 'User ditemukan',
                    'data' => [
                        'user' => $user
                    ]
                ], 200);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal mengambil data user'
                ], 400);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan pada server'
            ], 500);
        }
    }

    public function getUserById(Request $request, $idUser)
    {
        $user = null;

        /// Request user access if role == admin
        $userRequest = $request->auth;
        if ($userRequest == 'admin') {
            $user = User::find($idUser);
        } else {
            $user = User::find($userRequest->id);
        }

        // Get data user by ID
        try {
            if ($user) {
                return response()->json([
                    'success' => true,
                    'message' => 'User ditemukan',
                    'data' => [
                        'user' => $user
                    ]
                ], 200);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal mengambil data user'
                ], 200);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan pada server'
            ], 500);
        }
    }

    public function updateUser(Request $request, $idUser)
    {
        $user = User::find($idUser);

        // Update data by ID user
        try {
            if ($user) {
                $user->name = $request->input('name');
                $user->email = $request->input('email');
                $user->password = Hash::make($request->input('password'));
                $user->save();
                return response()->json([
                    'success' => true,
                    'message' => 'User berhasil diupdate'
                ], 200);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'ID User tidak ditemukan'
                ], 404);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan pada server'
            ]);
        }
    }
    
    public function destroy(Request $request, $idUser)
    {
        $user = User::find($idUser);

        // Delete data by ID user
        try {
            if ($user) {
                $user->delete();
                return response()->json([
                    'success' => true,
                    'message' => 'User berhasil dihapus'
                ], 200);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'ID User tidak ditemukan'
                ], 404);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan pada server'
            ], 500);
        }
    }
}