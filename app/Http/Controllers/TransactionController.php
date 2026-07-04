<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TransactionController extends Controller
{
    public function index() {
        $transactions = Auth::user()->transactions()->latest()->get();

        return response()->json([
            'success' => true,
            'message' => 'List all transactions',
            'data' => $transactions
        ] , 200);
    }

    public function store(Request $request) {
        $validatedData = $request->validate([
            'title' => ['required' , 'string' , 'min:3' , 'max:255'],
            'description' => ['required' , 'string' , 'nullable' , 'max:2000'],
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
}
