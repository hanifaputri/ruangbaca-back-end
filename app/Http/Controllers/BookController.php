<?php

namespace App\Http\Controllers;
use App\Models\Book;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class BookController extends Controller
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

    public function index()
    {   
        $books = Book::all();

        try {
            if($books){
                return response()->json([
                    'success' => true,
                    'message' => 'Book succesfully retrieved',
                    'data' => 
                        [
                            'id' => $book->id,
                            'isbn' => $book->isbn,
                            'img_url' => $book->img_url,
                            'author' => $book->author,
                            'publisher' => $book->publisher->publisher,
                            'category' => $book->category->category,
                            'language' => $book->language->language
                        ]
                ], 200);
            }else {
                return response()->json([
                    'success' => false,
                    'message' => 'Book record not exist',
                ], 404);
            }
        } catch (\Exception $e){
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function get($id)
    {
        $book = Book::where('id', $id)->first();

        try {
            if($book){
                return response()->json([
                    'success' => true,
                    'message' => 'Book succesfully retrieved',
                    'data' => $book
                ], 200);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Book not found',
                ], 404);
            }
        } catch (\Exception $e){
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function insert(Request $request){
        $input = $request->input();

        $this->validate($request, [
            'isbn'          =>'required|max:15|unique:books',
            'title'         =>'required',
            'img_url'       =>'required|max:255',
            'author'        =>'required',
            'publisher_id'  =>'required|exists:publishers,id',
            'category_id'   =>'required|exists:categories,id',
            'language_id'   =>'required|exists:languages,id'
        ],['isbn.unique'   => 'Book already exist']);

        try {
            $book = Book::create([
                'isbn'          => $input['isbn'],
                'title'         => $input['title'],
                'img_url'       => $input['img_url'],
                'author'        => $input['author'],
                'publisher_id'  => $input['publisher_id'],
                'category_id'   => $input['category_id'],
                'language_id'   => $input['language_id']
            ]);

            return response()->json([
                'success'   => true,
                'message'   => 'Book successfully added',
                'data'      => $book
            ], 201);
        } catch (\Exception $e){
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function update(Request $request, $id)
    {
        $book = Book::where('id', $id)->first();

        try {
            if ($book) {
                $this->validate($request, ['isbn'=>'max:15|unique:books'],['isbn.unique'   => 'Book already exist']);

                $book->fill($request->input())->save();

                return response()->json([
                    'success' => true,
                    'message' => 'Data succesfully updated',
                    'data' => $book
                ], 200);
            } else {
                return response()->json([
                    'success'   => false,
                    'message'   => 'Book not found',
                ], 404);
            }
        } catch (\Exception $e){
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan pada server: '. $e->getMessage()
            ],500);
        }
    }

    public function delete(Request $request, $id)
    {
        $book = Book::where('id', $id)->first();
        
        try {
            if ($book){
                $book->delete();

                if($book->trashed()) {
                    return response()->json([
                        'success' => true,
                        'message' => 'Data succesfully deleted'
                    ], 200);
                } else {
                    return response()->json([
                        'success' => false,
                        'message' => 'Unable to delete file'
                    ], 404);
                }
            } else {
                return response()->json([
                    'success'   => false,
                    'message'   => 'Book not found',
                ], 404);
            }
        } catch (\Exception $e){
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan pada server',
            ], 500);
        }
    }

    public function restore(Request $request)
    {
        $id = $request->input('id');
        $book = Book::onlyTrashed()->where('id', $id);
        // dd($book->first());
        try {
            if ($book->first()){
                $book->restore();
                $restoredBook = Book::where('id', $id)->get();

                return response()->json([
                    'success' => true,
                    'message' => 'Data succesfully restored',
                    'data' => $restoredBook
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
