<?php

namespace App\Http\Controllers;
use App\Models\Book;
use Illuminate\Http\Request;

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
                    'data' => $book
                ], 200);
            }else {
                return response()->json([
                    'success' => false,
                    'message' => 'Unable to retrieve book data',
                ], 404);
            }
        } catch (Throwable $e){
            return response()->json([
                'success' => false,
                'message' => 'Server error',
            ]);
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
                    'data' => $book
                ], 200);
            }else {
                return response()->json([
                    'success' => false,
                    'message' => 'Book not found',
                ], 404);
            }
        } catch (Throwable $e){
            return response()->json([
                'success' => false,
                'message' => 'Server error',
            ]);
        }
    }

    public function insert(Request $request){
        $input = $request->input();
        try {
            if ($input) {
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
                    'data'      => $book
                ], 200);
            } else {
                return response()->json([
                    'success'   => false,
                    'message'   => 'Please complete the required field',
                ], 404);
            }
        } catch (Throwable $e) {
            echo "Error";
        }
    }

    public function update(Request $request, $bookId)
    {
        $book = Book::where('id', $bookId)->first();

        if ($book) {
            $book->title = $request->input('title');
            $book->description = $request->input('description');
            $book->author = $request->input('author');
            $book->year = (int) $request->input('year');
            $book->synopsis = $request->input('synopsis');
            $book->stock = (int) $request->input('stock');
            $book->save();

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
    }

    public function delete(Request $request, $bookId)
    {
        $book = Book::where('id', $bookId)->first();
        
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
    }

    public function restore(Request $request)
    {
        $bookId = $request->input('id');
        $book = Book::onlyTrashed()->where('id', $bookId);

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
       
    }

    // TODO: Create book logic
}
