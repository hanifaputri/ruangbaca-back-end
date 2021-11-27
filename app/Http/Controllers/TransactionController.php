<?php

namespace App\Http\Controllers;

use App\Models\Transaction;

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

    public function getAllTransaction()
    {
        $transaction = Transaction::join('book', 'transaction.book_id', '=', 'book.id')->join('user', 'transaction.user_id', '=', 'user.id')->get();

        try {
            if ($transaction) {
                return response()->json([
                    'success' => true,
                    'message' => 'Transaction succesfully retrieved',
                    'data' => [
                        'id' => $transaction['id'],
                        'user' => [
                            'name' => $transaction['name'],
                            'email' => $transaction['email']
                        ],
                        'book' => [
                            'title' => $transaction['title'],
                            'author' => $transaction['author']
                        ],
                        'created_at' => $transaction['created_at'],
                        'updated_at' => $transaction['updated_at']
                    ]
                ], 200);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Unable to retrieve transaction'
                ], 404);
            }
        } catch (Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Server error',
            ]);
        }
    }

    public function getTransactionId($transactionId)
    {
        $transaction = Transaction::join('book', 'transaction.book_id', '=', 'book.id')->join('user', 'transaction.user_id', '=', 'user.id')->where('id', $transactionId)->first();

        try {
            if ($transaction) {
                return response()->json([
                    'success' => true,
                    'message' => 'Transaction succesfully retrieved',
                    'data' => [
                        'id' => $transaction['id'],
                        'user' => [
                            'name' => $transaction['name'],
                            'email' => $transaction['email']
                        ],
                        'book' => [
                            'title' => $transaction['title'],
                            'author' => $transaction['author']
                        ],
                        'created_at' => $transaction['created_at'],
                        'updated_at' => $transaction['updated_at']
                    ]
                ], 200);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Unable to retrieve transaction'
                ], 404);
            }
        } catch (Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Server error',
            ]);
        }
    }

    public function insert(Request $request)
    {
        $input = $request->input();

        try {
            if ($input) {
                $transaction = Transaction::create([
                    'book_id' => $input['book_id'],
                    'user_id' => $input['user_id'],
                    'dateline' => $input['dateline'],
                    'created_at' => date('Y-m-d H:i:s')
                ]);
    
                return response()->json([
                    'success'   => true,
                    'message'   => 'Transaction successfully added',
                    'data'      => $transaction
                ], 200);
            } else {
                return response()->json([
                    'success'   => false,
                    'message'   => 'Please complete the required field',
                ], 404);
            }
        } catch (Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Server error',
            ]);
        }
    }

    public function update(Request $request, $transactionId)
    {
        $transaction = Transaction::where('id', $transactionId)->first();

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
                        'transaction' => $transaction
                    ]
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
