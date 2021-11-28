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

    public function getAllTransaction(Request $request)
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
                        'created_at' => $transaction->created_at,
                        'updated_at' => $transaction->updated_at,
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

    public function getTransactionId(Request $request, $transactionId)
    {
        $transaction = Transaction::find($transactionId);
        try {
            if ($transaction) {
                if ($request->auth->id == $transaction->user_id || $request->auth->role == 'admin'){
                    // Method 2: Predefined the variables
                    // $user = $transaction->user()->select(['name', 'email'])->get();
                    // $book = $transaction->book()->select(['title', 'author'])->get();
    
                    return response()->json([
                        'success' => true,
                        'message' => 'Transaction succesfully retrieved',
                        'data' => [
                            'transaction' => [
                                'user' => [
                                    'name' => $transaction->user->name,
                                    'email' => $transaction->user->email,
                                ],
                                'book' => [
                                    'title' => $transaction->book->title,
                                    'author' => $transaction->book->author,
                                    'description' => $transaction->book->description,
                                    'synopsis' => $transaction->book->synopsis
                                ],
                                'deadline' => $transaction->deadline,
                                'created_at' => $transaction->created_at,
                                'updated_at' => $transaction->updated_at,
                            ]
                        ]
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

        $val_required = Validator::make($request->all(), [
            'book_id' => 'integer|required'
        ]);

        if ($val_required->fails()){
            return response()->json([
                'success' => false,
                'message' => 'Field should not be empty'
            ], 400);
        }

        try {
            $book = Book::where('id', $id)->first();
            if ($book) {
                if ($book->stock > 0){
                    $transaction = Transaction::create([
                        'book_id' => $id,
                        'user_id' => $request->auth->id,
                        'deadline' => date('Y-m-d H:i:s', time() + 7 * 24 * 60 * 60)
                    ]);
    
                    $book->stock -= 1;
                    // Commit update
                    $book->save();
                    $transaction->save();
        
                    return response()->json([
                        'success'   => true,
                        'message'   => 'Transaction successfully added',
                        'data'      => [
                            'transaction' => [
                                'book' => [
                                    'title' => $transaction->book->title,
                                    'author'=> $transaction->book->author
                                ],
                                'deadline' => $transaction->deadline,
                                'created_at' => $transaction->created_at,
                                'updated_at' => $transaction->updated_at
                            ],
                        ],
                    ], 201);
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
                'message' => 'Terjadi kesalahan pada server: '. $e->getMessage()
            ], 500);
        }
    }

    public function update(Request $request, $transactionId)
    {
        $transaction = Transaction::find($transactionId);

        try {
            if ($transaction) {
                $book = Book::where('id', $transaction->book_id)->first();

                // Update book stock only if it hasn't returned
                if (isset($transaction->deadline)){
                    $transaction->deadline = null;
                    $book->stock += 1;
                    // Commit update
                    $book->save();
                    $transaction->save();
                }

                // Else just return a message
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
                                'author' => $transaction->book->author,
                                'description' => $transaction->book->description,
                                'synopsis' => $transaction->book->synopsis
                            ],
                            'deadline' => $transaction->deadline,
                            'created_at' => $transaction->created_at,
                            'updated_at' => $transaction->updated_at
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
