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
                        ]
                    ]
                ], 200);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Unable to retrieve transaction'
                ], 403);
            }
        } catch (Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan pada server',
            ], 500);
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
                       ]
                    ]
                ], 200);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Unable to retrieve transaction'
                ], 403);
            }
        } catch (Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan pada server',
            ], 500);
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
                'message' => 'Terjadi kesalahan pada server',
            ], 500);
        }
    }

    public function update(Request $request, $transactionId)
    {
        $transaction = Transaction::where('id', $transactionId)->first();

        try {
            if ($transaction) {
                $transaction->book_id = $request->input('book_id');
                $transaction->user_id = $request->input('user_id');
                $transaction->dateline = $request->input('dateline');
                $transaction->update_at = date('Y-m-d H:i:s');
                $transaction->save();

                return response()->json([
                    'success' => true,
                    'message' => 'Data succesfully updated',
                    'data' => $transaction
                ], 200);
            } else {
                return response()->json([
                    'success'   => false,
                    'message'   => 'Transaction not found',
                ], 404);
            }
        } catch (Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan pada server',
            ], 500);
        }
    }
}
