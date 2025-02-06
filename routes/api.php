<?php

use App\Http\Controllers\AccountController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BusinessController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\PdfController;
use App\Http\Controllers\ProfitController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\WalletController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Route group for guest user only
Route::prefix('auth')->group(function (){
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
});

// Route group for authentication user only
Route::middleware('auth:sanctum')->group(function (){
    // user routes
    Route::get('/user', [AuthController::class, 'user']);
    Route::post('/logout', [AuthController::class, 'logout']);

    Route::get('/companies', [CompanyController::class, 'index']); // Show only user’s companies
    Route::get('/transactions', [TransactionController::class, 'index']); // Show user’s company transactions
    Route::get('/accounts', [AccountController::class, 'index']);
    Route::get('/balance', [AccountController::class, 'getBalance']);


    Route::post('account/{account}/deposit', [AccountController::class, 'deposit']);
    Route::post('account/{account}/withdraw', [AccountController::class, 'withdraw']);
    Route::get('account/{account}/history', [AccountController::class, 'getHistory']);


    Route::post('cash/deposit', [WalletController::class, 'deposit']);
    Route::post('cash/withdraw', [WalletController::class, 'withdraw']);

    Route::get('transactions', [AccountController::class, 'getTransactions']);


    Route::prefix('pdf')->group(function (){
        Route::get('transactions', [PdfController::class, 'getTransactionsPrint']);
        Route::get('account/{account}/history', [PdfController::class, 'getHistory']);
    });

    Route::get('customers', [CustomerController::class, 'index']);
    Route::post('customer/store', [CustomerController::class, 'store']);
    Route::post('customer/{customer}/payment', [CustomerController::class, 'payment']);

});
Route::get('reboot', [SettingsController::class, 'reboot']);
Route::get('seed', [SettingsController::class, 'seed']);
