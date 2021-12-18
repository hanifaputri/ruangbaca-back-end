<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\User;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TransactionController extends Controller
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
        $role = $request->auth->role;
        
        try {
            $usersId = array();
            if ($role == 'admin') {
                $usersId = Transaction::pluck('id');
            } else {
                $usersId = Transaction::where('user_id', $request->auth->id)->pluck('id');
            }
            // dd(count($usersId));

            if (count($usersId)>0){
                // Retrieve user ids in a form of array
                $data = array(); 

                // Method 1: Directly define variable
                foreach($usersId as $id) {
                    $transaction = Transaction::find($id);
                    // $user = Transaction::find($id)->user()->select(['name', 'email'])->get();
                    // $book = Transaction::find($id)->book()->select(['title', 'author'])->get();

                    $item = [
                        'id' => $id,
                        'user' => [
                            'name' => $transaction->user->name,
                            'email' => $transaction->user->email,
                        ],
                        'book' => [
                            'title' => $transaction->book->title,
                            'author' => $transaction->book->author
                        ],
                        'deadline' => $transaction->deadline,
                        'borrowed_at' => $transaction->borrowed_at,
                        'returned_at' => $transaction->returned_at,
                    ];
                    array_push($data, $item);
                }
                // die();
                return response()->json([
                    'success' => true,
                    'message' => 'Transaction succesfully retrieved',
                    'data' => [
                        'transactions' => $data
                    ]
                ], 200);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'No transaction found'
                ], 404);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan pada server: '
            ], 500);
        }
    }

    public function get(Request $request, $id)
    {
        try {
            $transaction = Transaction::find($id);
            // dd($transaction);

            if ($transaction) {
                if ($request->auth->id == $transaction->user_id || $request->auth->role == 'admin'){
                    // Method 2: Predefined the variables
                    // $user = $transaction->user()->select(['name', 'email'])->get();
                    // $book = $transaction->book()->select(['title', 'author'])->get();
    
                    return response()->json([
                        'success'   => true,
                        'message'   => 'Transaction successfully retrieved',
                        'data'      => [
                            'id' => $transaction->id,
                            'book' => [
                                'title' => $transaction->book->title,
                                'author'=> $transaction->book->author,
                                'img_url' => $transaction->book->img_url
                            ],
                            'deadline' => $transaction->deadline,
                            'borrowed_at' => $transaction->borrowed_at,
                            'returned_at' => $transaction->deadline,
                            'duration' => $transaction->duration,
                            'status' => $transaction->status
                        ],
                    ], 200);
                } else {
                    return response()->json([
                        'success' => false,
                        'message' => 'Access denied'
                    ], 403);
                }
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Transaction not found'
                ], 404);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan server: ' . $e->getMessage()
            ]);
        }
    }

    public function insert(Request $request)
    {
        // dd(date('Y-m-d H:i:s', time() + 7 * 24 * 60 * 60));
        $id = $request->input('book_id');
        $userId = $request->auth->id;

        $this->validate($request, [
            'book_id' => 'integer|required',
            'duration' => 'required'
        ]);

        // Admin should fill user_id
        if ($request->auth->role == 'admin'){
            $this->validate($request, [
                'user_id' => 'integer|required'
            ]);
            $userId = $request->input('user_id');
        } 

        try {
            $book = Book::where('id', $id)->first();
            if ($book) {
                if ($book->status == 'Tersedia'){
                    $duration = $request->input('duration');
                    
                    $transaction = Transaction::create([
                        'book_id' => $id,
                        'user_id' => $userId,
                        'duration' => $duration,
                        'borrowed_at' => date('Y-m-d H:i:s', time()),
                        'deadline' => date('Y-m-d H:i:s', time() + (($duration) * 24 * 60 * 60)),
                        'status' => 'Sedang Dipinjam'
                    ]);
    
                    $book->status = 'Tidak Tersedia';
                    $book->save();
        
                    return response()->json([
                        'success'   => true,
                        'message'   => 'Transaction successfully added',
                        'data'      => [
                            'id' => $transaction->id,
                            'book' => [
                                'title' => $transaction->book->title,
                                'author'=> $transaction->book->author,
                                'img_url' => $transaction->book->img_url
                            ],
                            'deadline' => $transaction->deadline,
                            'borrowed_at' => $transaction->borrowed_at,
                            'duration' => $transaction->duration,
                            'status' => $transaction->status
                        ],
                    ], 200);
                } else {
                    return response()->json([
                        'success'   => false,
                        'message'   => 'Book not available'
                    ], 400);
                }
            } else {
                return response()->json([
                    'success'   => false,
                    'message'   => 'Book not found'
                ], 400);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function return(Request $request, $id)
    {
        $transaction = Transaction::find($id);

        try {
            if ($transaction) {
                // Change book status
                $book = Book::where('id', $transaction->book_id)->first();
                $book->status = 'Tersedia';
                $book->save();

                // Update status & return date
                $returnedDate = strtotime(date('Y-m-d H:i:s', time()));
                $deadlineDate = strtotime($transaction->deadline);
                $interval = $deadlineDate - $returnedDate;
                // dd(($interval > 0) ? 'Selesai': 'Terlambat');
                
                $transaction->returned_at = date('Y-m-d H:i:s', time());
                $transaction->status = ($interval > 0) ? 'Selesai': 'Terlambat';
                $transaction->save();

                return response()->json([
                    'success' => true,
                    'message' => 'Book has succesfully returned',
                    'data' => [
                        'transaction' => [
                            'user' => [
                                'name' => $transaction->user->name,
                                'email' => $transaction->user->email
                            ],
                            'book' => [
                                'title' => $transaction->book->title,
                                'author'=> $transaction->book->author,
                                'img_url' => $transaction->book->img_url
                            ],
                            'status' => $transaction->status,
                            'deadline' => $transaction->deadline,
                            'borrowed_at' => $transaction->borrowed_at,
                            'returned_at' => $transaction->returned_at
                        ],
                    ],
                ], 200);
            } else {
                return response()->json([
                    'success'   => false,
                    'message'   => 'Transaction not found',
                ], 404);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan pada server: ' . $e->getMessage(),
            ], 500);
        }
    }
}
