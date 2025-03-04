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

Route::scopeBindings()->group(function () {
    Route::middleware('auth:sanctum')->group(function (){

        Route::get('user', [AuthController::class, 'user']);
        Route::post('logout', [AuthController::class, 'logout']);

        Route::get('companies', [CompanyController::class, 'index']);
        Route::get('companies/default', [CompanyController::class, 'default']);

        // route for accounts
        Route::get('companies/{company}/accounts', [AccountController::class, 'index']);
        Route::post('companies/{company}/accounts/store', [AccountController::class, 'store']);
        Route::post('companies/{company}/accounts/{account}/deposit', [AccountController::class, 'deposit']);
        Route::post('companies/{company}/accounts/{account}/withdraw', [AccountController::class, 'withdraw']);
        Route::post('companies/{company}/accounts/exchange', [AccountController::class, 'exchange']);
        Route::get('companies/{company}/balance', [AccountController::class, 'getBalance']);
        Route::get('companies/{company}/transactions', [AccountController::class, 'transactions']);
        Route::get('companies/{company}/accounts/{account}/statement', [AccountController::class, 'statement']);

        Route::get('companies/{company}/income', [AccountController::class, 'income']);
        Route::get('companies/{company}/expense', [AccountController::class, 'expense']);

        // route for customers
        Route::get('companies/{company}/customers', [CustomerController::class, 'index']);
        Route::post('companies/{company}/customer/store', [CustomerController::class, 'store']);
        Route::get('companies/{company}/{customer}/show', [CustomerController::class, 'show']);
        Route::post('companies/{company}/{customer}/payment', [CustomerController::class, 'payment']);
        Route::get('companies/{company}/{customer}/report', [CustomerController::class, 'getReport']);
        Route::delete('companies/{company}/{customer}/delete', [CustomerController::class, 'destroy']);


        Route::get('companies/{company}/users', [UserController::class, 'index']);
        Route::post('companies/{company}/user/store', [UserController::class, 'store']);
        Route::get('companies/{company}/{user}/show', [UserController::class, 'show']);
        Route::put('companies/{company}/{user}/update', [UserController::class, 'update']);
        Route::delete('companies/{company}/{user}/delete', [UserController::class, 'destroy']);


        Route::post('companies/{company}/day-close', [SettingsController::class, 'closeDay']);
        Route::post('companies/{company}/day-open', [SettingsController::class, 'openDay']);

    });
});

Route::get('reboot', [SettingsController::class, 'reboot']);
Route::get('seed', [SettingsController::class, 'seed']);


