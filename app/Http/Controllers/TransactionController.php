<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TransactionController extends Controller
{
    public function index() {
        $transactions = Auth::user()->transactions()->latest()->paginate(10);

        return response()->json([
            'success' => true,
            'message' => 'List all transactions',
            'data' => $transactions
        ] , 200);
    }

    public function show(Transaction $transaction)
    {
        if(Auth::id() != $transaction->user_id) {
            return response()->json([
                'success' => false,
                'message' => 'You do not have permission to view this transaction',
            ] , 403);
        }
        return response()->json([
            'success' => true,
            'message' => 'Transaction details',
            'data' => $transaction
        ] , 200);
    }

    public function store(Request $request) {
        $validatedData = $request->validate([
            'title' => ['required' , 'string' , 'min:3' , 'max:255'],
            'description' => ['string' , 'nullable' , 'max:2000'],
            'amount' => ['required' , 'numeric' , 'min:0.01'],
            'type' => ['required' , 'in:income,expense'],
            'date' => ['required' , 'date']
        ]);

        Auth::user()->transactions()->create($validatedData);

        return response()->json([
            'success' => true,
            'message' => 'Transaction created',
            'data' => $validatedData
        ] , 201);
    }
    public function update(Request $request , Transaction $transaction)
    {
        if(Auth::id() != $transaction->user_id) {
            return response()->json([
                'success' => false,
                'message' => 'You do not have permission to update this transaction',
            ] , 403);
        }

        $validatedData = $request->validate([
            'title' => ['required' , 'string' , 'min:3' , 'max:255'],
            'description' => ['string' , 'nullable' , 'max:2000'],
            'amount' => ['required' , 'numeric' , 'min:0.01'],
            'type' => ['required' , 'in:income,expense'],
            'date' => ['required' , 'date']
        ]);

       $transaction->update($validatedData);


        return response()->json([
            'success' => true,
            'message' => 'Transaction updated',
            'data' => $transaction
        ] , 200);
    }

    public function destroy(Transaction $transaction) {
        if(Auth::id() != $transaction->user_id) {
            return response()->json([
                'success' => false,
                'message' => 'You do not have permission to delete this transaction',
            ] , 403);
        }
        $transaction->delete();
        return response()->json([] , 204);
    }
}
