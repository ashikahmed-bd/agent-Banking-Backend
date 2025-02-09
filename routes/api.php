<?php

use App\Http\Controllers\AccountController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\PdfController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

// Route group for guest user only
Route::prefix('auth')->group(function (){
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
});

// Route group for authentication user only
Route::middleware('auth:sanctum')->group(function (){

    Route::get('user', [AuthController::class, 'user']);
    Route::post('logout', [AuthController::class, 'logout']);

    Route::prefix('companies')->group(function (){
        Route::post('store', [CompanyController::class, 'store']);
    });

    Route::prefix('accounts')->group(function (){
        Route::get('all', [AccountController::class, 'index']);
        Route::post('store', [AccountController::class, 'store']);
        Route::post('{account}/deposit', [AccountController::class, 'deposit']);
        Route::post('{account}/withdraw', [AccountController::class, 'withdraw']);
        Route::get('{account}/transactions', [AccountController::class, 'getTransactions']);
        Route::get('balance', [AccountController::class, 'getBalance']);
    });

    Route::get('balances', [AccountController::class, 'getAllBalances']);
    Route::get('transactions', [AccountController::class, 'getLatestTransactions']);

    Route::prefix('customers')->group(function (){
        Route::get('all', [CustomerController::class, 'index']);
        Route::post('store', [CustomerController::class, 'store']);
        Route::get('{customer}/show', [CustomerController::class, 'show']);
        Route::post('{customer}/payment', [CustomerController::class, 'payment']);
        Route::get('{customer}/report', [CustomerController::class, 'getReport']);
        Route::get('wallet', [CustomerController::class, 'getTotalBalance']);
    });

    Route::prefix('users')->group(function (){
        Route::get('all', [UserController::class, 'index']);
        Route::post('store', [UserController::class, 'store']);
        Route::get('{user}/show', [UserController::class, 'show']);
        Route::put('{user}/update', [UserController::class, 'update']);
        Route::delete('{user}/delete', [UserController::class, 'destroy']);
    });

    Route::prefix('pdf')->group(function (){
        Route::get('transactions', [PdfController::class, 'getTransactionsPrint']);
        Route::get('account/{account}/history', [PdfController::class, 'getHistory']);
    });

});



Route::get('reboot', [SettingsController::class, 'reboot']);
Route::get('seed', [SettingsController::class, 'seed']);
