<?php

namespace App\Http\Controllers;
use App\Models\Publisher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PublisherController extends Controller
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

    public function get(){
        $publisher = Publisher::all();
        try {
            if($publisher){
                return response()->json([
                    'success' => true,
                    'message' => 'Publisher succesfully retrieved',
                    'data' => $publisher
                ], 200);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Publisher not exists',
                ], 404);
              } 
            } catch (\Exception $e) {
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage()
                ], 500);
            }
        }

    public function insert(Request $request){
        $this->validate($request, ['publisher' =>'required|unique:publishers']);
        try {
            $publisher = Publisher::create([
                'publisher' => $request->input('publisher')
            ]);

            return response()->json([
                'success'   => true,
                'message'   => 'Publisher successfully added',
                'data'      => $publisher
            ], 201);
        } catch (\Exception $e){
            return response()->json([
                'success' => false,
                'message' =>  $e->getMessage(),
            ], 500);
        }
    }
    public function update(Request $request, $id){
        $publisher = Publisher::where('id', $id)->first();
        try {
            if ($publisher) {
                $this->validate($request, ['publisher' =>'required']);

                $publisher->publisher = $request->input('publisher');
                $publisher->save();

                return response()->json([
                    'success' => true,
                    'message' => 'Publisher succesfully updated',
                    'data' => $publisher
                ], 200);
            } else {
                return response()->json([
                    'success'   => false,
                    'message'   => 'Publisher not found',
                ], 404);
            }
        } catch (\Exception $e){
            return response()->json([
                'success' => false,
                'message' => 'Internal server error:'. $e->getMessage()
            ],500);
        }
    }

    public function delete(Request $request, $id){
        $publisher = Publisher::where('id', $id)->first();
        try {
            if ($publisher){
                $publisher->delete();

                return response()->json([
                    'success' => true,
                    'message' => 'Publisher succesfully deleted'
                ], 200);
            } else {
                return response()->json([
                    'success'   => false,
                    'message'   => 'Publisher not found',
                ], 404);
            }
        } catch (\Exception $e){
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function getPublisherById($id){
        $publisher = Publisher::where('id', $id)->first();
        try {
            if($publisher){
                return response()->json([
                    'success' => true,
                    'message' => 'Publisher succesfully retrieved',
                    'data' => $publisher
                ], 200);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Publisher not found',
                ], 404);
            }
        } catch (\Exception $e){
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan pada server',
            ], 500);
        }

    }
}

