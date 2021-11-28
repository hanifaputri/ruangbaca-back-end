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

    public function getAllBooks()
    {
        $book = Book::all();

        try {
            if($book){
                return response()->json([
                    'success' => true,
                    'message' => 'Book succesfully retrieved',
                    'data' => [
                        'books' => $book
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
                'message' => 'Terjadi kesalahan pada server',
            ], 500);
        }
    }

    public function getBookById($bookId)
    {
        $book = Book::where('id', $bookId)->first();

        try {
            if($book){
                return response()->json([
                    'success' => true,
                    'message' => 'Book succesfully retrieved',
                    'data' => [
                        'book' => $book
                    ]
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
                'message' => 'Terjadi kesalahan pada server',
            ], 500);
        }
    }

    public function insert(Request $request){
        $input = $request->input();

        $val_required = Validator::make($request->all(), [
            'title'         =>'required',
            'author'        =>'required',
            'description'   =>'required',
            'synopsis'      =>'required',
            'year'          =>'required|integer',
            'stock'         =>'required|integer',
        ]);

        if ($val_required->fails()){
            return response()->json([
                'success' => false,
                'message' => 'Field should not be empty'
            ], 400);
        }

        try {
            $book = Book::create([
                'title'         => $input['title'],
                'description'   => $input['description'],
                'author'        => $input['author'],
                'year'          => $input['year'],
                'synopsis'      => $input['synopsis'],
                'stock'         => $input['stock']
            ]);

            return response()->json([
                'success'   => true,
                'message'   => 'Book successfully added',
                'data'      => [
                    'book' => $book
                ]
            ], 201);
        } catch (\Exception $e){
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan pada server',
            ], 500);
        }
    }

    public function update(Request $request, $bookId)
    {
        $book = Book::where('id', $bookId)->first();

        $val = Validator::make($request->all(), [
            'year'          =>'integer',
            'stock'         =>'integer',
        ]);

        try {
            if ($book) {
                // $book->title = $request->input('title');
                // $book->description = $request->input('description');
                // $book->author = $request->input('author');
                // $book->year = (int) $request->input('year');
                // $book->synopsis = $request->input('synopsis');
                // $book->stock = (int) $request->input('stock');

                $book->fill($request->input())->save();

                return response()->json([
                    'success' => true,
                    'message' => 'Data succesfully updated',
                    'data' => [
                        'book' => $book
                    ]
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

    public function delete(Request $request, $bookId)
    {
        $book = Book::where('id', $bookId)->first();
        
        try {
            if ($book){
                $book->delete();

                if($book->trashed()) {
                    $deletedBook = Book::onlyTrashed()->where('id', $bookId)->get();

                    return response()->json([
                        'success' => true,
                        'message' => 'Data succesfully deleted',
                        'data' => $deletedBook
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
        $bookId = $request->input('id');
        $book = Book::onlyTrashed()->where('id', $bookId);

        try {
            if ($book){
                $book->restore();
                $restoredBook = Book::where('id', $bookId)->get();

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
                'message' => 'Terjadi kesalahan pada server',
            ],$e->getStatusCode());
        }
    }

    // TODO: Create book logic
}
