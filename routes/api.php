<?php

use App\Http\Controllers\AccountController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BusinessController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\WalletController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::prefix('auth')->group(function (){
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
});

Route::middleware('auth:sanctum')->group(function (){

    Route::get('owner', [BusinessController::class, 'getOwner']);
    Route::get('accounts', [BusinessController::class, 'getAccounts']);
    Route::get('balance', [BusinessController::class, 'getBalance']);

    Route::post('account/{account}/deposit', [AccountController::class, 'deposit']);
    Route::post('account/{account}/withdraw', [AccountController::class, 'withdraw']);

    Route::post('cash/deposit', [WalletController::class, 'deposit']);
    Route::post('cash/withdraw', [WalletController::class, 'withdraw']);

    Route::get('latest-transaction', [AccountController::class, 'latestTransaction']);

    Route::get('customers', [CustomerController::class, 'index']);
    Route::post('customer/store', [CustomerController::class, 'store']);

    Route::post('/user', [AuthController::class, 'user']);
    Route::post('/logout', [AuthController::class, 'logout']);
});
