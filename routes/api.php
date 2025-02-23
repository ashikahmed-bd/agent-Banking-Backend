<?php

use App\Http\Controllers\AccountController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CashController;
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

    Route::get('accounts', [AccountController::class, 'index']);
    Route::post('account/store', [AccountController::class, 'store']);
    Route::post('account/transfer', [AccountController::class, 'transfer']);
    Route::post('account/deposit', [AccountController::class, 'deposit']);
    Route::post('account/withdraw', [AccountController::class, 'withdraw']);
    Route::get('transactions', [AccountController::class, 'transactions']);


    Route::get('customers', [CustomerController::class, 'index']);
    Route::post('customer/store', [CustomerController::class, 'store']);
    Route::get('{customer}/show', [CustomerController::class, 'show']);
    Route::post('{customer}/payment', [CustomerController::class, 'payment']);
    Route::get('{customer}/report', [CustomerController::class, 'getReport']);
    Route::delete('{customer}/delete', [CustomerController::class, 'destroy']);


    Route::get('users', [UserController::class, 'index']);
    Route::post('user/store', [UserController::class, 'store']);
    Route::get('{user}/show', [UserController::class, 'show']);
    Route::put('{user}/update', [UserController::class, 'update']);
    Route::delete('{user}/delete', [UserController::class, 'destroy']);

    Route::prefix('pdf')->group(function (){
        Route::get('transactions', [PdfController::class, 'getTransactionsPrint']);
        Route::get('account/{account}/history', [PdfController::class, 'getHistory']);
        Route::get('customers', [PdfController::class, 'getCustomers']);
    });

    Route::get('reboot', [SettingsController::class, 'reboot']);
    Route::get('seed', [SettingsController::class, 'seed']);
});




