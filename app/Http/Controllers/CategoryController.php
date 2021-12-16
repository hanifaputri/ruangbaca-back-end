<?php

namespace App\Http\Controllers;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CategoryController extends Controller
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

    public function get()
    {
        $categories = Category::all();

        try {
            if($categories){
                return response()->json([
                    'success' => true,
                    'message' => 'Category succesfully retrieved',
                    'data' => $categories
                ], 200);
            }else {
                return response()->json([
                    'success' => false,
                    'message' => 'Category not exists',
                ], 404);
            }
        } catch (\Exception $e){
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
    
    public function insert(Request $request)
    { 
        $this->validate($request, ['category' =>'required|unique:categories']);

        try {
            $category = Category::create([
                'category' => $request->input('category')
            ]);

            return response()->json([
                'success'   => true,
                'message'   => 'Category successfully added',
                'data'      => $category
            ], 201);
        } catch (\Exception $e){
            return response()->json([
                'success' => false,
                'message' =>  $e->getMessage(),
            ], 500);
        }
    }

    public function update(Request $request, $id)
    {
        $category = Category::where('id', $id)->first();

        try {
            if ($category) {
                $this->validate($request, ['category' =>'required']);

                $category->category = $request->input('category');
                $category->save();

                return response()->json([
                    'success' => true,
                    'message' => 'Category succesfully updated',
                    'data' => $category
                ], 200);
            } else {
                return response()->json([
                    'success'   => false,
                    'message'   => 'Category not found',
                ], 404);
            }
        } catch (\Exception $e){
            return response()->json([
                'success' => false,
                'message' => 'Internal server error:'. $e->getMessage()
            ],500);
        }
    }

    public function delete(Request $request, $id)
    {
        $category = Category::where('id', $id)->first();
        
        try {
            if ($category){
                $category->delete();

                return response()->json([
                    'success' => true,
                    'message' => 'Category succesfully deleted'
                ], 200);
            } else {
                return response()->json([
                    'success'   => false,
                    'message'   => 'Category not found',
                ], 404);
            }
        } catch (\Exception $e){
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}
