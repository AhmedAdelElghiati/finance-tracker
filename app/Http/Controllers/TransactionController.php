<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTransactionRequest;
use App\Http\Requests\UpdateTransactionRequest;
use App\Http\Resources\TransactionResource;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TransactionController extends Controller
{
    public function index() {
        $transactions = Auth::user()->transactions()->latest()->paginate(10);

        return TransactionResource::collection($transactions)->additional(
            [
                'success' => true ,
                'message' => 'Transactions retrieved successfully'
            ]
        );
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
            'data' => new TransactionResource($transaction)
        ] , 200);
    }

    public function store(StoreTransactionRequest $request) {
        $validatedData = $request->validated();

        $transaction =  Auth::user()->transactions()->create($validatedData);

        return response()->json([
            'success' => true,
            'message' => 'Transaction created',
            'data' => new TransactionResource($transaction)
        ] , 201);
    }
    public function update(UpdateTransactionRequest $request , Transaction $transaction)
    {
        if(Auth::id() != $transaction->user_id) {
            return response()->json([
                'success' => false,
                'message' => 'You do not have permission to update this transaction',
            ] , 403);
        }

       $validatedData = $request->validated();

       $transaction->update($validatedData);

        return response()->json([
            'success' => true,
            'message' => 'Transaction updated',
            'data' => new TransactionResource($transaction)
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

    public function summary() {
        $transactions = Auth::user()->transactions();

        $totalIncome = (clone $transactions)->where('type' , 'income')->sum('amount');
        $totalExpense = (clone $transactions)->where('type' , 'expense')->sum('amount');
        $totalBalance = bcsub($totalIncome , $totalExpense , 3);

        return response()->json([
            'success' => true,
            'message' => 'Dashboard summary retrieved successfully',
            'data' => [
                'totalIncome' => $totalIncome,
                'totalExpense' => $totalExpense,
                'totalBalance' => $totalBalance,
                'currency' => 'EGP'
            ]
        ] , 200);
    }

    public function search(Request $request) {
        $query = $request->input('query');
        $transactions = Auth::user()->transactions()
            ->where(function ($q) use ($query) {
                $q->where('title', 'like', "%{$query}%")
                    ->orWhere('description', 'like', "%{$query}%");
            })
            ->paginate(10);

        return TransactionResource::collection($transactions)->additional(
            [
                'success' => true,
                'message' => 'Search results',
            ]
        );
    }
}
