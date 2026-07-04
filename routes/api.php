<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\TransactionController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


Route::post('/register' , [AuthController::class , 'register']);
Route::post('/login' , [AuthController::class , 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('transactions/summary' , [TransactionController::class , 'summary']);
    Route::get('transactions/search' , [TransactionController::class , 'search']);
    Route::apiResource('transactions', TransactionController::class);
});
